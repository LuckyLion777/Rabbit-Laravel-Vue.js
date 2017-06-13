<?php

namespace App\Http\Requests;

class UpdateLedgerCategoryRequest extends CreateLedgerCategoryRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:3|ledger_category_unique:' . $this->route('categories')->id,
        ];
    }
}
