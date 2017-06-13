<?php

namespace App\Contracts\ImportExport;

use App\Models\RabbitBreeder;
use Illuminate\Support\Collection;

interface Exporter
{
    /**
     * @param RabbitBreeder[]|Collection $breeders
     * @return string
     */
    public function exportBreeders($breeders);
}
