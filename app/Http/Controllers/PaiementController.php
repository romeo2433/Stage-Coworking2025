<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    public function create($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $modes = Mode::all();

        return view('paiements.create', compact('reservation', 'modes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Id_Reservation' => 'required|exists:reservations,Id_Reservation',
            'Id_Mode' => 'required|exists:mode,Id_Mode',
            'montant_payer' => 'required|numeric|min:0',
        ]);

        $reservation = Reservation::findOrFail($request->Id_Reservation);
        $mode = Mode::findOrFail($request->Id_Mode);
        $typeMode = mb_strtolower($mode->Type_Mode);

        if ($typeMode === 'espèces') {
            // ✅ Cas espèces → en attente d'approbation admin
            Paiement::create([
                'Reference' => 'PAY-' . strtoupper(Str::random(6)),
                'montant_payer' => 0, // pas encore payé
                'montant_Impayer' => $reservation->total,
                'date_paiement' => now()->toDateString(),
                'statut_paiement' => 'en_attente',
                'Id_Reservation' => $reservation->Id_Reservation,
                'Id_Mode' => $mode->Id_Mode,
            ]);

            // ✅ Réservation en attente de paiement
            $reservation->Statut_Reservation = 'en_attente_de_paiement';
            $reservation->save();
        } else {
            // ✅ Autres modes : paiement immédiat
            $montantPayer = $request->montant_payer;
            $paiement = Paiement::create([
                'Reference' => 'PAY-' . strtoupper(Str::random(6)),
                'montant_payer' => $montantPayer,
                'montant_Impayer' => max(0, $reservation->total - $montantPayer),
                'date_paiement' => now()->toDateString(),
                'statut_paiement' => $montantPayer >= $reservation->total ? 'paye' : 'partiel',
                'Id_Reservation' => $reservation->Id_Reservation,
                'Id_Mode' => $mode->Id_Mode,
            ]);

            $reservation->Statut_Reservation = $paiement->montant_Impayer == 0
                ? 'terminee'
                : 'partiellement_payee';
            $reservation->save();
        }

        return redirect()->route('reservations.my')
                         ->with('success', 'Votre paiement a été enregistré. Merci !');
    }
}
