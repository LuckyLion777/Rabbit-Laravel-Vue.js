<?php

namespace App\Http\Requests;

use App\Models\Ledger\Category;

class CreateLedgerEntryRequest extends Request
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

    protected function sanitize()
    {
        $category = Category::find($this->get('category_id'));
        if (!$category || !in_array($category->special, ['breeder', 'litter'])) {
            unset($this['associated_id']);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return [
            'name' => 'required',
            'date' => 'required|date_format:' . $this->user()->getDateFormatPHP(),
            'category_id' => 'ledger_category_exists',
            'category_name' => 'required_without:category_id|min:3',
            'debit' => 'boolean',
            'amount' => 'required|numeric|min:1',
            'associated_id' => 'ledger_association',
        ];
    }
}
