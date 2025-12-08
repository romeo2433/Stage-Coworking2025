<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Espace;
use App\Models\Utilisateur;
use Carbon\Carbon;

class EtatAnalytiqueController extends Controller
{
    public function index(Request $request)
    {
        // Filtre par date si fourni
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date   = $request->query('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Récupérer tous les paiements dans la période
        $paiements = Paiement::whereBetween('date_paiement', [$start_date, $end_date])->get();

        // Analyse par espace
        $paiementsParEspace = $paiements->groupBy(fn($p) => $p->reservation?->espace?->Nom ?? '—');

        // Analyse par client
        $paiementsParClient = $paiements->groupBy(fn($p) => $p->reservation?->utilisateur?->Prenom . ' ' . $p->reservation?->utilisateur?->Nom ?? '—');

        return view('admin.etat_analytique', compact(
            'paiements',
            'paiementsParEspace',
            'paiementsParClient',
            'start_date',
            'end_date'
        ));
    }
}
