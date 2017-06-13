<?php

namespace App\Pdf;

use App\Pdf\Contracts\Mpdf as MpdfContract;
use App\Pdf\Contracts\MpdfInstance;

class Mpdf implements MpdfContract
{
    /**
     * {@inheritdoc}
     */
    public function loadHtml($html, $format = 'A4')
    {
        $pdf = new \mPDF('UTF-8', $format);
        $pdf->WriteHTML($html);
        return app(MpdfInstance::class, [ $pdf ]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFile($file, $format = 'A4')
    {
        return $this->loadHtml(file_get_contents($file), $format);
    }

    /**
     * {@inheritdoc}
     */
    public function loadView($view, $params = [], $format = 'A4')
    {
        return $this->loadHtml(\View::make($view, $params)->render(), $format);
    }
}
