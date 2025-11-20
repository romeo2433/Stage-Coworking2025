<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    public function run()
    {
        DB::table('utilisateurs')->insert([
            [
                'numero' => '0388138146',
                'Prenom' => 'Romeo',
                'Entreprise' => 'Mahefa SARL',
                'email' => 'romeo@example.com',
                'password' => 'romeo1234',
                'Nom' => 'Mahefa',
                'date_inscription' => '2025-11-14',
                'Id_Profil' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'numero' => '0340771841',
                'Prenom' => 'Ravao',
                'Entreprise' => 'Art Malagasy',
                'email' => 'ravao@example.com',
                'password' =>'ravao1234',
                'Nom' => 'Mahefa',
                'date_inscription' => '2025-11-14',
                'Id_Profil' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
