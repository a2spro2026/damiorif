<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'date_charge',
    'type',
    'libelle',
    'montant',
    'depot',
    'user_id',
    'user_name',
])]
class Charge extends Model
{
    protected $table = 'charges';

    protected function casts(): array
    {
        return [
            'date_charge' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
