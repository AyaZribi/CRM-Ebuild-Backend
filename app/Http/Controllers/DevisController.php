<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Operation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DevisController extends Controller
{
    public function store(Request $request){

        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.quantité' => 'required|numeric|min:0',
            'operations.*.montant_ht' => 'required|numeric|min:0',
            'operations.*.taux_tva' => 'numeric|min:0',
            'calculate_ttc' => 'boolean',
            'note' => 'nullable|string|max:255',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();
        $calculateTtc = $request->input('calculate_ttc', true);

        $devis = Devis::create([
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'nombre_operations' => count($request['operations']),
            'date_creation' => now(),
            'note' => $request->input('note'),

        ]);

        foreach ($request->input('operations') as $operationData) {
            $tauxTva = isset($operationData['taux_tva']) ? $operationData['taux_tva'] : 19; // Use 19 as default if taux_tva is not provided

            $operation = new Operation([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $tauxTva,
                // 'montant_ttc' => $operationData['montant_ht'] * (1 + $tauxTva / 100),
            ]);
            if (!$calculateTtc) {
                $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);
            }

            $devis->operations()->save($operation);
        }

        return response()->json($devis, 201);
    }
    public function generate($id,Request $request)
    {
        $user = $request->user();
      /*  if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }*/
        $devis = Devis::with('operations')->findOrFail($id);

        // Retrieve the client by email
        $client = Client::where('email', $devis->client_email)->first();

        // Retrieve the phone number and RNE from the client object
        $phone_number = $client->phone_number;
        $RNE = $client->RNE;

        $pdf = PDF::loadView('pdf.devis', compact('devis', 'phone_number', 'RNE'));

        return $pdf->download('devis.pdf');
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.quantité' => 'required|numeric|min:0',
            'operations.*.montant_ht' => 'required|numeric|min:0',
            'operations.*.taux_tva' => 'numeric|min:0',
            'calculate_ttc' => 'boolean',
           // 'invoiced'=>'numeric|default:0',
            'note' => 'nullable|string|max:255',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();
        $switch = $request->input('switch', false);
        $devis = Devis::findOrFail($id);
        $calculateTtc = $request->input('calculate_ttc', true);

        $devis->update([
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'invoiced'=>$request['invoiced'],
            'note'=>$request['note'],
            'nombre_operations' => count($request['operations']),
            'date_creation' => now(),
        ]);

        $devis->update($devis);

        // Delete existing operations
        $devis->operations()->delete();

        foreach ($request->input('operations') as $operationData) {
            $tauxTva = isset($operationData['taux_tva']) ? $operationData['taux_tva'] : 19; // Use 19 as default if taux_tva is not provided

            $operation = new Operation([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $tauxTva,
                // 'montant_ttc' => $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100),
                // 'montant_ttc' => $operationData['montant_ht'] * (1 + $tauxTva / 100),
                'devis_id' => $devis->id, // Assign the devis_id to the operation
            ]);
            $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);

            if (!$calculateTtc) {
                $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);
            }

            $operation->save(); // Save the operation

            $devis->operations()->save($operation);
            $devis->operations()->save($operation); // Associate the operation with the devis
        }

        return response()->json($devis, 200);
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::findOrFail($id);
        $devis->delete();

        return response()->json(null, 204);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::with('operations')->findOrFail($id);

        return response()->json($devis, 200);
    }
    public function showall(Request $request)
    {


        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::with('operations')->get();

        if ($user->hasRole('admin')) {
            $devis = Devis::with('operations')->get();
        } else {
            $devis = Devis::where('client_email', $user->email)->with('operations')->get();
        }

        return response()->json($devis, 200);
    }



}
