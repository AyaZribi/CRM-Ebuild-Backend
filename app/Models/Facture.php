<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;
    protected $fillable = [
        'client',
        'client_email',
        'date_creation',
        'nombre_operations',
        'total_montant_ht',
        'total_montant_ttc',
        'total_montant_letters',
    ];
    public function getFormattedIdAttribute()
    {
        return 'Facture N° ' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
    protected $appends = ['formatted_id'];


    protected static function booted()
    {
        static::creating(function ($facture) {
            $facture->date_creation = now();
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function operationfactures()
    {
        return $this->hasMany(Operationfacture::class);
    }
}
