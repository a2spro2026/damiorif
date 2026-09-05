<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_mouvement_lignes', function (Blueprint $table) {
            $table->decimal('prix_unitaire', 14, 2)->default(0)->after('quantite');
            $table->decimal('sous_total', 14, 2)->default(0)->after('prix_unitaire');
        });

        Schema::table('stock_mouvements', function (Blueprint $table) {
            $table->decimal('montant', 14, 2)->default(0)->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('stock_mouvement_lignes', function (Blueprint $table) {
            $table->dropColumn(['prix_unitaire', 'sous_total']);
        });

        Schema::table('stock_mouvements', function (Blueprint $table) {
            $table->dropColumn('montant');
        });
    }
};
