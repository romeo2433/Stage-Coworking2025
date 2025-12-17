<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
    public function index()
    {
        // 1. Total clients (profil = 4)
        $totalClients = DB::table('utilisateurs')->where('Id_Profil', 4)->count();
    
        // 2. Statistiques globales sur les réservations
        $statsReservations = [
            'total' => DB::table('reservations')->count(),
            'en_attente' => DB::table('reservations')->where('Statut_Reservation', 'en_attente')->count(),
            'confirmee' => DB::table('reservations')->where('Statut_Reservation', 'confirmee')->count(),
            'payee' => DB::table('reservations')->where('Statut_Reservation', 'payee')->count(),
            'terminee' => DB::table('reservations')->where('Statut_Reservation', 'terminee')->count(),
            'annulee' => DB::table('reservations')->where('Statut_Reservation', 'annulee')->count(),
        ];
    
        // 3. Revenu total des réservations (via le champ total)
        $revenuTotal = DB::table('reservations')
            ->whereIn('Statut_Reservation', ['payee', 'terminee'])
            ->sum('total');
    
        // 4. Réservations par statut pour le donut chart
        $reservationsParStatut = [
            ['label' => 'En attente', 'value' => $statsReservations['en_attente'], 'color' => '#ffc107'],
            ['label' => 'Confirmée', 'value' => $statsReservations['confirmee'], 'color' => '#007bff'],
            ['label' => 'Payée', 'value' => $statsReservations['payee'], 'color' => '#28a745'],
            ['label' => 'Terminée', 'value' => $statsReservations['terminee'], 'color' => '#17a2b8'],
            ['label' => 'Annulée', 'value' => $statsReservations['annulee'], 'color' => '#dc3545'],
        ];
    
        // 5. Revenu mensuel (basé sur created_at des réservations payées/terminées)
        $revenusParMois = DB::table('reservations')
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, SUM(total) as total')
            ->whereIn('Statut_Reservation', ['payee', 'terminee'])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get()
            ->map(fn($row) => [
                'label' => $row->annee . '-' . str_pad($row->mois, 2, '0', STR_PAD_LEFT),
                'total' => (int)$row->total
            ])->toArray();
    
        // 6. Réservations par mois (toutes terminées)
        $reservationsParMois = DB::table('reservations')
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(*) as total')
            ->where('Statut_Reservation', 'terminee')
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get()
            ->map(fn($row) => [
                'label' => $row->annee . '-' . str_pad($row->mois, 2, '0', STR_PAD_LEFT),
                'total' => $row->total
            ])->toArray();
    
        return view('admin.statistiques.index', compact(
            'totalClients',
            'statsReservations',
            'revenuTotal',
            'reservationsParStatut',
            'revenusParMois',
            'reservationsParMois'
        ));
    }
}
