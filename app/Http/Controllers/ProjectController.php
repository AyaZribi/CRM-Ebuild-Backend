<?php

namespace App\Http\Controllers;
use App\Models\Framework;
use App\Models\TypeOfProject;
use Illuminate\Support\Facades\DB;
use App\Mail\TicketCreated;
use App\Models\Answer;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectCreated;
use App\Models\Personnel;
use App\Models\Project;


class ProjectController extends Controller
{

    public function storeproject(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request
        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'projectname' => 'required|string|unique:projects',
            'typeofproject_id' => 'required|exists:typeofprojects,id',
            'frameworks' => 'required|array',
            'frameworks.*' => 'required|exists:frameworks,id',
            'project_database' => 'required|string',
            'description' => 'required|string',
            'datecreation' => 'required|date',
            'deadline' => 'required|date',
            //'etat' => 'required|string',
            'staff' => 'required|array'
        ]);

        $client = Client::where('email', $request->input('client_email'))->firstOrFail();

        // Create the project and assign staff
        $project = new Project();
        $project->projectname = $request->input('projectname');
        $project->typeofproject_id = $request->input('typeofproject_id');
        $project->project_database = $request->input('project_database');
        $project->description = $request->input('description');
        $project->datecreation = $request->input('datecreation');
        $project->deadline = $request->input('deadline');
        $project->etat = $request->input('etat')?? "Pending";
        $project->client = $client->name;
        $project->client_email = $request->input('client_email');

        $project->save();

        // Assign staff to the project
        $staff = $request->input('staff');
        foreach ($staff as $staffName) {
            $personnel = Personnel::where('Name', $staffName)->firstOrFail();
            $project->personnel()->attach($personnel);
        }

        // Assign frameworks to the project
        $frameworks = $request->input('frameworks');
        $project->frameworks()->attach($frameworks);

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
        /*if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }*/
        $project = Project::with('personnel')->findOrFail($id);
        $project->unsetRelation('personnel');
         foreach ($project->personnel as $personnelMember) {
                               $personnelMember->makeHidden('pivot');
                           }
        return response()->json(['project' => $project], 200);
    }


    public function showAll(Request $request)
    {
        $user = $request->user();

               if ($user->hasRole('admin')) {
                   // If user is an admin, return all projects with personnel
                   $projects = Project::with('personnel')->get();
               } elseif ($user->hasRole('client')) {
                   // If user is a client, return projects associated with the client's email
                   $projects = Project::where('client_email', $user->email)->get();
            //        $projects[0]=$user->email;
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
        //    'file' => 'array',
            'files.*' => 'required|file|max:2048'
           // 'file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $project = Project::findOrFail($request->input('project_id'));
        $user = $request->user();

        // Check if user is the client of the project
        if (!$user->hasRole('admin')) {
        if ($user->email !== $project->client_email) {
            abort(403, 'Unauthorized action.');
        }
        }
        // Create the ticket
        $ticket = new Ticket();
        $ticket->project_id = $project->id;
        $ticket->object = $request->input('object');
        $ticket->description = $request->input('description');
        $ticket->closing_date = $request->input('closing_date');
        $ticket->status=$request['status'] ?? "Pending";
        $ticket->priority=$request['priority'] ?? "Low";
        $ticket->save();
         if ($request->hasFile('file')) {

                $files = $request->file('file');
              foreach ($files as $file) {

                  if ($file) {
                //  return response()->json([ $file], 201);
                  $media = new File();
                  $media->file_name = $file->getClientOriginalName();
                  $media->file_path = $file->store('public/files');

                  $ticket->files()->save($media);
              }
              }
            }
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
        $ticket = Ticket::with('project', 'user', 'files')->findOrFail($id);

            // Get the file URLs
            $fileUrls = $ticket->files->pluck('url');

            return response()->json(['ticket' => $ticket, 'file_urls' => $fileUrls]);
    }

    public function showClientTickets()
    {
        $user = auth()->user();

        // Retrieve the tickets associated with the client's projects
        $tickets = Ticket::whereHas('project', function ($query) use ($user) {
            $query->where('client_email', $user->email);
        })->get();

        return response()->json(['tickets' => $tickets]);
    }

    public function viewAssignedTickets()
    {
        $user = auth()->user();

       // Retrieve the personnel ID based on the user's email
          $personnel = Personnel::where('email', $user->email)->first();

          if (!$personnel) {
              return response()->json(['message' => 'Personnel not found'], 404);
          }

          // Retrieve all tickets assigned to the personnel
          $assignedTickets = Ticket::whereHas('project.personnel', function ($query) use ($personnel) {
              $query->where('personnel.id', $personnel->id);
          })->get();

        return response()->json(['tickets' => $assignedTickets]);
    }
    public function getAllTickets()
    {
        // Check if user is an admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve all tickets
        $tickets = Ticket::all();

        return response()->json(['tickets' => $tickets]);
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
    public function deleteTicketAndAnswers($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json(['message' => 'Ticket not found'], 404);
            }

            $ticket->delete();

            Answer::where('ticket_id', $id)->delete();

            DB::commit();

            return response()->json(['message' => 'Ticket and related answers deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to delete ticket and related answers'], 500);
        }
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
         public function updateTicket(Request $request, $id)
                 {
                  /* $user = $request->user();
                     if (!$user->hasRole('admin')) {
                         abort(403, 'Unauthorized action.');
                     }
                  */

                    $request->validate([
                        'object' => 'required|string',
                        'description' => 'required|string',
                        'status' => 'string',

                    ]);
                     $ticket = Ticket::find($id);
                           $ticket->object = $request->input('object');
                           $ticket->description = $request->input('description');
                           $ticket->status=$request['status'] ?? $ticket->status;
                           $ticket->priority=$request['priority'] ?? $ticket->priority;
                           /* if ($request->hasFile('files')) {
                                $uploadedFiles = $request->file('files');
                                foreach ($uploadedFiles as $uploadedFile) {
                                    if ($uploadedFile) {
                                        $media = new File();
                                        $media->file_name = $uploadedFile->getClientOriginalName();
                                        $media->file_path = $uploadedFile->store('files');
                                        $ticket->files()->save($media);
                                    }
                                }
                            }*/
                           $ticket->save();
                         return response()->json(['Success' => 'Ticket Updated'],200);

                 }
    public function getStatusStatistics()
    {
       $user = auth()->user();

            if ($user->hasRole('admin')) {
                $statistics = Ticket::groupBy('status')
                    ->select('status', \DB::raw('count(*) as count'))
                    ->get()
                    ->pluck('count', 'status')
                    ->toArray();
            }elseif ($user->hasRole('client')) {
                  $statistics = Ticket::whereHas('project', function ($query) use ($user) {
                  $query->where('client_email', $user->email);
                  })
                  ->groupBy('status')
                  ->select('status', \DB::raw('count(*) as count'))
                  ->get()
                  ->pluck('count', 'status')
                  ->toArray();
            }elseif ($user->hasRole('personnel')) {
                 // Personnel statistics
                  $personnel = Personnel::where('email', $user->email)->first();

                    if (!$personnel) {
                       return response()->json(['message' => 'Personnel not found'], 404);
                    }

                     $statistics = Ticket::whereHas('project.personnel', function ($query) use ($personnel) {
                     $query->where('personnel.id', $personnel->id);
                     })
                     ->groupBy('status')
                     ->select('status', \DB::raw('count(*) as count'))
                     ->get()
                     ->pluck('count', 'status')
                     ->toArray();
            }

        return $statistics;
    }
     public function getPriorityStatistics()
        {
            $user = auth()->user();

            if ($user->hasRole('admin')) {
                        $statistics = Ticket::groupBy('priority')
                            ->select('priority', \DB::raw('count(*) as count'))
                            ->get()
                            ->pluck('count', 'priority')
                            ->toArray();
            }elseif ($user->hasRole('client')) {
             $statistics = Ticket::whereHas('project', function ($query) use ($user) {
                $query->where('client_email', $user->email);
                 })
                 ->groupBy('priority')
                 ->select('priority', \DB::raw('count(*) as count'))
                 ->get()
                 ->pluck('count', 'priority')
                 ->toArray();
            }elseif ($user->hasRole('personnel')) {
                        // Personnel statistics
                              $personnel = Personnel::where('email', $user->email)->first();

                              if (!$personnel) {
                              return response()->json(['message' => 'Personnel not found'], 404);
                                }

                            $statistics = Ticket::whereHas('project.personnel', function ($query) use ($personnel) {
                                    $query->where('personnel.id', $personnel->id);
                                })
                                ->groupBy('priority')
                                ->select('priority', \DB::raw('count(*) as count'))
                                ->get()
                                ->pluck('count', 'priority')
                                ->toArray();
            }

            return $statistics;
        }
        public function getEtatStatistics()
                {
                 $user = auth()->user();

                   if ($user->hasRole('admin')) {
                                     $statistics = Project::groupBy('etat')
                                         ->select('etat', \DB::raw('count(*) as count'))
                                         ->get()
                                         ->pluck('count', 'etat')
                                         ->toArray();
                   }
                    elseif ($user->hasRole('client')) {
                                   $statistics = Project::where('client_email', $user->email)
                                           ->groupBy('etat')
                                           ->select('etat', \DB::raw('count(*) as count'))
                                           ->get()
                                           ->pluck('count', 'etat')
                                           ->toArray();
                   }elseif ($user->hasRole('personnel')) {
                           // Retrieve the personnel ID based on the user's email
                           $personnel = Personnel::where('email', $user->email)->first();

                           if (!$personnel) {
                               return response()->json(['message' => 'Personnel not found'], 404);
                           }

                           // Retrieve the statistics for the personnel's projects
                           $statistics = Project::whereHas('personnel', function ($query) use ($personnel) {
                                   $query->where('personnel.id', $personnel->id);
                               })
                               ->groupBy('etat')
                               ->select('etat', \DB::raw('count(*) as count'))
                               ->get()
                               ->pluck('count', 'etat')
                               ->toArray();
                        }else{
                          abort(401, 'Unauthenticated');
                        }

                    return $statistics;
                }

    public function addFramework(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:frameworks',
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageName = time() . '.' . $request->picture->extension();
        $request->picture->move(public_path('framework_images'), $imageName);

        $framework = new Framework();
        $framework->name = $request->input('name');
        $framework->picture = $imageName;
        $framework->save();

        return response()->json(['message' => 'Framework added successfully'], 201);
    }

    public function addTypeOfProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:typeofprojects',
        ]);

        $typeofproject = new TypeOfProject();
        $typeofproject->name = $request->input('name');
        $typeofproject->save();

        return response()->json(['message' => 'Type of project added successfully'], 201);
    }
    public function updateTypeOfProject(Request $request, $id)
    {
        $typeofproject = TypeOfProject::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:typeofprojects,name,' . $typeofproject->id,
        ]);

        $typeofproject->name = $request->input('name');
        $typeofproject->save();

        return response()->json(['message' => 'Type of project updated successfully']);
    }

    public function deleteTypeOfProject($id)
    {
        $typeofproject = TypeOfProject::findOrFail($id);
        $typeofproject->delete();

        return response()->json(['message' => 'Type of project deleted successfully']);
    }

    public function viewAllTypeOfProjects()
    {
        $typeofprojects = TypeOfProject::all();

        return response()->json(['typeofprojects' => $typeofprojects]);
    }

    public function updateFramework(Request $request, $id)
    {
        $framework = Framework::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:frameworks,name,' . $framework->id,
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $imageName = time() . '.' . $request->picture->extension();
            $request->picture->move(public_path('framework_images'), $imageName);
            $framework->picture = $imageName;
        }

        $framework->name = $request->input('name');
        $framework->save();

        return response()->json(['message' => 'Framework updated successfully']);
    }

    public function deleteFramework($id)
    {
        $framework = Framework::findOrFail($id);
        $framework->delete();

        return response()->json(['message' => 'Framework deleted successfully']);
    }

    public function viewAllFrameworks()
    {
        $frameworks = Framework::all();

        return response()->json(['frameworks' => $frameworks]);
    }



}

