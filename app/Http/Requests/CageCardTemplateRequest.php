<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CageCardTemplateRequest
 * @package App\Http\Requests
 */
class CageCardTemplateRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'post'  => [
                'name'   => 'required|string',
                'size'   => 'required|string',
                'type'   => 'required|string',
                'fields' => 'required',
            ],
            'put' => [
                'name'   => 'required|string',
                'size'   => 'required|string',
                'type'   => 'required|string',
                'fields' => 'required',
            ]
        ];

        return $rules[strtolower($this->method())];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'fields.required' => 'You should select fields for template',
        ];
    }
}
