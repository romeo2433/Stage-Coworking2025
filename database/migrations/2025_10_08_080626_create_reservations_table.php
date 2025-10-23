<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('Id_Reservation'); 
            $table->string('reference', 50);
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->integer('duree_heures');
            $table->integer('total');
            $table->enum('Statut_Reservation', ['en_attente','confirmee','annulee','terminee','partiellement_payee','en_attente_de_paiement']);
            
            // clés étrangères (nullable si optionnelles)
            $table->unsignedBigInteger('Id_Abonnement')->nullable();
            $table->unsignedBigInteger('Id_Option')->nullable();
            $table->unsignedBigInteger('Id_Type');
            $table->unsignedBigInteger('Id_Espace');
            $table->unsignedBigInteger('Id_Utilisateur');

            $table->timestamps(); // created_at et updated_at

            // contraintes FK
            $table->foreign('Id_Abonnement')->references('Id_Abonnement')->on('abonnements')->onDelete('set null');
            $table->foreign('Id_Option')->references('Id_Option')->on('options')->onDelete('set null');
            $table->foreign('Id_Type')->references('Id_Type')->on('type_reservations')->onDelete('cascade');
            $table->foreign('Id_Espace')->references('Id_Espace')->on('espaces')->onDelete('cascade');
            $table->foreign('Id_Utilisateur')->references('Id_Utilisateur')->on('utilisateurs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
