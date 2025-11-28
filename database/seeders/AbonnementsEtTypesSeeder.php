<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbonnementsEtTypesSeeder extends Seeder
{
    public function run()
    {
        // Abonnements
        DB::table('abonnements')->insert([
            [
                'Nom_Abonnement' => 'Abonnement Journalier',
                'Status_Abonnement' => 'actif',
                'Type_Abonnement' => 'journalier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom_Abonnement' => 'Abonnement Mensuel Premium',
                'Status_Abonnement' => 'actif',
                'Type_Abonnement' => 'mensuel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Types d'espaces
        DB::table('type_espaces')->insert([
            ['Id_Type' => 1, 'Type_Espace' => 'Salle de réunion'],
            ['Id_Type' => 2, 'Type_Espace' => 'Espace BtoB'],
            ['Id_Type' => 3, 'Type_Espace' => 'Cyber (poste PC)'],
            ['Id_Type' => 4, 'Type_Espace' => 'Salon d accueil'],
            ['Id_Type' => 5, 'Type_Espace' => 'Bureau privé'],
        ]);

        // Types d'équipements
        DB::table('type_equipements')->insert([
            ['Type' => 'Informatique'],
            ['Type' => 'Mobilier'],
            ['Type' => 'Audio-visuel'],
            ['Type' => 'Électroménager'],
            ['Type' => 'Divers'],
        ]);
    }
}
