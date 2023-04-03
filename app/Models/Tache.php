<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;
    protected $fillable = ['intitule', 'deadline', 'description', 'file', 'image'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function personnel()
    {
        return $this->belongsToMany(Personnel::class);
    }
}
