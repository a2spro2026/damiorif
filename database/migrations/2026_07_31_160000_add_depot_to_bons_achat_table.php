<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bons_achat', function (Blueprint $table) {
            $table->string('depot')->nullable()->after('echeance');
        });
    }

    public function down(): void
    {
        Schema::table('bons_achat', function (Blueprint $table) {
            $table->dropColumn('depot');
        });
    }
};
