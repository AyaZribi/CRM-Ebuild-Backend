<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'projectname',
        'typeofproject',
        'frameworks',
        'database',
        'description',
        'datecreation',
        'deadline',
        'etat'
    ];
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    public function personnel()
    {
        return $this->belongsToMany(Personnel::class);
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function typeofproject()
    {
        return $this->belongsTo(TypeOfProject::class);
    }

    public function frameworks()
    {
        return $this->belongsToMany(Framework::class);
    }
}
