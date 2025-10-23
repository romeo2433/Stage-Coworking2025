<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('type_espaces', function (Blueprint $table) {
            $table->id('Id_Type');
            $table->string('Type_Espace', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_espaces');
    }
};
