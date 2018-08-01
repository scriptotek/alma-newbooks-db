<?php

namespace App\Http\Controllers;

use App\Document;
use App\Http\Requests\CreateReportRequest;
use App\Report;
use App\Template;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class ReportsController extends Controller
{
    protected $ttl = 1440;

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['create', 'edit', 'preview', 'store', 'update', 'delete', 'destroy']]);
    }

    /**
     * Helper method, should probably be moved elsewhere.
     */
    protected function allTemplates()
    {
        $templates = [];
        foreach (Template::orderBy('name', 'asc')->get() as $template) {
            $templates[$template->id] = $template->name;
        }
        return $templates;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.index', [
            'reports' => Report::orderBy('name')->get(),
        ]);
    }

    /**
     * Display a listing of the resource based on a raw query.
     *
     * @param CreateReportRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(CreateReportRequest $request)
    {
        $report = new Report();
        $report->querystring = $request->get('querystring');

        if (empty($report->querystring)) {
            return response()
                ->json(['docs' => []]);
        }

        try {
            $docs = $report->getDocumentBuilder()->received()->getUnique([
                'limit' => intval($request->max_items),
            ]);
        } catch (QueryException $e) {
                return response()
                ->json(['error' => $e->getMessage()], 500);
        }

        return response()
            ->json(['docs' => $docs]);
    }

    public function asRss(Request $request, Report $report, $documents, $subtitle = null)
    {

        $template = Template::find($request->get('template'));
        if (is_null($template)) {
            return response('Ingen "template"-parameter ble angitt. Sjekk at du har lagt inn korrekt adresse', 400);
        }

        $feed = \Rss::feed('2.0', 'UTF-8');

        $feed->channel([
            'title'       => $report->name . (!is_null($subtitle) ? ' : ' . $subtitle : ''),
            'description' => 'Tilvekstliste',
            'link'        => $report->link,
            'ttl'         => 43200,
        ]);

        foreach ($documents as $doc) {
            $feed->item([
                'title'              => $doc->title,
                'link'               => $doc->primo_link,
                'description|cdata'  => $template->render($doc),
                'pubDate'            => $doc->{Document::RECEIVING_OR_ACTIVATION_DATE},
            ]);
        }

        return $feed;
    }

    public function asJson(Request $request, Report $report, $documents, $groups = null, $subtitle = null)
    {
        if ($request->has('template')) {
            $template = Template::find($request->get('template'));
        } else {
            $template = null;
        }

        if (is_null($groups)) {
            $json = [];
            foreach ($documents as $doc) {
                $json[] = $doc->toArrayUsingTemplate($template);
            }
        } else {
            $json = ['groups' => $groups];
            foreach ($documents as $group => $docs) {
                foreach ($docs as $doc) {
                    $json['documents'][$group][] = $doc->toArrayUsingTemplate($template);
                }
            }
        }

        return json_encode($json);
    }

    /**
     * Display the report html view.
     *
     * @param Request $request
     * @param Report $report
     * @return Response
     */
    public function show(Request $request, Report $report)
    {
        return view('reports.show', [
            'report' => $report,
            'templates' => Template::orderBy('name', 'asc')->get(),
            'viewUrl' => action('ReportsController@show', ['id' => $report->id]),
            'rssUrl' => action('ReportsController@showData', ['id' => $report->id, 'format' => 'rss']),
            'jsonUrl' => action('ReportsController@showData', ['id' => $report->id, 'format' => 'json']),
        ]);
    }

    /**
     * Get the report rss or json data. The response can be safely cached.
     *
     * @param Request $request
     * @param Response $response
     * @param Report $report
     * @param string $format
     * @return Response
     */
    public function showData(Request $request, Response $response, Report $report, $format)
    {
        // Set caching behaviour
        $response->setLastModified($report->updated_at);
        $response->setPublic();
        if ($response->isNotModified($request)) {
            return $response;
        }

        $sort = $request->get('sort', 'receiving_or_activation_date');
        $sortDir = $request->get('sortDir', 'desc');

        $limit = intval($request->get('limit', config('rss.limit')));
        $days = intval($request->get('days'));

        $builder = $report
            ->getDocumentBuilder()
            ->take($limit);

        if ($days > 0) {
            $dateLimit = Carbon::now()->subDays($days);
            $builder->where($sort, '>=', $dateLimit);
        }

        if (strtolower($request->get('received', 'true') === 'false')) {
            $builder->nonReceived();
        } else {
            $builder->received();
        }

        $builder->orderBy($sort, $sortDir)
             ->whereNotNull($sort);

        if ($format == 'rss') {
            $body = $this->asRss($request, $report, $builder->getUnique());
            return $response->setContent($body)->header('Content-Type', 'text/xml');
        }

        if (!$request->get('group_by')) {
            $body = $this->asJson($request, $report, $builder->getUnique());
            return $response->setContent($body)->header('Content-Type', 'application/json');
        }
        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        $body = $this->asJson($request, $report, $docs, $groups);
        return $response->setContent($body)->header('Content-Type', 'application/json');
    }

    /**
     * Display the report html view for a given month.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Report $report
     * @param  string $yearMonth
     * @return \Illuminate\Http\Response
     */
    public function byMonth(Request $request, Report $report, $yearMonth)
    {
        list($year, $month) = explode('-', $yearMonth);
        $year = intval($year);
        $month = intval($month);
        $currentMonth = Carbon::create($year, $month, 1);
        $subtitle = ucfirst($currentMonth->formatLocalized('%B %Y'));

        $prevMonth = $currentMonth->copy()->subMonth();
        $prevUrl = action('ReportsController@byMonth', ['report' => $report->id, 'month' => $prevMonth->format('Y-m')]);
        $prevLink = "<a href=\"$prevUrl\">« " . $prevMonth->formatLocalized('%B %Y') . "</a>";

        $nextMonth = $currentMonth->copy()->addMonth();
        $nextUrl = action('ReportsController@byMonth', ['report' => $report->id, 'month' => $nextMonth->format('Y-m')]);
        $nextLink = "<a href=\"$nextUrl\">" . $nextMonth->formatLocalized('%B %Y') . " »</a>";

        return view('reports.filtered', [
            'report' => $report,
            'header' => $subtitle,
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
            'templates' => Template::orderBy('name', 'asc')->get(),
            'viewUrl' => action('ReportsController@byMonth', ['id' => $report->id, 'month' => $yearMonth]),
            'rssUrl' => action('ReportsController@byMonthData', ['id' => $report->id, 'month' => $yearMonth, 'format' => 'rss']),
            'jsonUrl' => action('ReportsController@byMonthData', ['id' => $report->id, 'month' => $yearMonth, 'format' => 'json']),
        ]);
    }

    /**
     * Get the report rss or json data for a given month. The response can be safely cached.
     *
     * @param Request $request
     * @param Response $response
     * @param Report $report
     * @param string $month
     * @param string $format
     * @return Response
     */
    public function byMonthData(Request $request, Response $response, Report $report, $month, $format)
    {
        // Set caching behaviour
        $response->setLastModified($report->updated_at);
        $response->setPublic();
        if ($response->isNotModified($request)) {
            return $response;
        }

        list($year, $month) = explode('-', $month);
        $year = intval($year);
        $month = intval($month);
        $currentMonth = Carbon::create($year, $month, 1);
        $subtitle = ucfirst($currentMonth->formatLocalized('%B %Y'));

        $builder = $report
            ->getDocumentBuilder()
            ->fromMonth($year, $month)
            ->received()
            ->take(1000) // a "very high number"[TM]
            ;

        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        if ($format == 'rss') {
            $body = $this->asRss($request, $report, $docs[null], $subtitle);
            return $response->setContent($body)->header('Content-Type', 'text/xml');
        }

        $body = $this->asJson($request, $report, $docs, $groups);
        return $response->setContent($body)->header('Content-Type', 'application/json');
    }

    /**
     * Display the report html view for a given week.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Report $report
     * @param  string yearWeek
     * @return \Illuminate\Http\Response
     */
    public function byWeek(Request $request, Report $report, $yearWeek)
    {
        list($year, $week) = explode('-', $yearWeek);
        $year = intval($year);
        $week = intval($week);
        $currentWeek = new Carbon(sprintf('%04dW%02d', $year, $week));
        $subtitle = $currentWeek->formatLocalized('Uke %W, %Y');

        $prevWeek = $currentWeek->copy()->subWeek();
        $prevUrl = action('ReportsController@byWeek', ['report' => $report->id, 'week' => $prevWeek->format('Y-W')]);
        $prevLink = "<a href=\"$prevUrl\">« " . $prevWeek->formatLocalized('Uke %W') . "</a>";

        $nextWeek = $currentWeek->copy()->addWeek();
        $nextUrl = action('ReportsController@byWeek', ['report' => $report->id, 'week' => $nextWeek->format('Y-W')]);
        $nextLink = "<a href=\"$nextUrl\">" . $nextWeek->formatLocalized('Uke %W') . " »</a>";

        return view('reports.filtered', [
            'report' => $report,
            'header' => $subtitle,
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
            'templates' => Template::orderBy('name', 'asc')->get(),
            'viewUrl' => action('ReportsController@byWeek', ['id' => $report->id, 'week' => $yearWeek]),
            'rssUrl' => action('ReportsController@byWeekData', ['id' => $report->id, 'week' => $yearWeek, 'format' => 'rss']),
            'jsonUrl' => action('ReportsController@byWeekData', ['id' => $report->id, 'week' => $yearWeek, 'format' => 'json']),
        ]);
    }

    /**
     * Get the report rss or json data for a given week. The response can be safely cached.
     *
     * @param Request $request
     * @param Response $response
     * @param Report $report
     * @param string $week
     * @param string $format
     * @return Response
     */
    public function byWeekData(Request $request, Response $response, Report $report, $week, $format)
    {
        // Set caching behaviour
        $response->setLastModified($report->updated_at);
        $response->setPublic();
        if ($response->isNotModified($request)) {
            return $response;
        }

        list($year, $week) = explode('-', $week);
        $year = intval($year);
        $week = intval($week);
        $currentWeek = new Carbon(sprintf('%04dW%02d', $year, $week));
        $subtitle = $currentWeek->formatLocalized('Uke %W, %Y');

        $builder = $report
            ->getDocumentBuilder()
            ->fromWeek($year, $week)
            ->received()
            ->take(1000) // a "very high number"[TM]
            ;

        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        if ($format == 'rss') {
            $body = $this->asRss($request, $report, $docs[null], $subtitle);
            return $response->setContent($body)->header('Content-Type', 'text/xml');

        }

        $body = $this->asJson($request, $report, $docs, $groups);
        return $response->setContent($body)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reports.edit', [
            'report' => new Report(),
            'templates' => $this->allTemplates(),
            'fields' => Document::getFields(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);

        return view('reports.edit', [
            'report' => $report,
            'templates' => $this->allTemplates(),
            'fields' => Document::getFields(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateReportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateReportRequest $request)
    {
        $report = Report::create([
            'name' => $request->get('name'),
            'querystring' => $request->get('querystring'),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->action('ReportsController@show', $report->id)
            ->with('status', trans('reports.saved'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateReportRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateReportRequest $request, Repository $cache, $id)
    {
        $report = Report::findOrFail($id);

        $report->name = $request->get('name');
        $report->querystring = $request->get('querystring');
        $report->updated_by = Auth::user()->id;

        $report->save();

        // Flush cache
        $cache->tags('report:' . $report->id)->flush();

        return redirect()
            ->action('ReportsController@show', $id)
            ->with('status', trans('reports.saved'));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $report = Report::findOrFail($id);

        return view('reports.delete', [
            'report' => $report,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Report::destroy($id);

        return redirect()
            ->action('ReportsController@index')
            ->with('status', trans('reports.deleted'));
    }
}
