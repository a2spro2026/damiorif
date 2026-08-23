<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_commande_depot', function (Blueprint $table) {
            $table->id();
            $table->date('date_commande');
            $table->string('numero', 30)->unique();
            $table->string('depot_demandeur', 50);
            $table->string('depot_fournisseur', 50)->default('damiorif');
            $table->string('statut', 20)->default('brouillon');
            $table->text('note')->nullable();
            $table->foreignId('stock_mouvement_id')->nullable()->constrained('stock_mouvements')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->timestamps();
        });

        Schema::create('bon_commande_depot_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_depot_id')->constrained('bons_commande_depot')->cascadeOnDelete();
            $table->string('ref', 100)->nullable();
            $table->string('designation');
            $table->decimal('qte_demandee', 12, 3);
            $table->decimal('qte_expediee', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_commande_depot_lignes');
        Schema::dropIfExists('bons_commande_depot');
    }
};
