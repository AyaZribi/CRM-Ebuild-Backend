<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;
    protected $fillable = [
        'devis_id',
        'nature',
        'montant_ht',
        'taux_tva',
        'montant_ttc',
    ];
    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }


}
