<?php

namespace App\Http\Requests;

class UpdateBreederCategoryRequest extends CreateBreederCategoryRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:3|breeder_category_unique:' . $this->route('categories')->id,
        ];
    }
}
