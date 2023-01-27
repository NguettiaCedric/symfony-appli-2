<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf; /* est une proprieté */

    public function __construct()
    {
        $this->domPdf = new Dompdf();
 
        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Drico');

        $this->domPdf->setOptions($pdfOptions);

    }


    // La fonction qui va afficher le PDF
    public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("details.pdf", [
            'Attachement' => false
        ]);
    }


    //function pour generer du pdf
    public function generateBinaryPDF($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }

}