<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class CreateRabbitBreederRequest extends Request
{
    private $limit_exceeded = false;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (($limit = $this->user()->getMaxBreeders()) &&
                $this->user()->breeders()->active(true)->count() >= $limit) {
            $this->limit_exceeded = true;
            return false;
        }
        return true;
    }

    public function forbiddenResponse()
    {
        return $this->limit_exceeded ? new Response('breeders-limit-exceeded', 403) : parent::forbiddenResponse();
    }

    public function sanitize()
    {

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
            'category_name' => 'required_without:category_id|min:3',
        ];
    }
}
