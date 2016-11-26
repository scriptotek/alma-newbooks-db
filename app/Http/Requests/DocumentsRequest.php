<?php

namespace App\Http\Requests;

use App\Document;
use Illuminate\Foundation\Http\FormRequest;

class DocumentsRequest extends FormRequest
{
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
        'gr' => 'is greater than',
        'le' => 'is less than',
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
        return [
            //
        ];
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

    protected function addStatement($builder, $i)
    {
        if ($this->has("k$i") && $this->has("r$i")) {

            // TODO: check if key in Document::$fields
            $key = $this->get("k$i");
            $rel = $this->get("r$i");
            $val = $this->get("v$i");

            if ($val) {
                switch ($rel) {
                    case 'be':
                        $builder->where($key, 'LIKE', $val . '%');
                        break;
                    case 'co':
                        $builder->where($key, 'LIKE', '%' . $val . '%');
                        break;
                    case 'nc':
                        $builder->where($key, 'NOT LIKE', '%' . $val . '%');
                        break;
                    case 'eq':
                        $builder->where($key, '=', $val);
                        break;
                    case 'ne':
                        $builder->where($key, '!=', $val);
                        break;
                    case 'gr':
                        $builder->where($key, '>', $val);
                        break;
                    case 'le':
                        $builder->where($key, '<', $val);
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
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryBuilder()
    {
        $builder = Document::query();

        $sortField = $this->get('sort', $this->defaultSortField);
        $sortDir = $this->get('sortDir', $this->defaultSortDirection);
        $builder->orderBy($sortField, $sortDir);

        foreach (range(1, $this->maxStatements) as $i) {
            $this->addStatement($builder, $i);
        }

        return $builder;
    }
}
