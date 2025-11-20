<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TypeReservationsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('Type_reservations')->insert([
            ['Type' => 'Standard',  'created_at' => $now, 'updated_at' => $now],
            ['Type' => 'Premium',   'created_at' => $now, 'updated_at' => $now],
            ['Type' => 'VIP',       'created_at' => $now, 'updated_at' => $now],
            ['Type' => 'Réunion',   'created_at' => $now, 'updated_at' => $now],
            ['Type' => 'Événement', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
