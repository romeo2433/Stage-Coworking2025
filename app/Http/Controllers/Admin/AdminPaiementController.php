<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;

class AdminPaiementController extends Controller
{
    public function index()
    {
        $paiements = Paiement::with('reservation.utilisateur', 'mode')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.paiements.index', compact('paiements'));
    }

    public function payer($id)
    {
        $paiement = Paiement::findOrFail($id);

        // Vérifie que le paiement est en attente et en espèces
        if ($paiement->mode?->Type_Mode === 'Espèces' && $paiement->statut_paiement === 'en_attente') {
            $paiement->update([
                'montant_payer' => $paiement->reservation->total, 
                'montant_Impayer' => 0,                           
                'statut_paiement' => 'paye',
                'date_paiement' => now(),
            ]);

        // Mettre à jour le statut de la réservation
        if ($paiement->reservation) {
            $paiement->reservation->update(['Statut_Reservation' => 'terminee']);
        }
        }
        return redirect()->route('admin.paiements.index')
                        ->with('success', 'Paiement marqué comme payé.');
    }


    public function annuler($id)
    {
        $paiement = Paiement::findOrFail($id);
        $paiement->update(['statut_paiement' => 'refuse']);

        if ($paiement->reservation) {
            $paiement->reservation->update(['Statut_Reservation' => 'annulee']);
        }

        return redirect()->back()->with('error', 'Paiement annulé.');
    }
}

