<?php

namespace App\Http\Requests;

use App\Document;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

class DocumentsRequest extends FormRequest
{
    public $defaultSortField = Document::RECEIVING_OR_ACTIVATION_DATE;
    public $defaultSortDirection = 'desc';
    protected $maxStatements = 10;

    protected $defaultFields = [
        Document::RECEIVING_OR_ACTIVATION_DATE,
        'title',
        'author',
        'acquisition_method',
        'material_type',
        'library_name',
        'location_name',
        'dewey_classification',
        'fund_ledger_code',
        'reporting_code',
        'permanent_call_number',
        'receiving_note',
    ];

    /**
     * The controller action to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectAction = 'DocumentsController@index';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    protected function withValidator($validator)
    {

    }

    protected function syncParamWithSession($param, $default)
    {
        if ($this->has($param)) {
            \Session::put($param, $this->get($param));
        }
        if (\Session::has($param)) {
            return \Session::get($param);
        }
        return $default;
    }

    public function syncWithSession()
    {
        sort($this->defaultFields);

        $statements = [];
        foreach (range(1, $this->maxStatements) as $i) {
            if ($this->has("k$i") && $this->has("r$i")) {
                \Session::put("k$i", $this->get("k$i"));
                \Session::put("r$i", $this->get("r$i"));
                \Session::put("v$i", $this->get("v$i"));
            }
        }
        foreach (range(1, $this->maxStatements) as $i) {
            if (\Session::has("k$i") && \Session::has("r$i") && \Session::get("v$i") !== '') {
                $statements[$i] = [
                    'key' => \Session::get("k$i"),
                    'rel' => \Session::get("r$i"),
                    'val' => \Session::get("v$i"),
                    'idx' => $i,
                ];
            }
        }

        return [
            'statements' => $statements,
            'sort' => $this->syncParamWithSession('sort', $this->defaultSortField),
            'sortDir' => $this->syncParamWithSession('sortDir', $this->defaultSortDirection),
            'fields' => $this->syncParamWithSession('show', $this->defaultFields),
        ];
    }

    public function resetSession() {
        foreach (range(1, $this->maxStatements) as $i) {
            \Session::remove("k$i");
            \Session::remove("r$i");
            \Session::remove("v$i");
        }
        \Session::remove('sort');
        \Session::remove('sortDir');
        \Session::remove('show');
    }

}
