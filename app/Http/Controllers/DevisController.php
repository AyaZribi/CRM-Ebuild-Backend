<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use Illuminate\Http\Request;

class DevisController extends Controller
{
    public function index()
    {
        $devis = Devis::all();
        return response()->json($devis);
    }

    public function store(Request $request)
    {
        $devis = Devis::create($request->all());
        return response()->json($devis, 201);
    }

    public function show($id)
    {
        $devis = Devis::findOrFail($id);
        return response()->json($devis);
    }

    public function update(Request $request, $id)
    {
        $devis = Devis::findOrFail($id);
        $devis->update($request->all());
        return response()->json($devis, 200);
    }

    public function destroy($id)
    {
        Devis::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
