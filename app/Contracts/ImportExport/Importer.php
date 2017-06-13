<?php

namespace App\Contracts\ImportExport;

use App\Models\RabbitBreeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface Importer
{
    /**
     * @param UploadedFile $file
     * @return RabbitBreeder[]|Collection
     */
    public function parseBreeders(UploadedFile $file);
}
