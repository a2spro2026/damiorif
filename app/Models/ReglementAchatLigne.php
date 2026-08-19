<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reglement_achat_id',
    'bon_achat_id',
    'numero_bon',
    'montant_applique',
])]
class ReglementAchatLigne extends Model
{
    protected $table = 'reglement_achat_lignes';

    protected function casts(): array
    {
        return [
            'montant_applique' => 'decimal:2',
        ];
    }

    public function reglement(): BelongsTo
    {
        return $this->belongsTo(ReglementAchat::class, 'reglement_achat_id');
    }

    public function bonAchat(): BelongsTo
    {
        return $this->belongsTo(BonAchat::class);
    }
}
