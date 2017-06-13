<?php

namespace App\Http\Requests;

class UpdateRabbitBreederRequest extends Request
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
        return [
            'name'=>'required',
            'aquired'=>'date_format:' . $this->user()->getDateFormatPHP(),
            'category_id' => 'breeder_category_exists',
            // 'category_name' => 'required_without:category_id|min:3',
        ];
    }
}
