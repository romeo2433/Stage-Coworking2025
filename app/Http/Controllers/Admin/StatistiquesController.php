<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
    public function index()
    {
        // 1. Clients (profil = 4)
        $totalClients = DB::table('utilisateurs')
            ->where('Id_Profil', 4)
            ->count();

        // 2. Réservations terminées
        $totalReservations = DB::table('reservations')
            ->where('Statut_Reservation', 'terminee')
            ->count();

        // 3. Total paiements payés
        $revenuTotal = DB::table('paiements')
            ->where('statut_paiement', 'paye')
            ->sum('montant_payer');

        // 4. Revenu par mois basé sur date_paiement
        $revenusParMois = DB::table('paiements')
        ->selectRaw('YEAR(date_paiement) as annee, MONTH(date_paiement) as mois, SUM(montant_payer) as total')
        ->where('statut_paiement', 'paye')
        ->groupBy('annee', 'mois')
        ->orderBy('annee')
        ->orderBy('mois')
        ->get()
        ->map(function ($row) {
            return [
                'label' => $row->annee . '-' . str_pad($row->mois, 2, '0', STR_PAD_LEFT),
                'total' => $row->total,
            ];
        })
        ->toArray();
        // 5. Réservations terminées par mois et année
            $reservationsParMois = DB::table('reservations')
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(*) as total')
            ->where('Statut_Reservation', 'terminee')
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => $row->annee . '-' . str_pad($row->mois, 2, '0', STR_PAD_LEFT),
                    'total' => $row->total,
                ];
            })
            ->toArray();
            return view('admin.statistiques.index', compact(
                'totalClients', 'totalReservations', 'revenuTotal', 'revenusParMois', 'reservationsParMois'
            ));            
    }
}
