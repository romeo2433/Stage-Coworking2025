<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;



class EtatAnalytiqueController extends Controller
{
    public function index(Request $request)
    {
        // ... (ton code existant reste identique jusqu'au return view)
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date   = $request->query('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $paiements = Paiement::with(['reservation.espace', 'reservation.utilisateur'])
            ->whereBetween('date_paiement', [$start_date, $end_date])
            ->get();

        $totalGeneral = $paiements->sum('montant_payer');

        $paiementsParEspace = $paiements->groupBy(fn($p) => $p->reservation?->espace?->Nom ?? 'Inconnu')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('montant_payer')
            ])->sortByDesc('total');

        $topEspaces = $paiementsParEspace->take(5);

        $paiementsParClient = $paiements->groupBy(fn($p) => trim(($p->reservation?->utilisateur?->Prenom ?? '') . ' ' . ($p->reservation?->utilisateur?->Nom ?? 'Inconnu')))
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('montant_payer')
            ])->sortByDesc('total');

        $topClients = $paiementsParClient->take(5);

        $paiementsParMode = $paiements->groupBy('mode_paiement')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('montant_payer')
            ]);

        $chartEspacesLabels = $topEspaces->keys()->toArray();
        $chartEspacesData   = $topEspaces->pluck('total')->toArray();
        $chartClientsLabels = $topClients->keys()->toArray();
        $chartClientsData   = $topClients->pluck('total')->toArray();

        return view('admin.etat_analytique', compact(
            'paiements',
            'paiementsParEspace',
            'paiementsParClient',
            'paiementsParMode',
            'topEspaces',
            'topClients',
            'totalGeneral',
            'start_date',
            'end_date',
            'chartEspacesLabels',
            'chartEspacesData',
            'chartClientsLabels',
            'chartClientsData'
        ));
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        // Réutilise la même logique que index()
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date   = $request->query('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $paiements = Paiement::with(['reservation.espace', 'reservation.utilisateur'])
            ->whereBetween('date_paiement', [$start_date, $end_date])
            ->get();

        $totalGeneral = $paiements->sum('montant_payer');

        $paiementsParEspace = $paiements->groupBy(fn($p) => $p->reservation?->espace?->Nom ?? 'Inconnu')
            ->map(fn($group) => ['count' => $group->count(), 'total' => $group->sum('montant_payer')])
            ->sortByDesc('total');

        $paiementsParClient = $paiements->groupBy(fn($p) => trim(($p->reservation?->utilisateur?->Prenom ?? '') . ' ' . ($p->reservation?->utilisateur?->Nom ?? 'Inconnu')))
            ->map(fn($group) => ['count' => $group->count(), 'total' => $group->sum('montant_payer')])
            ->sortByDesc('total');

        $paiementsParMode = $paiements->groupBy('mode_paiement')
            ->map(fn($group) => ['count' => $group->count(), 'total' => $group->sum('montant_payer')]);

        $pdf = Pdf::loadView('admin.etat_analytique_pdf', compact(
            'paiementsParEspace',
            'paiementsParClient',
            'paiementsParMode',
            'totalGeneral',
            'start_date',
            'end_date'
        ));

        $fileName = 'etat-analytique_' . Carbon::parse($start_date)->format('d-m-Y') . '_au_' . Carbon::parse($end_date)->format('d-m-Y') . '.pdf';

        return $pdf->download($fileName);
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date   = $request->query('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $paiements = Paiement::with(['reservation.espace', 'reservation.utilisateur'])
            ->whereBetween('date_paiement', [$start_date, $end_date])
            ->get();

        $totalGeneral = $paiements->sum('montant_payer');

        // Analyse par espace
        $dataEspace = $paiements->groupBy(fn($p) => $p->reservation?->espace?->Nom ?? 'Inconnu')
            ->map(fn($group, $espace) => [
                'Espace' => $espace,
                'Nombre de paiements' => $group->count(),
                'Total payé (Ar)' => $group->sum('montant_payer'),
                '% du CA' => $totalGeneral > 0 ? round(($group->sum('montant_payer') / $totalGeneral) * 100, 2) : 0
            ])->values()->prepend(['Espace' => 'Espace', 'Nombre de paiements' => 'Nombre', 'Total payé (Ar)' => 'Total', '% du CA' => '%']); // En-têtes

        // Analyse par client
        $dataClient = $paiements->groupBy(fn($p) => trim(($p->reservation?->utilisateur?->Prenom ?? '') . ' ' . ($p->reservation?->utilisateur?->Nom ?? 'Inconnu')))
            ->map(fn($group, $client) => [
                'Client' => $client,
                'Nombre de paiements' => $group->count(),
                'Total payé (Ar)' => $group->sum('montant_payer'),
                '% du CA' => $totalGeneral > 0 ? round(($group->sum('montant_payer') / $totalGeneral) * 100, 2) : 0
            ])->values()->prepend(['Client' => 'Client', 'Nombre de paiements' => 'Nombre', 'Total payé (Ar)' => 'Total', '% du CA' => '%']);

        // Analyse par mode (si champ existe)
        $dataMode = $paiements->groupBy('mode_paiement')
            ->map(fn($group, $mode) => [
                'Mode de paiement' => $mode ?? 'Non spécifié',
                'Nombre' => $group->count(),
                'Total payé (Ar)' => $group->sum('montant_payer'),
                '% du CA' => $totalGeneral > 0 ? round(($group->sum('montant_payer') / $totalGeneral) * 100, 2) : 0
            ])->values()->prepend(['Mode de paiement' => 'Mode', 'Nombre' => 'Nombre', 'Total payé (Ar)' => 'Total', '% du CA' => '%']);

        $fileName = 'etat-analytique_' . Carbon::parse($start_date)->format('d-m-Y') . '_au_' . Carbon::parse($end_date)->format('d-m-Y');

        return Excel::create($fileName, function($excel) use ($dataEspace, $dataClient, $dataMode, $totalGeneral, $start_date, $end_date) {

            // Sheet 1 : Par Espace
            $excel->sheet('Par Espace', function($sheet) use ($dataEspace, $totalGeneral, $start_date, $end_date) {
                $sheet->row(1, ['État Analytique - Analyse par Espace']);
                $sheet->row(2, ['Période : ' . Carbon::parse($start_date)->format('d/m/Y') . ' au ' . Carbon::parse($end_date)->format('d/m/Y')]);
                $sheet->row(3, ['Total général : ' . number_format($totalGeneral, 0, ',', ' ') . ' Ar']);
                $sheet->rows($dataEspace);
                $sheet->setFontSize(11);
                $sheet->setAutoSize(true);
            });

            // Sheet 2 : Par Client
            $excel->sheet('Par Client', function($sheet) use ($dataClient, $totalGeneral, $start_date, $end_date) {
                $sheet->row(1, ['État Analytique - Analyse par Client']);
                $sheet->row(2, ['Période : ' . Carbon::parse($start_date)->format('d/m/Y') . ' au ' . Carbon::parse($end_date)->format('d/m/Y')]);
                $sheet->row(3, ['Total général : ' . number_format($totalGeneral, 0, ',', ' ') . ' Ar']);
                $sheet->rows($dataClient);
                $sheet->setAutoSize(true);
            });

            // Sheet 3 : Par Mode de paiement (si données)
            if ($dataMode->count() > 1) { // >1 car on a les en-têtes
                $excel->sheet('Par Mode', function($sheet) use ($dataMode, $totalGeneral, $start_date, $end_date) {
                    $sheet->row(1, ['État Analytique - Répartition par Mode']);
                    $sheet->row(2, ['Période : ' . Carbon::parse($start_date)->format('d/m/Y') . ' au ' . Carbon::parse($end_date)->format('d/m/Y')]);
                    $sheet->row(3, ['Total général : ' . number_format($totalGeneral, 0, ',', ' ') . ' Ar']);
                    $sheet->rows($dataMode);
                    $sheet->setAutoSize(true);
                });
            }

        })->download('xlsx');
    }
}