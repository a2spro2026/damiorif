<?php

namespace App\Models;

use App\Support\SyncsNomParametre;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nom'])]
class UniteMesure extends Model
{
    use SyncsNomParametre;

    protected $table = 'unites_mesure';
}
