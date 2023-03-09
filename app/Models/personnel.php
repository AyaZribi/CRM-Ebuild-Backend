<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personnel extends Model
{
    use HasFactory;
    protected $table = 'personnel';

    protected $fillable = [
        'phone_number',
        'ID_card',
        'Work_tasks',
        'salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
