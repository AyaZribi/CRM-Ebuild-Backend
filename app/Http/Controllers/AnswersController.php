<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreated;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;





class AnswersController extends Controller
{
    public function destroy($id, Request $request)
    {
        $user = $request->user();
      /*  if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }*/
        $Answer = Answer::findOrFail($id);
        $Answer->delete();

        return response()->json(['message' => 'Answer deleted'], 200);
    }
      public function update(Request $request, $id)
         {
           $user = $request->user();
           /*  if (!$user->hasRole('admin')) {
                 abort(403, 'Unauthorized action.');
             }*/

             $data = $request->validate([
                 'object' => 'string|max:255',
                 'description' => 'string|max:255'
             ]);

             $Answer = Answer::find($id);
             if ($Answer) {
                 $Answer->object = $data['object'] ?? $Answer->object;
                 $Answer->description = $data['description'] ?? $Answer->description;
                 $Answer->save();
                 return response()->json(['Success' => 'Answer Updated'],200);
             } else {
                 return response()->json(['Error' => 'Answer not found'], 404);
             }
         }

}

