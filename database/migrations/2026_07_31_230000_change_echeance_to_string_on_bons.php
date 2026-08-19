<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bons_achat', function (Blueprint $table) {
            $table->string('echeance')->nullable()->change();
        });

        Schema::table('bons_vente', function (Blueprint $table) {
            $table->string('echeance')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bons_achat', function (Blueprint $table) {
            $table->unsignedSmallInteger('echeance')->nullable()->change();
        });

        Schema::table('bons_vente', function (Blueprint $table) {
            $table->unsignedSmallInteger('echeance')->nullable()->change();
        });
    }
};
