<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('espaces', function (Blueprint $table) {
            $table->id('Id_Espace');
            $table->string('Nom', 50);
            $table->enum('Statut', ['disponible', 'Fermé', 'maintenance']);
            $table->integer('capacite');
            $table->decimal('tarif_horaire', 9, 2);
            $table->decimal('tarif_journalier', 9, 2);
            $table->decimal('tarif_mensuel', 10, 2);
            $table->unsignedBigInteger('Id_Type');
            $table->string('photo')->nullable();

            // Clé étrangère
            $table->foreign('Id_Type')
                  ->references('Id_Type')
                  ->on('type_espaces')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espaces');
    }
};
