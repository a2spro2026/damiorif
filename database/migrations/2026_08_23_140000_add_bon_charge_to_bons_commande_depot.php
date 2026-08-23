<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bons_commande_depot', function (Blueprint $table) {
            $table->string('numero_bon_charge', 30)->nullable()->after('numero');
            $table->date('date_bon_charge')->nullable()->after('numero_bon_charge');
        });
    }

    public function down(): void
    {
        Schema::table('bons_commande_depot', function (Blueprint $table) {
            $table->dropColumn(['numero_bon_charge', 'date_bon_charge']);
        });
    }
};
