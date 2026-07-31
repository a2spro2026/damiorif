<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reglement_vente_id',
    'bon_vente_id',
    'numero_bon',
    'montant_applique',
])]
class ReglementVenteLigne extends Model
{
    protected $table = 'reglement_vente_lignes';

    protected function casts(): array
    {
        return [
            'montant_applique' => 'decimal:2',
        ];
    }

    public function reglement(): BelongsTo
    {
        return $this->belongsTo(ReglementVente::class, 'reglement_vente_id');
    }

    public function bonVente(): BelongsTo
    {
        return $this->belongsTo(BonVente::class);
    }
}
