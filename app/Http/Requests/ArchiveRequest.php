<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class ArchiveRequest extends Request
{
    private $no_params = false;
    private $limit_exceeded = false;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( !$this->has('archived')) {
            $this->no_params = true;
            return false;
        }
        
        $limit = $this->get('archived')
            ? $this->user()->getMaxArchivedBreeders()
            : $this->user()->getMaxBreeders();
        if (isset($limit) && $this->user()->breeders()->active(!$this->get('archived'))->count() >= $limit) {
            $this->limit_exceeded = true;
            return false;
        }

        return true;
    }

    public function forbiddenResponse()
    {
        if ($this->no_params) {
            return response()->json(['error' => 'No param'], 403);
        }
        if ($this->limit_exceeded) {
            return new Response('breeders-limit-exceeded', 403);
        }
        return parent::forbiddenResponse();
    }

    public function sanitize()
    {
        $input = [];
        if ($this->get('archived') != 1) {
            $input['archived'] = 0;
        }
        $this->merge($input);
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
