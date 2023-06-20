<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewClientMail;
use App\Models\Client;

class ClientController extends Controller
{
    public function storeclient(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'social_reason' => 'required|string|max:255',
            'RNE' => 'required|string|max:255',
            'confirmation' => 'nullable|boolean',
        ]);
        $client = new Client();
        $client->name = $data['name'];
        $client->email = $data['email'];
        $client->phone_number = $request->input('phone_number');
        $client->address = $request->input('address');
        $client->social_reason = $request->input('social_reason');
        $client->RNE = $request->input('RNE');
        // Generate a random 10 char password from below chars
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890/+-*');
        $password = substr($random, 0, 10);
        $client->confirmation = $request->input('confirmation') ?? false;
        $client->password=$password;
        $client->save();

        // Create a new user with role client in the user table
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($password);
        $user->role = 'client';
        $user->save();

        if ($data['confirmation']) {
            Mail::to($client->email)->send(new NewClientMail($client, $password));

            $client->save();
        }

        return response()->json(['success' => true]);
    }

    public function updatec(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:clients,email,'.$id,
            'phone_number' => 'string|max:255',
            'address' => 'string|max:255',
            'social_reason' => 'string|max:255',
            'RNE' => 'string|max:255',
            'confirmation' => 'nullable|boolean',
        ]);

        $client = Client::find($id);
          $userEmail=$client->email;
          $clientConfirmation=$client->confirmation;
        if ($client) {
            $client->name = $request->input('name') ?? $client->name;
            $client->email = $request->input('email') ?? $client->email;
            $client->phone_number = $data['phone_number'] ?? $client->phone_number;
            $client->address = $data['address'] ?? $client->address;
            $client->social_reason = $data['social_reason'] ?? $client->social_reason;
            $client->RNE = $data['RNE'] ?? $client->RNE;
            $client->confirmation = $request->input('confirmation'); // Update the confirmation attribute
            if (($data['confirmation'] <> $clientConfirmation && $data['confirmation'])|| ($data['confirmation'] && $userEmail <> $client->email ) ) {
                $password =$client->password;
                Mail::to($client->email)->send(new NewClientMail($client, $password));
            }
            $client->save();
              // Find the user record for the personnel
                    $user = User::where('email', $userEmail)->first();
                    if ($user) {
                        // Update the user record for the personnel
                        $user->name =  $client->name;
                        $user->email = $client->email;
                        $user->save();
                    } else {
                        return response()->json(['error' => 'User not found'], 404);
                    }

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }



    }
    public function deletec(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $client = Client::find($id);

        if ($client) {
            $client->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }

    public function viewallc(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $clients = Client::all();
        return response()->json(['clients' => $clients]);
    }


}
