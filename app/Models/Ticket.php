<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'project_id',
        'object',
        'description',
        'closing_date',
        'status',
        'priority'
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

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
    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }
}
