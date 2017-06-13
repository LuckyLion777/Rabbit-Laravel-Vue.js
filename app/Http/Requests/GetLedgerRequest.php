<?php

namespace App\Http\Requests;

use Cache;
use Auth;
use Illuminate\Contracts\Auth\Guard;

class GetLedgerRequest extends Request
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
        $cachedOrderKey = 'user-' . Auth::id() .'-ledger-sort';
        if ($this->has('order')) {
            Cache::forever($cachedOrderKey,  $this->get('order'));
            $cachedOrder = $this->get('order');
        } else {
            $cachedOrder = Cache::get($cachedOrderKey, 'date|desc');
        }
        
        $chunks = explode('|', $cachedOrder);
        list ($order, $orderDirection) = [$chunks[0], @$chunks[1] ?: "asc"];
        if (!$orderDirection) {
            $orderDirection = 'asc';
        }
        
        $input['order'] = $order;
        $input['orderDirection'] = $orderDirection;
        $input['archived'] = $this->has('archived') ? !!$this->get('archived') : null;

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
