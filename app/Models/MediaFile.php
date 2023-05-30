<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
protected $fillable = ['file_name', 'file_path'];

    protected $table = 'media';

// Define relationships with other models
public function ticket()
{
return $this->belongsTo(Ticket::class);
}

// Other relationships and methods as needed
}
