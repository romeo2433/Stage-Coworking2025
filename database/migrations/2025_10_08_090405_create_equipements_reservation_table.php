<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipements_reservation', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_Equipement');
            $table->unsignedBigInteger('Id_Reservation');
            $table->integer('Nombre_Ajout')->default(1);

            $table->primary(['Id_Equipement', 'Id_Reservation']);

            $table->foreign('Id_Equipement')
                  ->references('Id_Equipement')
                  ->on('equipements')
                  ->onDelete('cascade');

            $table->foreign('Id_Reservation')
                  ->references('Id_Reservation')
                  ->on('reservations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipements_reservation');
    }
};
