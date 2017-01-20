<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
{
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
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'querystring.regex' => 'The query cannot contain po_creator',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('report');
        $uniqueName = isset($id) ? 'unique:reports,name,' . $id : 'unique:reports';

        return [
            'name' => [
                'required',
                'max:255',
                $uniqueName,
            ],
            'querystring' => [
                'required',
                'regex:/^((?!po_creator).)*$/s',
            ],
        ];
    }
}
