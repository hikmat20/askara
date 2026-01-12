<?php defined('BASEPATH') or exit('No direct script access allowed');

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PdfService
{
    protected $mpdf;

    public function __construct($params = [])
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs      = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData          = $defaultFontConfig['fontdata'];

        $fontPath = FCPATH . 'assets/fonts';

        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [
                $fontPath
            ]),
            'fontdata' => $fontData + [

                // Calibri
                'calibri' => [
                    'R'  => 'calibri/calibri.ttf',
                    'B'  => 'calibri/calibrib.ttf',
                    'I'  => 'calibri/calibrii.ttf',
                    'BI' => 'calibri/calibriz.ttf',
                ],

                // // Times New Roman
                // 'times' => [
                //     'R'  => 'times/times.ttf',
                //     'B'  => 'times/timesbd.ttf',
                //     'I'  => 'times/timesi.ttf',
                //     'BI' => 'times/timesbi.ttf',
                // ],

                // // contoh font bebas lisensi
                // 'dejavu' => $fontData['dejavusans'],
            ],
        ];

        $this->mpdf = new Mpdf(array_merge($config, $params));
    }

    public function load()
    {
        return $this->mpdf;
    }
}
