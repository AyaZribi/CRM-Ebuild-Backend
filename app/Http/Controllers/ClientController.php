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
       // $this->middleware('auth');

        // Ensure the authenticated user has the admin role
        $user = User::with('roles')->find(Auth::id());

        if (!$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'social_reason' => 'required|string|max:255',
            'RNE' => 'required|string|max:255',
            'confirmation' => 'boolean', // Add validation for new boolean attribute
        ]);

        // Create the new client in the database
        $client = new Client();
        $client->name = $data['name'];
        $client->email = $data['email'];
        $client->phone_number = $request->input('phone_number');
        $client->address = $request->input('address');
        $client->social_reason = $request->input('social_reason');
        $client->RNE = $request->input('RNE');
        $client->user_id = $request->user()->id; // set the user_id explicitly
        $client->save();

        // Send an email to the new client with their login credentials if required
        if ($data['confirmation']) {
            // Generate a random 10 char password from below chars
            $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
            $password = substr($random, 0, 10);

            Mail::to($client->email)->send(new NewClientMail($client, $password));

            // Set the generated password for the client and save the model
            $client->password = Hash::make($password);
            $client->save();
        }

        return response()->json(['success' => true]);
    }

    public function updatec(Request $request, $id)
    {
        // Ensure the authenticated user has the admin role
        $user = User::with('roles')->find(Auth::id());

        if (!$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the request data
        $data = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:clients,email,'.$id,
            'phone_number' => 'string|max:255',
            'address' => 'string|max:255',
            'social_reason' => 'string|max:255',
            'RNE' => 'string|max:255',
        ]);

        // Find the client in the database
        $client = Client::find($id);

        // Update the client with the new data
        if ($client) {
            $client->name = $data['name'] ?? $client->name;
            $client->email = $data['email'] ?? $client->email;
            $client->phone_number = $data['phone_number'] ?? $client->phone_number;
            $client->address = $data['address'] ?? $client->address;
            $client->social_reason = $data['social_reason'] ?? $client->social_reason;
            $client->RNE = $data['RNE'] ?? $client->RNE;
            $client->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }
    public function deletec(Request $request, $id)
    {
        // Ensure the authenticated user has the admin role
        $user = User::with('roles')->find(Auth::id());

        if (!$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the client in the database
        $client = Client::find($id);

        // Delete the client
        if ($client) {
            $client->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }
    public function viewallc(Request $request)
    {
        // Ensure the authenticated user has the admin role
        $user = User::with('roles')->find(Auth::id());

        if (!$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all clients from the database
        $clients = Client::all();

        // Return the clients as JSON
        return response()->json(['clients' => $clients]);
    }

}
