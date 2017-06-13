<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateLitterRequest extends Request
{
    public function sanitize()
    {
        if ( !$this->has('father_id')) {
            $input['father_id'] = null;
            $this->merge($input);
        }
        if ( !$this->has('mother_id')) {
            $input['mother_id'] = null;
            $this->merge($input);
        }
        if ( $this->get('butchered_date') == 'Invalid date') {
            $input['butchered_date'] = null;
            $this->merge($input);
        }
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (count($this->route('litters')->weighs) > 0 && $this->route('litters')->kits_amount != $this->get('kits_amount'))
            return false;

        return true;
    }

    public function forbiddenResponse()
    {
        return response()->json(['error'=>['kits_amount'=>'You can change kits amount only before first weigh']]);
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
