<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bon_commande_depot_id',
    'ref',
    'designation',
    'qte_demandee',
    'qte_expediee',
])]
class BonCommandeDepotLigne extends Model
{
    protected $table = 'bon_commande_depot_lignes';

    protected function casts(): array
    {
        return [
            'qte_demandee' => 'decimal:3',
            'qte_expediee' => 'decimal:3',
        ];
    }

    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BonCommandeDepot::class, 'bon_commande_depot_id');
    }
}
