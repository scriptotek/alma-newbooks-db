<?php

namespace App\Http\Requests;

use App\Document;
use Illuminate\Foundation\Http\FormRequest;

class DocumentsRequest extends FormRequest
{

    /**
     * The controller action to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectAction = 'DocumentsController@index';

    protected $maxStatements = 10;
    public $defaultSortField = Document::RECEIVING_OR_ACTIVATION_DATE;
    public $defaultSortDirection = 'desc';
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
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    protected function withValidator($validator)
    {
        $validator->after(function($validator) {
            foreach ($this->getStatements() as $idx => $stmt) {
                if (!in_array($stmt['key'], Document::getFields())) {
                    $validator->errors()->add("k$idx", 'Unknown field requested');
                }
            }
        });
    }

    public function getRelations()
    {
        return $this->relations;

    }

    public function getShowFields()
    {
        sort($this->defaultFields);
        return $this->get('show') ?: $this->defaultFields;
    }

    protected function addStatement($builder, $key, $rel, $val)
    {
        if ($val) {
            switch ($rel) {
                case 'be':
                    $builder->where($key, 'ILIKE', $val . '%');
                    break;
                case 'co':
                    $builder->where($key, 'ILIKE', '%' . $val . '%');
                    break;
                case 'nc':
                    $builder->where($key, 'NOT ILIKE', '%' . $val . '%');
                    break;
                case 'eq':
                    $builder->where($key, '=', $val);
                    break;
                case 'ne':
                    $builder->where($key, '!=', $val);
                    break;
                case 'gt':
                    $builder->where($key, '>', $val);
                    break;
                case 'lt':
                    $builder->where($key, '<', $val);
                    break;
                case 'gte':
                    $builder->where($key, '>=', $val);
                    break;
                case 'lte':
                    $builder->where($key, '<=', $val);
                    break;
            }
        } else {
            switch ($rel) {
                case 'nu':
                    $builder->whereNull($key);
                    break;
                case 'nn':
                    $builder->whereNotNull($key);
                    break;
            }
        }
    }

    public function getStatements()
    {
        $statements = [];

        foreach (range(1, $this->maxStatements) as $i) {
            if ($this->has("k$i") && $this->has("r$i")) {
                $statements[$i] = [
                    'key' => $this->get("k$i"),
                    'rel' => $this->get("r$i"),
                    'val' => $this->get("v$i"),
                ];
            }
        }

        return $statements;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryBuilder()
    {
        $builder = Document::query();

        $sortField = $this->get('sort', $this->defaultSortField);
        $sortDir = $this->get('sortDir', $this->defaultSortDirection);
        $builder->orderBy($sortField, $sortDir)
            ->whereNotNull($sortField);

        foreach ($this->getStatements() as $stmt) {
            $this->addStatement($builder, $stmt['key'], $stmt['rel'], $stmt['val']);
        }

        return $builder;
    }
}
