<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'object',
        'description',
        'file',
        'image',
    ];

    // Define relationship with Ticket model (Many-to-One)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Define relationship with User model (Many-to-One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
