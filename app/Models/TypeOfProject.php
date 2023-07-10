<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfProject extends Model
{
    protected $table = 'typeofprojects';

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
