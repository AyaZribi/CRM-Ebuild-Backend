<?php

namespace App\Http\Controllers;

use App\Mail\ProjectCreated;
use App\Mail\TacheCreated;
use App\Models\Comment;
use App\Models\personnel;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TacheController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'intitule' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'file' => 'nullable|file',
            'image' => 'nullable|image',
            'commentaire' => 'nullable|string',
            'staff' => 'required|array'
        ]);

        $tache = new Tache();
        $tache->intitule = $request->input('intitule');
        $tache->deadline = $request->input('deadline');
        $tache->description = $request->input('description');

        if ($request->hasFile('file')) {
            $tache->file = $request->file('file')->store('files');
        }

        if ($request->hasFile('image')) {
            $tache->image = $request->file('image')->store('images');
        }

        $tache->save();

        $staff = $request->input('staff');
        foreach ($staff as $staffName) {
            $personnel = Personnel::where('Name', $staffName)->firstOrFail();
            $tache->personnel()->attach($personnel);
        }

        // Send email to all staff
        $staffEmails = Personnel::whereIn('Name', $staff)->pluck('Email')->toArray();
        Mail::to($staffEmails)->send(new TacheCreated($tache));


        // Save the comment, if provided
        $comment = $request->input('commentaire');
        if ($comment) {
            $tache->comments()->create(['comment' => $comment]);
        }

        return response()->json(['message' => 'Tache created successfully'], 201);
    }

    public function createcomment(Tache $tache, Request $request) {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = new Comment();
        $comment->comment = $request->input('comment');
        $comment->tache_id = $tache->id;
        $comment->save();

        return response()->json(['message' => 'Comment created successfully'], 201);
    }
}
