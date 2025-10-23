<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id('Id_Paiement');
            $table->string('Reference', 50);
            $table->decimal('montant_payer', 15, 2);
            $table->decimal('montant_Impayer', 15, 2)->nullable();
            $table->date('date_paiement');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'partiel']);
            
            $table->unsignedBigInteger('Id_Reservation');
            $table->unsignedBigInteger('Id_Mode');
            
            $table->foreign('Id_Reservation')
                  ->references('Id_Reservation')
                  ->on('reservations')
                  ->onDelete('cascade');

            $table->foreign('Id_Mode')
                  ->references('Id_Mode')
                  ->on('mode')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
