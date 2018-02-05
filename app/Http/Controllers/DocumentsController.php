<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentBuilder;
use App\Http\Requests\DocumentsRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DocumentsController extends Controller
{
    protected $relations = [
        'be' => 'begins with',
        'co' => 'contains',
        'nc' => 'does not contain',
        'eq' => 'equals',
        'ne' => 'not equals',
        'nu' => 'is null',
        'nn' => 'is not null',
        'gt' => 'is greater than',
        'gte' => 'is greater than or equal',
        'lt' => 'is less than',
        'lte' => 'is less than or equal',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param DocumentsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(DocumentsRequest $request)
    {
        $fields = [];
        foreach (Document::getFields() as $k) {
            $fields[$k] = $k;
        }

        $data = $request->syncWithSession();

        $docs = Document::query()
            ->fromRequest($data)
            ->paginate(100);

        if (!count($data['statements'])) {
            $data['statements'] = [
                ['key' => 'author', 'rel' => 'be', 'val' => '', 'idx' => 1]
            ];
        }

        return view('documents.index', [
            'docs' => $docs,
            'fields' => $fields,
            'relations' => $this->relations,
            'statements' => $data['statements'],
            'show' => $data['fields'],
            'sort' => $data['sort'],
            'sortDir' => $data['sortDir'],
        ]);
    }

    public function resetForm(DocumentsRequest $request)
    {
        $request->resetSession();

        return redirect()->action('DocumentsController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (strlen($id) > 15) {
            $doc = Document::where('mms_id', '=', $id)->firstOrFail();
            return redirect()->action('DocumentsController@show', ['id' => $doc->id]);
        }
        $doc = Document::where('id', '=', $id)->firstOrFail();

        return view('documents.show', [
            'doc' => $doc,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
