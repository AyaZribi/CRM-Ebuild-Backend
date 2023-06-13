<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'project_id',
        'object',
        'description',
        'closing_date',
        'status',
        'priority',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function files()
        {
            return $this->hasMany(File::class);
        }

   public function registerMediaCollections(): void
     {
         $this->addMediaCollection('attachments');
     }
}
