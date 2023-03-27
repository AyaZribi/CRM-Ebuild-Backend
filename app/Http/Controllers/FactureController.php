<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Operation;
use App\Models\Operationfacture;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class FactureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'client_email' => 'required|string|email|max:255',
            'operationfactures' => 'required|array|min:1',
            'operationfactures.*.nature' => 'required|string|max:255',
            'operationfactures.*.quantité' => 'required|integer|min:1',
            'operationfactures.*.montant_ht' => 'required|numeric|min:0',
            'operationfactures.*.taux_tva' => 'required|numeric|min:0',
        ]);

        $facture = Facture::create([
            'client' => $request->input('client'),
            'client_email' => $request->input('client_email'),
            'date_creation' => now(),
        ]);

        $totalMontantHt = 0;
        $totalMontantTtc = 0;


        foreach ($request->input('operationfactures') as $operationData) {
            $operation = new Operationfacture([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $operationData['taux_tva'],
                'montant_ttc' => $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100),
            ]);

            $facture->operationfactures()->save($operation);

            $totalMontantHt += $operationData['montant_ht'] * $operationData['quantité'];
            $totalMontantTtc += $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100) * $operationData['quantité'];

        }

        $totalMontantTtc += 1.00; // Add 1% timbre

        // Convert the total montant to letters
        $totalMontantLetters = $this->convertMontantToLetters($totalMontantTtc);

        $facture->update([
            'total_montant_ht' => $totalMontantHt,
            'total_montant_ttc' => $totalMontantTtc,
            'total_montant_letters' => $totalMontantLetters,
        ]);

        return response()->json($facture, 201);
    }
    function convertMontantToLetters($montant)
    {
        $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        $hundreds = ['', 'cent', 'deux-cent', 'trois-cent', 'quatre-cent', 'cinq-cent', 'six-cent', 'sept-cent', 'huit-cent', 'neuf-cent'];

        $montant = number_format($montant, 2, '.', '');
        $intPart = (int) $montant;
        $decPart = (int) ($montant * 100) % 100;

        $result = '';
        if ($intPart == 0) {
            $result .= 'zéro ';
        }

        if ($intPart >= 1000) {
            $result .= $hundreds[(int)($intPart / 1000)] . ' mille ';
            $intPart %= 1000;
        }

        if ($intPart >= 100) {
            $result .= $hundreds[(int)($intPart / 100)] . ' ';
            $intPart %= 100;
        }

        if ($intPart >= 20) {
            $result .= $tens[(int)($intPart / 10)] . '-';
            $intPart %= 10;
        } elseif ($intPart >= 10) {
            $result .= $tens[$intPart - 10] . '-';
            $intPart = 0;
        }

        if ($intPart > 0) {
            $result .= $units[$intPart] . ' ';
        }

        if ($decPart == 0) {
            $result .= 'dirhams';
        } elseif ($decPart == 1) {
            $result .= 'dirham et une centime';
        } else {
            $result .= 'dirhams et ' . $this->convertMontantToLetters($decPart) . ' centimes';
        }

        return $result;
    }


    public function generatePdf(Facture $facture)
    {
        // Get the facture data
        $facture->load('operationfactures');

        // Generate the HTML view for the facture
        $html = View::make('pdf.facture', compact('facture'))->render();

        // Instantiate a new Dompdf instance
        $dompdf = new Dompdf();

        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Output the PDF to the browser or save it to a file
        return $dompdf->stream("facture-{$facture->id}.pdf");
    }



}
