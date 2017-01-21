<?php

namespace App\Http\Controllers;

use App\Document;
use App\Http\Requests\CreateReportRequest;
use App\Report;
use App\Template;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Functional\map;

class ReportsController extends Controller
{

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
            'reports' => Report::get(),
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
            $docs = $report->getDocumentBuilder()->received()->getUnique();
        } catch (QueryException $e) {
                return response()
                ->json(['error' => $e->getMessage()], 500);
        }

        return response()
            ->json(['docs' => $docs]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Report $report)
    {

        if (!$request->get('format')) {
            return view('reports.show', [
                'report' => $report,
                'templates' => Template::orderBy('name', 'asc')->get(),
            ]);
        }

        $limit = intval($request->get('limit', 30));

        $builder = $report
            ->getDocumentBuilder()
            ->take($limit);

        if (strtolower($request->get('received', 'true') == 'false')) {
            $builder->nonReceived();
        } else {
            $builder->received();
        }

        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        if ($request->get('format') == 'rss') {
            return $this->asRss($request, $report, $docs[null]);
        }

        return $this->asJson($request, $report, $docs, $groups);
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
                'link'               => $doc->getPrimoLink(),
                'description|cdata'  => $template->render($doc),
                'pubDate'            => $doc->{Document::RECEIVING_OR_ACTIVATION_DATE},
            ]);
        }

        return response($feed, 200)->header('Content-Type', 'text/xml');
    }

    public function asJson(Request $request, Report $report, $documents, $groups, $subtitle = null)
    {
        if ($request->has('template')) {
            $template = Template::find($request->get('template'));
        } else {
            $template = $report->template;
        }

        $json = ['groups' => $groups];
        foreach ($documents as $group => $docs) {
            foreach ($docs as $doc) {
                $json['documents'][$group][] = [
                    'title'              => $doc->title,
                    'link'               => $doc->getPrimoLink(),
                    'description'        => $template->render($doc),
                    'date'               => $doc->{Document::RECEIVING_OR_ACTIVATION_DATE} ? $doc->{Document::RECEIVING_OR_ACTIVATION_DATE}->toDateTimeString() : null,
                ];
            }
        }

        return response()->json($json);
    }

    /**
     * Display the specified resource for a given month.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Report  $report
     * @param  string $month
     * @return \Illuminate\Http\Response
     */
    public function byMonth(Request $request, Report $report, $month)
    {
        list($year, $month) = explode('-', $month);
        $year = intval($year);
        $month = intval($month);
        $currentMonth = Carbon::create($year, $month, 1);
        $subtitle = ucfirst($currentMonth->formatLocalized('%B %Y'));

        if (!$request->get('format')) {
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
            ]);
        }

        $template = Template::findOrFail($request->get('template'));

        $builder = $report
            ->getDocumentBuilder()
            ->fromMonth($year, $month)
            ->received()
            ;

        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        if ($request->get('format') == 'rss') {
            return $this->asRss($request, $report, $docs[null], $subtitle);
        }

        return $this->asJson($request, $report, $docs, $groups);
    }

    /**
     * Display the specified resource for a given week.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Report  $report
     * @param  string $week
     * @return \Illuminate\Http\Response
     */
    public function byWeek(Request $request, Report $report, $week)
    {
        list($year, $week) = explode('-', $week);
        $year = intval($year);
        $week = intval($week);
        $currentWeek = new Carbon(sprintf('%04dW%02d', $year, $week));
        $subtitle = $currentWeek->formatLocalized('Uke %W, %Y');

        if (!$request->get('format')) {
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
            ]);
        }

        $template = Template::findOrFail($request->get('template'));

        $builder = $report
            ->getDocumentBuilder()
            ->fromWeek($year, $week)
            ->received()
            ;

        list($docs, $groups) = $builder->getGrouped($report, $request->get('group_by'));

        if ($request->get('format') == 'rss') {
            return $this->asRss($request, $report, $docs[null], $subtitle);
        }

        return $this->asJson($request, $report, $docs, $groups);
    }

    /**
     * Display the specified resource as rss.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rss(Request $request, $id)
    {
        return redirect()->action(
            'ReportsController@show', ['id' => $id, 'format' => 'rss']
        )->withInput();
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
    public function update(CreateReportRequest $request, $id)
    {
        $report = Report::findOrFail($id);

        $report->name = $request->get('name');
        $report->querystring = $request->get('querystring');
        $report->updated_by = Auth::user()->id;

        $report->save();

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
