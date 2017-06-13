<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MakeBreedPlanRequest extends Request
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

    public function sanitize()
    {
        if (!$this->has('type')) {
            $input['type'] = 'rabbit';
            $this->merge($input);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'doe' => 'required|integer|min:1',
            'buck' => 'required|integer|min:1',
            'date' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'doe.min'   => 'The doe field is required.',
            'buck.min' => 'The buck field is required.',
        ];
    }
}
