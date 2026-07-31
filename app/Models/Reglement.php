<?php

namespace App\Models;

use App\Support\SyncsNomParametre;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nom'])]
class Reglement extends Model
{
    use SyncsNomParametre;
}
