<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_Type_Client')
                  ->default(1)
                  ->after('Id_Profil');

            $table->foreign('Id_Type_Client')
                  ->references('Id_Type_Client')
                  ->on('type_clients')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropForeign(['Id_Type_Client']);
            $table->dropColumn('Id_Type_Client');
        });
    }
};
