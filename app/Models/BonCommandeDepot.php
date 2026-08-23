<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date_commande',
    'numero',
    'numero_bon_charge',
    'date_bon_charge',
    'depot_demandeur',
    'depot_fournisseur',
    'statut',
    'note',
    'stock_mouvement_id',
    'user_id',
    'user_name',
])]
class BonCommandeDepot extends Model
{
    protected $table = 'bons_commande_depot';

    protected function casts(): array
    {
        return [
            'date_commande' => 'date',
            'date_bon_charge' => 'date',
        ];
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(BonCommandeDepotLigne::class);
    }

    public function stockMouvement(): BelongsTo
    {
        return $this->belongsTo(StockMouvement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function nextNumero(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero');
        $n = 1;
        if (is_string($last) && preg_match('/BCD-(\d+)/', $last, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return 'BCD-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public static function nextNumeroBonCharge(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero_bon_charge');
        $n = 1;
        if (is_string($last) && preg_match('/BCH-(\d+)/', $last, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return 'BCH-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public function qteTotale(): float
    {
        return round((float) $this->lignes->sum('qte_demandee'), 3);
    }

    public function isEditable(): bool
    {
        return $this->statut === 'brouillon';
    }
}
