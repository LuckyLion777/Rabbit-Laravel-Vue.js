<?php

namespace App\Pdf;

use App\Pdf\Contracts\MpdfInstance as MpdfInstanceContract;
use Symfony\Component\HttpFoundation\Response;

class MpdfInstance implements MpdfInstanceContract
{
    private $mpdf;
    
    public function __construct($mpdf)
    {
        $this->mpdf = $mpdf;
    }

    public function download($filename)
    {
        $output = $this->getNativeMpdf()->Output('', 'S');
        return new Response($output, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'attachment; filename="'.$filename.'"'
        ));
    }

    public function getNativeMpdf()
    {
        return $this->mpdf;
    }
}
