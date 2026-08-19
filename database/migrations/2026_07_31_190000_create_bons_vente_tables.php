<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_vente', function (Blueprint $table) {
            $table->id();
            $table->date('date_bon');
            $table->string('numero_bon')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('nom_client');
            $table->string('ville')->nullable();
            $table->string('type_reglement')->nullable();
            $table->unsignedSmallInteger('echeance')->nullable();
            $table->string('depot')->nullable();
            $table->decimal('qte_totale', 12, 2)->default(0);
            $table->decimal('montant', 14, 2)->default(0);
            $table->decimal('solde', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('bon_vente_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_vente_id')->constrained('bons_vente')->cascadeOnDelete();
            $table->string('ref')->nullable();
            $table->string('designation');
            $table->string('famille')->nullable();
            $table->string('categorie')->nullable();
            $table->decimal('qte', 12, 2)->default(0);
            $table->decimal('prix_unitaire', 14, 2)->default(0);
            $table->decimal('sous_total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_vente_lignes');
        Schema::dropIfExists('bons_vente');
    }
};
