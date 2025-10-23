<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->id('Id_Equipement');
            $table->decimal('prix', 15, 2);
            $table->string('nom', 50);
            $table->enum('Etat', ['OK', 'En_panne']);
            $table->unsignedBigInteger('Id_Type');
            $table->timestamps();

            $table->foreign('Id_Type')
                  ->references('Id_Type')
                  ->on('type_equipements')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
