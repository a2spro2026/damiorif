<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cin')->nullable()->after('name');
            $table->string('contact')->nullable()->after('cin');
            $table->string('mot_de_passe')->nullable()->after('password');
            $table->json('autorisations')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cin', 'contact', 'mot_de_passe', 'autorisations']);
        });
    }
};
