<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglements_vente', function (Blueprint $table) {
            $table->id();
            $table->date('date_reglement');
            $table->string('numero')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('nom_client');
            $table->string('type_reglement')->nullable();
            $table->string('banque')->nullable();
            $table->string('nom_tire')->nullable();
            $table->decimal('montant', 14, 2)->default(0);
            $table->date('date_encaissement')->nullable();
            $table->string('statut')->default('en_instance');
            $table->timestamps();
        });

        Schema::create('reglement_vente_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reglement_vente_id')->constrained('reglements_vente')->cascadeOnDelete();
            $table->foreignId('bon_vente_id')->constrained('bons_vente')->cascadeOnDelete();
            $table->string('numero_bon');
            $table->decimal('montant_applique', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglement_vente_lignes');
        Schema::dropIfExists('reglements_vente');
    }
};
