<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('type_clients', function (Blueprint $table) {
            $table->bigIncrements('Id_Type_Client');
            $table->string('type', 30)->unique();
            $table->timestamps();
        });

        // Types par défaut
        DB::table('type_clients')->insert([
            ['type' => 'Occasionnel'],
            ['type' => 'Abonne'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('type_clients');
    }
};
