<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreated;
use App\Models\Answer;
use App\Models\Client;
use App\Models\MediaFile;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     //Validate the incoming request
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

    public function getAllProjects(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            // If user is an admin, return all projects with personnel
            $projects = Project::with('personnel')->get();
        } elseif ($user->hasRole('client')) {
            // If user is a client, return projects associated with the client's email
            $projects = Project::where('client_email', $user->email)->get();
        } elseif ($user->hasRole('personnel')) {
            // If user is personnel, return projects assigned to the personnel
            $projects = Project::whereHas('personnel', function ($query) use ($user) {
                $query->where('email', $user->email);
            })->get();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return response()->json(['projects' => $projects], 200);
    }

    public function storeTicketsss(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'object' => 'required|string',
            'description' => 'required|string',
            'closing_date' => 'required|date',
            'status' => 'required|in:pending,inprogress,fixed',
            'priority' => 'required|in:low,high,urgent',
            'attachments' => 'array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $project = Project::findOrFail($request->input('project_id'));
        $user = $request->user();

        if ($user->email !== $project->client_email) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = new Ticket([
            'object' => $request->input('object'),
            'description' => $request->input('description'),
            'closing_date' => $request->input('closing_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
        ]);

        $project->tickets()->save($ticket);
        try {
            // Code for storing media files
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $media = new MediaFile();
                    $media->file_name = $attachment->getClientOriginalName();
                    $media->file_path = $attachment->store('attachments');

                    // Log the file path to check if it's being saved correctly
                   // Log::info('File path: ' . $media->file_path);

                    $ticket->media()->save($media);
                }
            }
        } catch (\Exception $e) {
            // Log the file path to check if it's being saved correctly
            Log::info('File path: ' . $media->file_path);
            // Log or output the exception
            dd($e->getMessage());
        }




        $adminEmail = User::where('role', 'admin')->pluck('email')->toArray();
        $personnelEmails = $project->personnel->pluck('email')->toArray();
        $emails = array_merge($adminEmail, $personnelEmails);

        Mail::to($emails)->send(new TicketCreated($ticket, $project));

        return response()->json(['message' => 'Ticket created successfully'], 201);
    }


    public function showTicket($id)
    {
        $user = auth()->user();

        // Check if user is an admin
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve the ticket with attachments
        $ticket = Ticket::with('media')->findOrFail($id);

        return response()->json(['ticket' => $ticket]);
    }

    public function getAllTickets(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            // If user is an admin, return all tickets with attachments
            $tickets = Ticket::with('media')->get();
        } elseif ($user->hasRole('client')) {
            // If user is a client, return tickets associated with the client's projects with attachments
            $tickets = Ticket::whereHas('project', function ($query) use ($user) {
                $query->where('client_email', $user->email);
            })->with('media')->get();
        } elseif ($user->hasRole('personnel')) {
            // If user is personnel, return tickets assigned to the personnel with attachments
            $tickets = Ticket::whereHas('project.personnel', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->with('media')->get();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return response()->json(['tickets' => $tickets], 200);
    }
    public function updateTicket(Request $request, $id)
    {
        $request->validate([
            'object' => 'required|string',
            'description' => 'required|string',
            'closing_date' => 'required|date',
            'status' => 'required|in:pending,inprogress,fixed',
            'priority' => 'required|in:low,high,urgent',
            'attachments' => 'array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $ticket = Ticket::findOrFail($id);
        $project = $ticket->project;
        $user = $request->user();

        if ($user->email !== $project->client_email) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->object = $request->input('object');
        $ticket->description = $request->input('description');
        $ticket->closing_date = $request->input('closing_date');
        $ticket->status = $request->input('status');
        $ticket->priority = $request->input('priority');
        $ticket->save();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $ticket->addMedia($attachment)->toMediaCollection('attachments');
            }
        }


        return response()->json(['message' => 'Ticket updated successfully'], 200);
    }
    public function deleteTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();

        if ($user->email !== $ticket->project->client_email) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the ticket's media (attachments) from the storage
        $ticket->clearMediaCollection('attachments');

        // Delete the ticket
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully'], 200);
    }



    public function answersByTicket(Request $request,$id)
    {
            $answers = Answer::whereHas('ticket', function ($query) use ($id) {
                $query->where('ticket_id', $id);
            })->with('user')->get();

            $response = [
                'answers' => []
            ];

            foreach ($answers as $answer) {
                $username = $answer->user->name;

                $response['answers'][] = [
                    'id' => $answer->id,
                    'ticket_id' => $answer->ticket_id,
                    'user_id' => $answer->user_id,
                    'username' => $username,
                    'object' => $answer->object,
                    'description' => $answer->description,
                    'file' => $answer->file,
                    'image' => $answer->image,
                    'created_at' => $answer->created_at,
                    'updated_at' => $answer->updated_at,
                ];
            }

            return response()->json($response);

    }
    public function answerTicket(Request $request, $id)
    {
        //error_log("tes");
        $ticket = Ticket::findOrFail($id);
        // Check if user is admin or personnel
       // if (auth()->user()->role !== 'admin' && !$ticket->project->personnel->contains(auth()->user())) {
           // abort(403, 'Unauthorized action.');
       // }

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
        //return redirect()->back()->with('success', 'Answer added successfully.');
        return response()->json(['message' => 'Comment created successfully'], 200);
    }




}

