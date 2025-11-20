<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Espace;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPaiementController extends Controller
{
    public function index(Request $request)
        {
            $espaces = Espace::orderBy('Nom')->get();
            $query = Paiement::with([
                'reservation.utilisateur', 
                'reservation.espace', 
                'mode'
            ]);

            // Filtre Client
            if ($request->filled('client')) {
                $query->whereHas('reservation.utilisateur', function ($q) use ($request) {
                    $q->where('Prenom', 'like', '%' . $request->client . '%')
                    ->orWhere('Nom', 'like', '%' . $request->client . '%');
                });
            }

            // Filtre référence paiement
            if ($request->filled('reference')) {
                $query->where('Reference', 'like', '%' . $request->reference . '%');
            }

            // Filtre espace
            if ($request->filled('espace')) {
            $query->whereHas('reservation.espace', function ($q) use ($request) {
                $q->where('Id_Espace', $request->espace);
            });
        }        

        // Filtre mode de paiement
        if ($request->filled('mode')) {
            $query->whereHas('mode', function ($q) use ($request) {
                $q->where('Type_Mode', 'like', '%' . $request->mode . '%');
            });
        }

        // Filtre statut paiement
        if ($request->filled('statut')) {
            $query->where('statut_paiement', $request->statut);
        }

        // Filtre sur date de paiement
        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }

        // Filtrer uniquement les paiements avec statut payé ou partiel
        if (!$request->filled('statut')) {
            $query->whereIn('statut_paiement', ['paye', 'partiel']);
        }
        // Filtre par mois
        if ($request->filled('mois')) {
            $query->whereMonth('date_paiement', $request->mois);
        }

        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('date_paiement', $request->annee);
        }


        $paiements = $query->orderBy('created_at', 'desc')->get();
        $totalPaid = $paiements->sum('montant_payer');
        return view('admin.paiements.index', compact('paiements', 'espaces', 'totalPaid'));
    }
    

    public function payer(Request $request, $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
    
        // Paiement existant ou création
        $paiement = $reservation->paiement ?? Paiement::create([
            'Id_Reservation'  => $reservation->Id_Reservation,
            'Reference'       => $request->Reference ?? 'PAY-' . strtoupper(uniqid()),
            'Id_Mode'         => $request->Id_Mode ?? 1,
            'montant_payer'   => 0,
            'montant_Impayer' => $reservation->total,
            'statut_paiement' => 'en_attente',
            'date_paiement'   => $request->date_paiement ?? null,
        ]);
    
        // Mise à jour si formulaire soumis
        if ($request->filled('Reference') || $request->filled('date_paiement') || $request->filled('Id_Mode')) {
            $paiement->update([
                'Reference'     => $request->Reference ?? $paiement->Reference,
                'date_paiement' => $request->date_paiement ?? $paiement->date_paiement,
                'Id_Mode'       => $request->Id_Mode ?? $paiement->Id_Mode,
            ]);
        }
    
        // Si paiement en attente → on règle immédiatement
        if ($paiement->statut_paiement === 'en_attente') {
            $paiement->update([
                'montant_payer'   => $reservation->total,
                'montant_Impayer' => 0,
                'statut_paiement' => 'paye',
                'date_paiement'   => $paiement->date_paiement ?? now(),
            ]);
    
            $reservation->update(['Statut_Reservation' => 'payee']);
    
            // Génération PDF
            $pdf = Pdf::loadView('admin.paiements.facture', [
                'reservation' => $reservation,
                'paiement'    => $paiement,
            ]);
    
            $fileName = 'Facture_'.$paiement->Reference.'.pdf';
            $pdf->save(storage_path('app/public/factures/' . $fileName));
        }
    
        return redirect()
            ->route('admin.planning.profil')
            ->with('success', 'Paiement traité ! La facture est disponible.');
    }
    

    /*public function update(Request $request, $id)
        {
            $paiement = Paiement::findOrFail($id);
        
            $paiement->update([
                'Reference'      => $request->Reference ?? $paiement->Reference,
                'date_paiement'  => $request->date_paiement ?? $paiement->date_paiement,
                'Id_Mode'        => $request->Id_Mode ?? $paiement->Id_Mode,
            ]);
        
            return redirect()->back()->with('success', 'Paiement mis à jour avec succès.');
        }
        
    public function annuler($id)
        {
            $paiement = Paiement::findOrFail($id);
            $paiement->update(['statut_paiement' => 'refuse']);

            if ($paiement->reservation) {
                $paiement->reservation->update(['Statut_Reservation' => 'annulee']);
            }

            return redirect()->back()->with('error', 'Paiement annulé.');
        }*/
}

