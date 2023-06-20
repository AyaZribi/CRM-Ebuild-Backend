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
            'nombre_operations' => count($request['operations']),
            'note' => $request['note'],
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
            'operations.*.taux_tva' => 'required|numeric|min:0',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();

        $devis = Devis::findOrFail($id);

        $devis->update([
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'invoiced'=>$request['invoiced'],
            'note'=>$request['note'],
            'nombre_operations' => count($request['operations']),
            'date_creation' => now(),
        ]);

        $devis->operations()->delete();

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

        if ($user->hasRole('admin') || $user->hasRole('client')) {
         $devis = Devis::with('operations')->findOrFail($id);
        }else {
              abort(403, 'Unauthorized action.');
              }


        return response()->json($devis, 200);
    }
    public function showall(Request $request)
{
    if (auth()->check()) {

          $user = $request->user();

         if ($user->hasRole('admin')) {
                                   // If user is an admin, return all projects with personnel
                                   $devis = Devis::with('operations')->get();
                               } elseif ($user->hasRole('client')) {
                                   // If user is a client, return projects associated with the client's email
                                   $devis = Devis::where('client_email', $user->email)
                                   ->with('operations')
                                   ->get();
                               } else {
                                   abort(403, 'Unauthorized action.');
                               }

        return response()->json($devis, 200);
        } else {
                abort(401, 'Unauthenticated');
            }
    }


}
