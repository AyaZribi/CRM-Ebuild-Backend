<?php

namespace App\Http\Controllers;

use App\Mail\FacturePdf;
use App\Models\Client;
use App\Models\Facture;
use App\Models\User;

use App\Models\Operationfacture;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;


class FactureController extends Controller
{

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operationfactures' => 'required|array|min:1',
            'operationfactures.*.nature' => 'required|string|max:255',
            'operationfactures.*.quantité' => 'required|integer|min:1',
            'operationfactures.*.montant_ht' => 'required|numeric|min:0',
            'operationfactures.*.taux_tva' => 'required|numeric|min:0|default:19',

        ]);
        $client = Client::where('email', $request->input('client_email'))->first();


        $facture = Facture::create([
            'client' => $client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'nombre_operations' => count($request['operationfactures']),
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
        $intPart = (int)$montant;
        $decPart = (int)($montant * 100) % 100;

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
            $result .= 'TND';
        } elseif ($decPart == 1) {
            $result .= 'TND et une millime';
        } else {
            $result .= 'TND et ' . $this->convertMontantToLetters($decPart) . ' millimes';
        }

        return $result;
    }

    public function generatePdf(Facture $facture, Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $facture->load('operationfactures');
        $client = Client::where('email', $facture->client_email)->first();
        $phone_number = $client->phone_number;


        // Create an instance of the PDF class
        $pdf = app(PDF::class);

        // Set the path to your logo image file
        $logo = asset('resources/images/logo.svg');

        $html = View::make('pdf.facture', compact('facture', 'phone_number',  'logo', 'pdf'))->render();

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);

        // Set the options on the PDF instance
        $pdf->setOptions(['isHTML5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->getDomPDF()->setHttpContext($contxt);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream("facture-{$facture->id}.pdf");

    }


    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $facture = Facture::findOrFail($id);

        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operationfactures' => 'required|array|min:1',
            'operationfactures.*.nature' => 'required|string|max:255',
            'operationfactures.*.quantité' => 'required|integer|min:1',
            'operationfactures.*.montant_ht' => 'required|numeric|min:0',
            'operationfactures.*.taux_tva' => 'required|numeric|min:0',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();

        $facture->update([
            'client' => $client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'nombre_operations' => count($request['operationfactures']),
            'date_creation' => now(),
        ]);

        $totalMontantHt = 0;
        $totalMontantTtc = 0;

        $facture->operationfactures()->delete();

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

        return response()->json($facture, 200);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $facture = Facture::findOrFail($id);
        $facture->operationfactures()->delete();
        $facture->delete();

        return response()->json(null, 204);
    }

    public function show($id, Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $facture = Facture::with('operationfactures')->findOrFail($id);

        return response()->json($facture, 200);
    }

    public function showall(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $facture = Facture::with('operationfactures')->get();
        return response()->json($facture, 200);
    }

   // use Illuminate\Support\Facades\Mail;

    /*  public function sendPdfToClient(Facture $facture,Request $request)
      {
          $client = Client::where('email', $facture->client_email)->first();
          $pdf = $this->generatePdf($facture,$request);

          Mail::send([], [], function($message) use ($facture, $client, $pdf) {
              $message->to($client->email)
                  ->subject("Invoice #{$facture->id}")
                  ->attachData($pdf->output(), "facture-{$facture->id}.pdf");
          });

    }*/





}
