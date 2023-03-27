<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.quantité' => 'required|numeric|min:0',
            'operations.*.montant_ht' => 'required|numeric|min:0',
            'operations.*.taux_tva' => 'required|numeric|min:0',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();

       /* if (!$client) {
            // If the client does not exist, create a new client
            $client = Client::create([
                'name' => $request->input('client'),
                'email' => $request->input('client_email'),
            ]);
        }*/

        $devis = Devis::create([
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'date_creation' => now(),
        ]);

        foreach ($request->input('operations') as $operationData) {
            $operation = new Operation([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $operationData['taux_tva'],
                'montant_ttc' => $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100),
            ]);

            $devis->operations()->save($operation);
        }

        return response()->json($devis, 201);
    }
    /*public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.montant_ht' => 'required|numeric|min:0',
            'operations.*.taux_tva' => 'required|numeric|min:0',
        ]);

        $devis = Devis::create([
            'client' => $request->input('client'),
            'client_email' => $request->input('client_email'),
            'date_creation' => now(),
        ]);

        foreach ($request->input('operations') as $operationData) {
            $operation = new Operation([
                'nature' => $operationData['nature'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $operationData['taux_tva'],
                'montant_ttc' => $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100),
            ]);

            $devis->operations()->save($operation);
        }

        return response()->json($devis, 201);
    }*/


    /*public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'client' => 'required|string',
            'client_email' => 'required|email',
            'operations' => 'required|array',
            'operations.*.nature' => 'required|string',
            'operations.*.montant_ht' => 'required|numeric',
            'operations.*.taux_tva' => 'required|numeric',
        ]);

        // Create the devis record
        $devis = Devis::create([
            'client' => $validatedData['client'],
            'client_email' => $validatedData['client_email'],
            'nombre_operations' => count($validatedData['operations']),
        ]);

        // Create an operation record for each item in the 'operations' array
        foreach ($validatedData['operations'] as $operationData) {
            // Calculate the montant_ttc for the operation
            $montant_ttc = $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100);

            // Create the operation record
            $operation = new Operation;
            $operation->nature = $operationData['nature'];
            $operation->montant_ht = $operationData['montant_ht'];
            $operation->taux_tva = $operationData['taux_tva'];
            $operation->montant_ttc = $montant_ttc;

            // Save the operation record to the devis
            $devis->operations()->save($operation);
        }

        // Return a success response
        return response()->json(['message' => 'Devis created successfully'], 201);
    }*/


    /*public function store($devisId, Request $request)
    {
        $devis = Devis::findOrFail($devisId);

        $operation = new Operation;
        $operation->nature = $request->input('nature');
        $operation->montant_ht = $request->input('montant_ht');
        $operation->taux_tva = $request->input('taux_tva');
        $operation->montant_ttc = $operation->montant_ht * (1 + ($operation->taux_tva / 100));

        $devis->operations()->save($operation);

        $devis->increment('nombre_operations');

        return response()->json($operation, 201);
    }*/


    public function update(Request $request, $devisId, $operationId)
    {
        $operation = Operation::findOrFail($operationId);

        $operation->nature = $request->input('nature');
        $operation->montant_ht = $request->input('montant_ht');
        $operation->montant_ttc = $request->input('montant_ttc');

        $operation->save();

        return response()->json($operation, 200);
    }

    public function destroy($devisId, $operationId)
    {
        $operation = Operation::findOrFail($operationId);

        $operation->delete();

        $devis = Devis::findOrFail($devisId);

        $devis->decrement('nombre_operations');

        return response()->json(null, 204);
    }
}
