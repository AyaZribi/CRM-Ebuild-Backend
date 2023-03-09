<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        /*'name',
        'email',*/
        'phone_number',
        'address',
        'social_reason',
        'RNE',
        'confirmation',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
