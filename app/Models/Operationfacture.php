<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operationfacture extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'devis_id',
        'nature',
        'quantité',
        'montant_ht',
        'taux_tva',
        'montant_ttc',
    ];
    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }
}
