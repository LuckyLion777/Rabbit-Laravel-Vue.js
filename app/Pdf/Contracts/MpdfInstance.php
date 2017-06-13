<?php

namespace App\Pdf\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface MpdfInstance
{
    /**
     * @param string $filename
     * @return Response
     */
    public function download($filename);

    /**
     * @return \mPDF
     */
    public function getNativeMpdf();
}
