<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mode', function (Blueprint $table) {
            $table->id('Id_Mode');
            $table->string('Type_Mode', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mode');
    }
};
