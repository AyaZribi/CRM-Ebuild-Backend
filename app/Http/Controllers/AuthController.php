<?php

namespace App\Http\Controllers;

use App\Mail\NewPersonnelMail;
use App\Models\Client;
use App\Models\personnel;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $this->credentials($request);

        if ($this->attemptLogin($credentials)) {
            $user = $request->user();
            $token = $user->createToken('Token Name')->plainTextToken;

            return response()->json([
                'token' => $token,
                'role' => $user->role,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /* public function login(Request $request)
     {
         $email = $request->input('email');
         $password = $request->input('password');

         $user = User::where('email', $email)->first();
         if (!$user) {
             $user = Personnel::where('email', $email)->first();
         }
         if (!$user) {
             $user = Client::where('email', $email)->first();
         }

         if ($user && Hash::check($password, $user->password)) {
             $token = $user->createToken('Token Name')->plainTextToken;
             return response()->json(['token' => $token], 200);
         } else {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     }*/


    protected function attemptLogin(array $credentials)
    {
        return auth()->guard('web')->attempt($credentials);
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }


    public function logout(Request $request)
    {
       if ($request->user()) {
               $request->user()->tokens()->delete();
           }

           return response()->json(['message' => 'Successfully logged out']);
    }

    public function ChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:4|max:30',
            'confirm_password' => 'required|same:new_password'
        ]);
        /*if ($validator->fails()) {
            return response()->json([
                'message' => 'validations fails',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the user is authenticated
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated',
                'errors' => ['user' => 'Unauthenticated']
            ], 401);
        }*/

        $user = $request->user();
        $credentials = $this->credentials(
            new \Illuminate\Http\Request(["email" => $user->email, "password" => $request->old_password])
        );
       /* return response()->json([
                        '1' => $credentials,
                        '2' => $this->attemptLogin($credentials)
                    ], 422);*/

      if ($this->attemptLogin($credentials)) {
      //  if (Hash::check($request->old_password, $user->password)) {
        $user->password = Hash::make($request->new_password);

           DB::beginTransaction();

           try {
               $user->save();

               if ($user->hasRole('client')) {
                   $client = Client::where('email', $user->email)->first();
                   $client->password = $request->new_password;
                   $client->save();
               } elseif ($user->hasRole('personnel')) {
                   $personnel = Personnel::where('email', $user->email)->first();
                   $personnel->password = $request->new_password;
                   $personnel->save();
               }

               DB::commit();

               return response()->json([
                   'message' => 'Password successfully updated'
               ], 200);
           } catch (\Exception $e) {
               DB::rollback();

               return response()->json([
                   'message' => 'Failed to update password',
                   'error' => $e->getMessage()
               ], 500);
           }
       } else {
           return response()->json([
               'message' => 'Old password does not match',
               'errors' => ['old_password' => 'The old password is incorrect']
           ], 422);
       }
    }



    public function store1(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:personnel',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'ID_card' => 'required|string|max:255',
            'Work_tasks' => 'required|string|max:255',
            'subcontracting' => 'boolean',
            'salary' => 'required|string|max:255',
        ]);

        // Generate random 10 char password from below chars
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890^*-+@');
        $password = substr($random, 0, 10);

        // Create the new personnel in the database
        $personnel = new Personnel();
        $personnel->name = $data['name'];
        $personnel->email = $data['email'];
        $personnel->password =$password;
        $personnel->phone_number = $request->input('phone_number');
        $personnel->address = $request->input('address');
        $personnel->ID_card = $request->input('ID_card');
        $personnel->Work_tasks = $request->input('Work_tasks');
        $personnel->subcontracting = $request->input('subcontracting');
        $personnel->salary = $request->input('salary');
        $personnel->save();

        // Create a new user with role personnel in the user table
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($password);
        $user->role = 'personnel';
        $user->save();

        // Send email to the new personnel with the generated password
        Mail::to($personnel->email)->send(new NewPersonnelMail($personnel, $password));

        return response()->json(['success' => true]);
    }



    // View all personnel
    public function index(Request $request)
    {
        $user = $request->user();
      /*  if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }*/
        $personnel = Personnel::all();
        return response()->json(['personnel' => $personnel]);
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $personnel = Personnel::find($id);

        if (!$personnel) {
            return response()->json(['error' => 'Personnel not found'], 404);
        }
        $personnel->delete();

        return response()->json(['success' => true]);
    }

        public function updatel(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get the personnel
        $personnel = Personnel::find($id);

        if (!$personnel) {
            return response()->json(['error' => 'Personnel not found'], 404);
        }

        // Validate the request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:personnel,email,'.$personnel->id,
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'ID_card' => 'required|string|max:255',
            'Work_tasks' => 'required|string|max:255',
            'subcontracting' => 'boolean' ,// add validation rule for subcontracting
            'salary' => 'required|string|max:255',

            // Add other validation rules for your personnel attributes
        ]);
               $user = User::where('email', $personnel->email)->first();
               $user->name = $data['name'];
               $user->email = $data['email'];
               $user->save();
        $oldMail=$personnel->email;
        // Update the personnel
        $personnel->name = $data['name'];
        $personnel->email = $data['email'];
        $personnel->phone_number = $request->input('phone_number');
        $personnel->address = $request->input('address');
        $personnel->ID_card = $request->input('ID_card');
        $personnel->Work_tasks = $request->input('Work_tasks');
        $personnel->subcontracting = $request->input('subcontracting'); // set the subcontracting attribute value
        $personnel->salary = $request->input('salary');
        $personnel->save();
        if($oldMail <> $personnel->email)
        {
        Mail::to($personnel->email)->send(new NewPersonnelMail($personnel, $personnel->password));
        }
        // Find the user record for the personnel




        return response()->json(['success' => true]);
    }
       public function GetInfo(Request $request)
        {
            $user = Auth::user();
            return response()->json(['user' => $user]);
        }

}
