<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreated;
use App\Models\Answer;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectCreated;
use App\Models\Personnel;
use App\Models\Project;


class ProjectController extends Controller
{

    public function store(Request $request)
 {
     $user = $request->user();
     if (!$user->hasRole('admin')) {
         abort(403, 'Unauthorized action.');
     }
    // Validate the incoming request
    $request->validate([
        'client_email' => 'required|string|email|max:255',
        'projectname' => 'required|string|unique:projects',
        'typeofproject' => 'required|string',
        'frameworks' => 'required|string',
        'database' => 'required|string',
        'description' => 'required|string',
        'datecreation' => 'required|date',
        'deadline' => 'required|date',
        'etat' => 'required|string',
        'staff' => 'required|array'
    ]);
     $client = Client::where('email', $request->input('client_email'))->first();


     // Create the project and assign staff
    $project = new Project();
     $project->projectname = $request->input('projectname');
     $project->typeofproject = $request->input('typeofproject');
    $project->projectname = $request->input('projectname');
    $project->typeofproject = $request->input('typeofproject');
    $project->frameworks = $request->input('frameworks');
    $project->database = $request->input('database');
    $project->description = $request->input('description');
    $project->datecreation = $request->input('datecreation');
    $project->deadline = $request->input('deadline');
    $project->etat = $request->input('etat');
    $project->client = $client->name;
    $project->client_email = $request['client_email'];

    $project->save();

    // Assign staff to the project
    $staff = $request->input('staff');
    foreach ($staff as $staffName) {
        $personnel = Personnel::where('Name', $staffName)->firstOrFail();
        $project->personnel()->attach($personnel);
    }

    // Send email to all staff
    $staffEmails = Personnel::whereIn('Name', $staff)->pluck('Email')->toArray();
    Mail::to($staffEmails)->send(new ProjectCreated($project));

    return response()->json(['message' => 'Project created successfully'], 201);
}
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request
        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'projectname' => 'required|string',
            'typeofproject' => 'required|string',
            'frameworks' => 'required|string',
            'database' => 'required|string',
            'description' => 'required|string',
            'datecreation' => 'required|date',
            'deadline' => 'required|date',
            'etat' => 'required|string',
            'staff' => 'required|array'
        ]);

        $project = Project::findOrFail($id);
        $client = Client::where('email', $request->input('client_email'))->firstOrFail();

        // Update the project and assign staff
        $project->projectname = $request->input('projectname');
        $project->typeofproject = $request->input('typeofproject');
        $project->frameworks = $request->input('frameworks');
        $project->database = $request->input('database');
        $project->description = $request->input('description');
        $project->datecreation = $request->input('datecreation');
        $project->deadline = $request->input('deadline');
        $project->etat = $request->input('etat');
        $project->client = $client->name;
        $project->client_email = $request['client_email'];

        $project->save();

        // Remove previous staff assigned to the project
        $project->personnel()->detach();

        // Assign new staff to the project
        $staff = $request->input('staff');
        foreach ($staff as $staffName) {
            $personnel = Personnel::where('Name', $staffName)->firstOrFail();
            $project->personnel()->attach($personnel);
        }

        // Send email to all staff
        $staffEmails = Personnel::whereIn('Name', $staff)->pluck('Email')->toArray();
        Mail::to($staffEmails)->send(new ProjectCreated($project));

        return response()->json(['message' => 'Project updated successfully'], 200);
    }



    public function destroy($id, Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully'], 200);
    }


    public function show($id,Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $project = Project::with('personnel')->findOrFail($id);

        return response()->json(['project' => $project], 200);
    }


    public function showAll(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $projects = Project::with('personnel')->get();

        return response()->json(['projects' => $projects], 200);
    }

    //GET:/api/client/projects
    public function getClientProjects(Request $request)
    {
        $client = $request->user(); // Assuming the authenticated user is the client
        $projects = Project::where('client_email', $client->email)->get();

        return response()->json(['projects' => $projects], 200);
    }
    //GET:
    public function viewAssignedProjects(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('personnel')) {
            abort(403, 'Unauthorized action.'); // Make sure only personnel can access this function
        }

        // Get the personnel's email from the authenticated user
        $personnelEmail = $user->email;

        // Get the projects assigned to the personnel
        $projects = Project::whereHas('personnel', function ($query) use ($personnelEmail) {
            $query->where('email', $personnelEmail);
        })->get();

        return response()->json(['projects' => $projects], 200);
    }
    public function storeTicket(Request $request)
    {
        // Validate the incoming request for the ticket
        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'object' => 'required|string',
            'description' => 'required|string',
            'closing_date' => 'required|date',
        ]);

        $project = Project::findOrFail($request->input('project_id'));
        $user = $request->user();

        // Check if user is the client of the project
        if ($user->email !== $project->client_email) {
            abort(403, 'Unauthorized action.');
        }

        // Create the ticket
        $ticket = new Ticket();
        $ticket->project_id = $project->id;
        $ticket->object = $request->input('object');
        $ticket->description = $request->input('description');
        $ticket->closing_date = $request->input('closing_date');
        $ticket->save();

        // Get admin and assigned personnel emails
        $adminEmail = User::where(function ($query) {
            $query->where('role', 'admin');
        })->pluck('email')->toArray();

        $personnelEmails = $project->personnel->pluck('email')->toArray();

        $emails = array_merge($adminEmail, $personnelEmails);

        // Send email to admin and assigned personnel
        Mail::to($emails)->send(new TicketCreated($ticket, $project));

        return response()->json(['message' => 'Ticket created successfully'], 201);
    }

    public function showTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        // Check if user is admin or personnel
        if (auth()->user()->role !== 'admin' && !$ticket->project->personnel->contains(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        // Load ticket with project and user relationship
        $ticket->load('project', 'user');

        return response()->json(['ticket' => $ticket]);

    }

    public function answerTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        // Check if user is admin or personnel
        if (auth()->user()->role !== 'admin' && !$ticket->project->personnel->contains(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request for the answer
        $request->validate([
            'object' => 'required|string',
            'description' => 'required|string',
            'file' => 'nullable|file',
            'image' => 'nullable|image',
        ]);

        // Create the answer
        $answer = new Answer();
        $answer->ticket_id = $ticket->id;
        $answer->user_id = auth()->user()->id;
        $answer->object = $request->input('object');
        $answer->description = $request->input('description');

        // Upload file if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('files'), $fileName);
            $answer->file = $fileName;
        }

        // Upload image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $answer->image = $imageName;
        }

        $answer->save();

        return redirect()->back()->with('success', 'Answer added successfully.');
    }




}

