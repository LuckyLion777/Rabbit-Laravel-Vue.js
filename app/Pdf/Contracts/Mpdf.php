<?php

namespace App\Pdf\Contracts;

interface Mpdf
{
    /**
     * @param string $html
     * @param string $format (A4-L for landscape)
     * @return MpdfInstance
     */
    public function loadHtml($html, $format = 'A4');

    /**
     * @param string $file File path
     * @param string $format (A4-L for landscape)
     * @return MpdfInstance
     */
    public function loadFile($file, $format = 'A4');

    /**
     * @param string $view
     * @param array $params
     * @param string $format (A4-L for landscape)
     * @return MpdfInstance
     */
    public function loadView($view, $params = [], $format = 'A4');
}
