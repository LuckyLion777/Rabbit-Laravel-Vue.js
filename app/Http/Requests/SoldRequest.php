<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class SoldRequest extends Request
{
    private $limit_exceeded = false;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $limit = $this->get('sold')
            ? $this->user()->getMaxArchivedBreeders()
            : $this->user()->getMaxBreeders();
        if (isset($limit) && $this->user()->breeders()->active(!$this->get('sold'))->count() >= $limit) {
            $this->limit_exceeded = true;
            return false;
        }

        return true;
    }

    public function forbiddenResponse()
    {
        if ($this->limit_exceeded) {
            return new Response('breeders-limit-exceeded', 403);
        }
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
