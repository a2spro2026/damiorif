<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglements_achat', function (Blueprint $table) {
            $table->id();
            $table->date('date_reglement');
            $table->string('numero')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->string('nom_fournisseur');
            $table->string('type_reglement')->nullable();
            $table->string('banque')->nullable();
            $table->string('nom_tire')->nullable();
            $table->decimal('montant', 14, 2)->default(0);
            $table->date('date_decaissement')->nullable();
            $table->timestamps();
        });

        Schema::create('reglement_achat_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reglement_achat_id')->constrained('reglements_achat')->cascadeOnDelete();
            $table->foreignId('bon_achat_id')->constrained('bons_achat')->cascadeOnDelete();
            $table->string('numero_bon');
            $table->decimal('montant_applique', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglement_achat_lignes');
        Schema::dropIfExists('reglements_achat');
    }
};
