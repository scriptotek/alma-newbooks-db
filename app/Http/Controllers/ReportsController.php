<?php

namespace App\Http\Controllers;

use App\Document;
use App\Report;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['create', 'edit', 'store', 'update', 'destroy']]);
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(Request $request)
    {
        $report = new Report();
        $report->days_start = $request->get('start');
        $report->days_end = $request->get('end');
        $report->querystring = $request->get('querystring');

        if (empty($report->querystring)) {
            return response()
                ->json(['docs' => []]);
        }

        try {
            $docs = $report->documents;
        } catch (QueryException $e) {
                return response()
                ->json(['error' => $e->getMessage()], 500);
        }

        return response()
            ->json(['docs' => $docs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reports.edit', [
            'report' => new Report,
            'fields' => Document::getFields(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:reports|max:255',
            'querystring' => 'required',
            'days_start' => 'required|numeric',
            'days_end' => 'required|numeric',
        ]);

        $report = Report::create([
            'name' => $request->get('name'),
            'querystring' => $request->get('querystring'),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'days_start' => $request->get('days_start'),
            'days_end' => $request->get('days_end'),
        ]);

        return redirect()->action('ReportsController@show', $report->id)
            ->with('status', trans('reports.saved'));
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
        if ($request->get('group_by') == 'dewey') {
            $docs = $report->documentsByDewey;
        } else if ($request->get('group_by') == 'week') {
            $docs = $report->documentsByWeek;
        } else {
            $docs = [null => $report->documents];
        }


        return view('reports.show', [
            'report' => $report,
            'docs' => $docs,
        ]);
    }

    /**
     * Display the specified resource as rss.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rss($id)
    {
        $report = Report::findOrFail($id);

        $feed = \Rss::feed('2.0', 'UTF-8');

        $feed->channel([
            'title'       => "Channel's title",
            'description' => "Channel's description",
            'link'        => "http://www.test.com/"
        ]);

        foreach ($report->documents as $doc) {

            $feed->item([
                'title' => $doc->title,
                'description|cdata' => $doc->mms_id,
                'link' => $doc->getPrimoLink(),
            ]);
        }

        return response($feed, 200)->header('Content-Type', 'text/xml');
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
            'fields' => Document::getFields(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:reports,name,' . $id . '|max:255',
            'querystring' => 'required',
            'days_start' => 'required|numeric',
            'days_end' => 'required|numeric',
        ]);

        $report = Report::findOrFail($id);

        $report->name = $request->get('name');
        $report->querystring = $request->get('querystring');
        $report->updated_by = Auth::user()->id;
        $report->days_start = $request->get('days_start');
        $report->days_end = $request->get('days_end');

        $report->save();

        return redirect()
            ->action('ReportsController@show', $id)
            ->with('status', Lang::get('reports.saved'));
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
            ->with('status', Lang::get('reports.deleted'));
    }
}
