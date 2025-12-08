<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('espaces', function (Blueprint $table) {
            if (!Schema::hasColumn('espaces', 'quantite')) {
                $table->integer('quantite')
                      ->default(1)
                      ->after('capacite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('espaces', function (Blueprint $table) {
            if (Schema::hasColumn('espaces', 'quantite')) {
                $table->dropColumn('quantite');
            }
        });
    }
};
