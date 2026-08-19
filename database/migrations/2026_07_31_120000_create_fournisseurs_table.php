<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->date('date_fiche')->nullable();
            $table->string('ref_frns')->unique();
            $table->string('nom_fournisseur');
            $table->string('nom_gerant')->nullable();
            $table->string('contact')->nullable();
            $table->string('ville')->nullable();
            $table->string('type_reglement')->nullable();
            $table->string('banque')->nullable();
            $table->string('rib', 24);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
