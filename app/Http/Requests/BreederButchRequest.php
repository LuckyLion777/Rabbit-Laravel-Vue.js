<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class BreederButchRequest extends Request
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
        $input = [];
        $input['butchered'] = $this->has('butchered') ? (int)$this->get('butchered') : 0;
        $this->merge($input);
    }

    public function forbiddenResponse()
    {
        return parent::forbiddenResponse();
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
}
