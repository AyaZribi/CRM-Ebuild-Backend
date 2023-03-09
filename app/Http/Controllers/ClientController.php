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
    public function store(Request $request)
    {
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
            'is_email_password' => 'boolean', // Add validation for new boolean attribute
            // Add other validation rules for your client attributes
        ]);

        // Create the new client in the database
        $client = new Client();
        $client->name = $data['name'];
        $client->email = $data['email'];
        $client->phone_number = $request->input('phone_number');
        $client->address = $request->input('address');
        $client->user_id = $request->user()->id; // set the user_id explicitly
        $client->save();

        // Send an email to the new client with their login credentials if required
        if ($data['is_email_password']) {
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
}
