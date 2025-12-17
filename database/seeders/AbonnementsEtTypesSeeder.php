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
                'Nom_Abonnement' => 'Journalier',
                'Status_Abonnement' => 'actif',
                'Type_Abonnement' => 'journalier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nom_Abonnement' => 'Mensuel',
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

        DB::table('espaces')->insert([
            [
                'Nom' => 'Salle Réunion A',
                'Statut' => 'disponible',
                'capacite' => 12,
                'tarif_horaire' => 5000.00,
                'tarif_journalier' => 30000.00,
                'tarif_mensuel' => 500000.00,
                'Id_Type' => 1,
                'photo' => 'reunion1.jpeg',
            ],
            [
                'Nom' => 'Salle Réunion B',
                'Statut' => 'maintenance',
                'capacite' => 16,
                'tarif_horaire' => 7000.00,
                'tarif_journalier' => 35000.00,
                'tarif_mensuel' => 600000.00,
                'Id_Type' => 1,
                'photo' => 'reunion2.jpg',
            ],
            [
                'Nom' => 'Espace BtoB Premium',
                'Statut' => 'disponible',
                'capacite' => 8,
                'tarif_horaire' => 8000.00,
                'tarif_journalier' => 40000.00,
                'tarif_mensuel' => 700000.00,
                'Id_Type' => 2,
                'photo' => 'Cow.jpg',
            ],
            [
                'Nom' => 'Cyber 1',
                'Statut' => 'disponible',
                'capacite' => 5,
                'tarif_horaire' => 1000.00,
                'tarif_journalier' => 6000.00,
                'tarif_mensuel' => 100000.00,
                'Id_Type' => 3,
                'photo' => 'cow3.jpg',
            ],
            [
                'Nom' => 'Cyber 2',
                'Statut' => 'Fermé',
                'capacite' => 10,
                'tarif_horaire' => 1500.00,
                'tarif_journalier' => 9000.00,
                'tarif_mensuel' => 150000.00,
                'Id_Type' => 3,
                'photo' => 'fond4.jpg',
            ],
            [
                'Nom' => 'Salon Accueil 1',
                'Statut' => 'disponible',
                'capacite' => 6,
                'tarif_horaire' => 3000.00,
                'tarif_journalier' => 20000.00,
                'tarif_mensuel' => 300000.00,
                'Id_Type' => 4,
                'photo' => 'sall4.jpg',
            ],
            [
                'Nom' => 'Bureau Privé Alpha',
                'Statut' => 'disponible',
                'capacite' => 2,
                'tarif_horaire' => 6000.00,
                'tarif_journalier' => 35000.00,
                'tarif_mensuel' => 500000.00,
                'Id_Type' => 5,
                'photo' => 'salle2.jpg',
            ],
            [
                'Nom' => 'Bureau Privé Beta',
                'Statut' => 'Fermé',
                'capacite' => 3,
                'tarif_horaire' => 6500.00,
                'tarif_journalier' => 40000.00,
                'tarif_mensuel' => 550000.00,
                'Id_Type' => 5,
                'photo' => 'salle5.jpg',
            ],
        ]);
        
    }
}
