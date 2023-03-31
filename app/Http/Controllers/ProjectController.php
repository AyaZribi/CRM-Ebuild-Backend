<?php

namespace App\Http\Controllers;

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

    // Create the project and assign staff
    $project = new Project();
    $project->projectname = $request->input('projectname');
    $project->typeofproject = $request->input('typeofproject');
    $project->frameworks = $request->input('frameworks');
    $project->database = $request->input('database');
    $project->description = $request->input('description');
    $project->datecreation = $request->input('datecreation');
    $project->deadline = $request->input('deadline');
    $project->etat = $request->input('etat');
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
        $project = Project::findOrFail($id);

        // Validate the incoming request
        $request->validate([
            'projectname' => 'string',
            'typeofproject' => 'string',
            'frameworks' => 'string',
            'database' => 'string',
            'description' => 'string',
            'datecreation' => 'date',
            'deadline' => 'date',
            'etat' => 'string',
            'staff' => 'array'
        ]);

        // Update the project
        $project->update($request->all());

        // Sync staff members
        if ($request->has('staff')) {
            $staff = $request->input('staff');
            $personnel = Personnel::whereIn('Name', $staff)->get();
            $project->personnel()->sync($personnel);
        }

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


}

