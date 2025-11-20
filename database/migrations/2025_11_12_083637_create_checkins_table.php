<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration.
     */
    public function up(): void
    {
        Schema::create('checkins_', function (Blueprint $table) {
            $table->id('Id_Checkin'); 
            $table->time('heure_arrivee'); 
            $table->time('heure_sortie')->nullable(); 
            $table->text('notes')->nullable(); 

            // clé étrangère vers la table reservations
            $table->unsignedBigInteger('Id_Reservation');
            $table->foreign('Id_Reservation')
                  ->references('Id_Reservation')
                  ->on('reservations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // timestamps Laravel (created_at / updated_at)
            $table->timestamps();
        });
    }

    /**
     * Annulation de la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins_');
    }
};
