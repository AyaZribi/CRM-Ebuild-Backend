<?php

namespace App\Http\Controllers;

use App\Models\Devis;
//use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfController extends Controller
{
    public function generate($id)
    {
        $devis = Devis::with('operations')->findOrFail($id);

        $pdf = PDF::loadView('pdf.devis', compact('devis'));

        return $pdf->download('devis.pdf');
    }
}
