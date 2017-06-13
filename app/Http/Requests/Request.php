<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * {@inheritdoc}
     * @param string|null $guard
     * @return User|null
     */
    public function user($guard = null)
    {
        return parent::user($guard);
    }

    protected function getValidatorInstance() {
        if (method_exists($this, 'sanitize')) {
            $this->sanitize();
        }
        return parent::getValidatorInstance();
    }
}
