<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id('Id_Utilisateur');
            $table->string('numero', 20);
            $table->string('Prenom', 50);
            $table->string('Nom', 50);
            $table->string('Entreprise', 100)->nullable();
            $table->string('email', 100);
            $table->string('password', 255);
            $table->date('date_inscription');
            $table->unsignedBigInteger('Id_Profil');
            $table->timestamps();

            $table->foreign('Id_Profil')
                  ->references('Id_Profil')
                  ->on('profils')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
