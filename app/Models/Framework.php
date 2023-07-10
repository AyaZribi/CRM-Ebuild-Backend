<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Framework extends Model
{
    protected $table = 'frameworks';

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }}
