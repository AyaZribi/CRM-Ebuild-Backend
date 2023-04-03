<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class personnel extends Authenticatable
{
    use \Laravel\Sanctum\HasApiTokens, HasFactory, \Illuminate\Notifications\Notifiable;
    protected $table = 'personnel';

    protected $fillable = [
        'address',
        'phone_number',
        'ID_card',
        'Work_tasks',
        'subcontracting',
        'salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
    public function taches()
    {
        return $this->belongsToMany(Tache::class);
    }
}
