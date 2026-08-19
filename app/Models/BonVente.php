<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date_bon',
    'numero_bon',
    'client_id',
    'nom_client',
    'ville',
    'type_reglement',
    'echeance',
    'depot',
    'qte_totale',
    'montant',
    'solde',
])]
class BonVente extends Model
{
    protected $table = 'bons_vente';

    protected function casts(): array
    {
        return [
            'date_bon' => 'date',
            'qte_totale' => 'decimal:2',
            'montant' => 'decimal:2',
            'solde' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(BonVenteLigne::class);
    }

    public static function nextNumero(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero_bon');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'BV-'.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
