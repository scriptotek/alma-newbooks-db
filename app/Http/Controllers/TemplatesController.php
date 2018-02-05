<?php

namespace App\Http\Controllers;

use App\Document;
use App\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Functional\map;

class TemplatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['create', 'edit', 'preview', 'store', 'update', 'delete', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('templates.index', [
            'templates' => Template::withTrashed()->orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('templates.show', [
            'template' => Template::findOrFail($id),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('templates.edit', [
            'id' => null,
            'name' => '',
            'body' => '',
        ]);
    }

    /**
     * Show the form for editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $template = Template::findOrFail($id);
        return view('templates.edit', [
            'id' => $template->id,
            'name' => $template->name,
            'body' => $template->currentVersion->body,
        ]);
    }

    /**
     * Preview/validate the template.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(Request $request)
    {
        $template = new Template();
        $body = $request->get('body');

        try {
            $docs = map(Document::whereNotNull(Document::RECEIVING_OR_ACTIVATION_DATE)->take(10)->get(), function($doc) use ($template, $body) {
                return [
                    'title' => $doc->title,
                    'receiving_or_activation_date' => $doc->receiving_or_activation_date,
                    'repr' => $template->render($doc, $body),
                ];
            });
        } catch (\Twig_Error $e) {
            return response()
                ->json(['error' => $e->getMessage()], 500);
        }

        return response()
            ->json([
                'status' => 'ok',
                'docs' => $docs,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $template = Template::create([
            'name' => $request->get('name'),
        ])->addVersionAndSave($request->get('body'), \Auth::user()->id);

        return redirect()->action('TemplatesController@show', $template->id)
            ->with('status', trans('templates.saved'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        $template->name = $request->get('name');
        $template->addVersionAndSave($request->get('body'), \Auth::user()->id);

        return redirect()
            ->action('TemplatesController@show', $id)
            ->with('status', trans('templates.saved'));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $template = Template::findOrFail($id);

        return view('templates.delete', [
            'template' => $template,
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
        Template::destroy($id);

        return redirect()
            ->action('TemplatesController@index')
            ->with('status', trans('templates.deleted'));
    }
}
