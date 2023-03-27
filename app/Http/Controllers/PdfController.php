<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Devis;
//use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfController extends Controller
{
    public function generate($id)
    {
        $devis = Devis::with('operations')->findOrFail($id);

        // Retrieve the client by email
        $client = Client::where('email', $devis->client_email)->first();

        // Retrieve the phone number and RNE from the client object
        $phone_number = $client->phone_number;
        $RNE = $client->RNE;

        $pdf = PDF::loadView('pdf.devis', compact('devis', 'phone_number', 'RNE'));

        return $pdf->download('devis.pdf');
    }
    /*public function generate($id)
    {
        $devis = Devis::with('operations')->findOrFail($id);
        $devis = Devis::with( 'client')->findOrFail($id);



        $pdf = PDF::loadView('pdf.devis', compact('devis'));

        return $pdf->download('devis.pdf');
    }*/
}
