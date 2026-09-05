<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'stock_mouvement_id',
    'produit_id',
    'ref_produit',
    'designation',
    'unite',
    'quantite',
    'prix_unitaire',
    'sous_total',
])]
class StockMouvementLigne extends Model
{
    protected function casts(): array
    {
        return [
            'quantite' => 'decimal:3',
            'prix_unitaire' => 'decimal:2',
            'sous_total' => 'decimal:2',
        ];
    }

    public function mouvement(): BelongsTo
    {
        return $this->belongsTo(StockMouvement::class, 'stock_mouvement_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
