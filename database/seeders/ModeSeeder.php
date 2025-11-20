<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('mode')->insert([
            ['Type_Mode' => 'Espèces',           'created_at' => $now, 'updated_at' => $now],
            ['Type_Mode' => 'Carte bancaire',    'created_at' => $now, 'updated_at' => $now],
            ['Type_Mode' => 'Mobile Money',      'created_at' => $now, 'updated_at' => $now],
            ['Type_Mode' => 'Virement bancaire', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
