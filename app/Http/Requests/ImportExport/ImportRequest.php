<?php

namespace App\Http\Requests\ImportExport;

use App\Http\Requests\Request;

class ImportRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'import' => 'required|file',
            'ids' => 'array|exists:rabbit_breeders',
        ];
    }
}
