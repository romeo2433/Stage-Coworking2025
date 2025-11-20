<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfilsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('profils')->insert([
            ['profil' => 'Administrateur', 'created_at' => $now, 'updated_at' => $now],
            ['profil' => 'Manager',        'created_at' => $now, 'updated_at' => $now],
            ['profil' => 'Employé',        'created_at' => $now, 'updated_at' => $now],
            ['profil' => 'Client',         'created_at' => $now, 'updated_at' => $now],
            ['profil' => 'Visiteur',       'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
