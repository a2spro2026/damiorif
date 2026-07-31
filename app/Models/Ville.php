<?php

namespace App\Models;

use App\Support\SyncsNomParametre;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nom'])]
class Ville extends Model
{
    use SyncsNomParametre;
}
