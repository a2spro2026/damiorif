<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bon_achat_id',
    'ref',
    'designation',
    'famille',
    'categorie',
    'qte',
    'prix_unitaire',
    'sous_total',
])]
class BonAchatLigne extends Model
{
    protected $table = 'bon_achat_lignes';

    protected function casts(): array
    {
        return [
            'qte' => 'decimal:2',
            'prix_unitaire' => 'decimal:2',
            'sous_total' => 'decimal:2',
        ];
    }

    public function bonAchat(): BelongsTo
    {
        return $this->belongsTo(BonAchat::class);
    }
}
