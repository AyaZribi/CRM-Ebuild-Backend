<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = ['client', 'client_email', 'date_creation', 'nombre_operations'];
    protected static function booted()
    {
        static::creating(function ($devis) {
            $devis->date_creation = now();
        });
    }
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}

