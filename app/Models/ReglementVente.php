<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date_reglement',
    'numero',
    'client_id',
    'nom_client',
    'type_reglement',
    'banque',
    'nom_tire',
    'montant',
    'date_encaissement',
    'statut',
])]
class ReglementVente extends Model
{
    protected $table = 'reglements_vente';

    protected function casts(): array
    {
        return [
            'date_reglement' => 'date',
            'date_encaissement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(ReglementVenteLigne::class);
    }

    public static function nextNumero(): string
    {
        $last = static::query()->orderByDesc('id')->value('numero');
        $num = 1;

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = ((int) $m[1]) + 1;
        }

        return 'RV-'.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
