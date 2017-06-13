<?php

namespace App\Http\Requests\ImportExport;

use App\Http\Requests\Request;

class ExportRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'in:csv',
        ];
    }
}
