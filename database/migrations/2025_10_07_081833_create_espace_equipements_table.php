<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('espace_equipements', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_Espace');
            $table->unsignedBigInteger('Id_Equipement');
            $table->integer('Nombre_Equipements');
            $table->primary(['Id_Espace', 'Id_Equipement']);

            $table->foreign('Id_Espace')
                  ->references('Id_Espace')
                  ->on('espaces')
                  ->onDelete('cascade');

            $table->foreign('Id_Equipement')
                  ->references('Id_Equipement')
                  ->on('equipements')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espace_equipements');
    }
};
