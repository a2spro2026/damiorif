<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banques', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('tresoreries', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('cin')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_mouvements', function (Blueprint $table) {
            $table->id();
            $table->date('date_mouvement');
            $table->string('numero')->unique();
            $table->string('type'); // entree|sortie|transfert
            $table->string('depot');
            $table->string('depot_destination')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_mouvement_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_mouvement_id')->constrained('stock_mouvements')->cascadeOnDelete();
            $table->foreignId('produit_id')->nullable()->constrained('produits')->nullOnDelete();
            $table->string('ref_produit')->nullable();
            $table->string('designation');
            $table->string('unite')->nullable();
            $table->decimal('quantite', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mouvement_lignes');
        Schema::dropIfExists('stock_mouvements');
        Schema::dropIfExists('chauffeurs');
        Schema::dropIfExists('tresoreries');
        Schema::dropIfExists('banques');
    }
};
