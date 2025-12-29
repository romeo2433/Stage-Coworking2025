<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Equipement;
use App\Models\Utilisateur;
use App\Models\Abonnement;

class ReservationAdminController extends Controller
{
    public function create(Request $request)
    {
        $espaces = Espace::with(['equipements' => function($query) {
            $query->where('Etat', 'OK');
        }])->where('Statut', 'disponible')->get();
    
        $query = Utilisateur::where('Id_Profil', 4);
    
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Prenom', 'like', "%$search%")
                  ->orWhere('Nom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
    
        $utilisateurs = $query->paginate(10)->withQueryString();
    
        $abonnements = Abonnement::where('Status_Abonnement', 'actif')->get();
    
        return view('admin.reservations.create', compact('espaces','utilisateurs','abonnements'));
    }
    
    

   // 1. MÉTHODE STORE  CORRIGÉE AVEC GESTION DE LA QUANTITÉ
   public function store(Request $request)
    {
        $request->validate([
            'Id_Espace' => 'required|exists:espaces,Id_Espace',
            'email_client' => 'required|email|exists:utilisateurs,email',
            'date_debut' => 'required|date',
            'heure_debut' => 'required',
            'duree' => 'required|numeric|min:1',
            'quantite_reservation' => 'required|integer|min:1',
            'duree_jour' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|numeric|min:0',
            'equipements' => 'array',
            'quantite_equipements' => 'array', // tableau des quantités par équipement
            'quantite_equipements.*' => 'integer|min:1',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email_client)->firstOrFail();
        $espace = Espace::findOrFail($request->Id_Espace);
        $quantite = $request->quantite_reservation;

        // === Calcul dates et prix de base ===
        $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);

        if ($request->Id_Abonnement) {
            $abo = Abonnement::find($request->Id_Abonnement);

            if ($abo->Type_Abonnement === 'journalier') {
                $duree = max(1, $request->duree_jour ?? 1);
                $fin = (clone $debut)->add(new \DateInterval("P{$duree}D"));
                $total = ($espace->tarif_journalier * $duree) * $quantite;
            } elseif ($abo->Type_Abonnement === 'mensuel') {
                $duree = max(1, $request->duree_mois ?? 1);
                $fin = (clone $debut)->add(new \DateInterval("P{$duree}M"));
                $total = ($espace->tarif_mensuel * $duree) * $quantite;
            } else {
                $duree = $request->duree;
                $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
                $total = ($espace->tarif_horaire * $duree) * $quantite;
            }
        } else {
            $duree = $request->duree;
            $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
            $total = ($espace->tarif_horaire * $duree) * $quantite;
        }

        // === Vérification disponibilité places (tu la gardes, c'est important) ===
        $reservationsChevauchantes = Reservation::where('Id_Espace', $espace->Id_Espace)
            ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee'])
            ->where(function ($q) use ($debut, $fin) {
                $q->where('date_debut', '<', $fin)->where('date_fin', '>', $debut);
            })
            ->sum('quantite_reservation');

        if ($reservationsChevauchantes + $quantite > $espace->quantite) {
            return back()->withErrors([
                'quantite' => "Il reste seulement " . ($espace->quantite - $reservationsChevauchantes) . " place(s)."
            ])->withInput();
        }

        // === Calcul prix équipements + préparation données pivot ===
        $totalEquipements = 0;
        $equipementsData = []; // pour sync avec Nombre_Ajout

        if ($request->has('equipements') && is_array($request->equipements)) {
            foreach ($request->equipements as $idEquip) {
                $idEquip = (int)$idEquip;
                $quantiteEq = (int)($request->quantite_equipements[$idEquip] ?? 1); // quantité choisie

                $equip = Equipement::find($idEquip);
                if ($equip) {
                    // Ajout au total
                    $totalEquipements += $equip->prix * $quantiteEq;

                    // Préparation pour la table pivot
                    $equipementsData[$idEquip] = ['Nombre_Ajout' => $quantiteEq];
                }
            }
        }

        $total += $totalEquipements;

        // === Création réservation ===
        $dernierNumero = Reservation::max('Id_Reservation');
        $prochainNumero = $dernierNumero ? $dernierNumero + 1 : 1;
        $reference = 'RES-' . $prochainNumero;

        $reservation = Reservation::create([
            'Reference' => $reference,
            'Id_Espace' => $espace->Id_Espace,
            'Id_Utilisateur' => $utilisateur->Id_Utilisateur,
            'Id_Abonnement' => $request->Id_Abonnement,
            'date_debut' => $debut,
            'date_fin' => $fin,
            'duree_heures' => $duree,
            'quantite_reservation' => $quantite,
            'total' => $total,
            'Statut_Reservation' => 'confirmee',
            'Id_Type' => 1,
            'created_by' => session('utilisateur')->Id_Utilisateur,
        ]);

        // === Sauvegarde des équipements avec quantité (Nombre_Ajout) ===
        if (!empty($equipementsData)) {
            $reservation->equipements()->sync($equipementsData);
        }

        return redirect()->route('admin.reservations.index')
            ->with('success', "Réservation créée avec succès ! Réf : {$reference}");
    }
   
   public function espaceDetails($id)
        {
            $espace = Espace::with('equipements')->find($id);
            if (!$espace) {
                return response()->json(['error' => 'Espace introuvable'], 404);
            }
            return response()->json([
                'tarif_horaire'     => $espace->tarif_horaire ?? 0,
                'tarif_journalier'  => $espace->tarif_journalier ?? 0,   
                'tarif_mensuel'     => $espace->tarif_mensuel ?? 0,      
                'quantite'          => $espace->quantite ?? 1,
                'equipements'       => $espace->equipements->map(function($e) {
                    return [
                        'Id_Equipement' => $e->Id_Equipement,
                        'nom'           => $e->nom,
                        'prix'          => $e->prix
                    ];
                })
            ]);
        }

   // 2. MÉTHODE heuresDisponibles → CORRIGÉE AVEC QUANTITÉ
    public function heuresDisponibles($id, Request $request)
        {
            $date = $request->query('date');
            if (!$date) {
                return response()->json(['error' => 'Date manquante'], 400);
            }

            $espace = Espace::findOrFail($id);
            $heuresPossibles = ['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00'];

            // Récupérer toutes les réservations du jour
            $reservations = Reservation::where('Id_Espace', $id)
                ->whereDate('date_debut', $date)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee','payee'])
                ->get();

            $heuresBloquees = [];

            foreach ($heuresPossibles as $heure) {
                $debutTest = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $heure);
                $finTest   = $debutTest->copy()->addHour();

                $count = $reservations->filter(function ($r) use ($debutTest, $finTest) {
                    return \Carbon\Carbon::parse($r->date_debut) < $finTest &&
                           \Carbon\Carbon::parse($r->date_fin) > $debutTest;
                })->sum('quantite_reservation');  
                

                // Si le nombre de réservations chevauchantes >= quantité → heure bloquée
                if ($count >= $espace->quantite) {
                    $heuresBloquees[] = $heure;
                }
            }

            $heuresDisponibles = array_diff($heuresPossibles, $heuresBloquees);

            return response()->json(['disponibles' => array_values($heuresDisponibles)]);
        }



        public function preview(Request $request)
{
    // Validation (on ajoute quantite_equipements comme dans store)
    $request->validate([
        'Id_Espace'            => 'required|exists:espaces,Id_Espace',
        'email_client'         => 'required|email|exists:utilisateurs,email',
        'date_debut'           => 'required|date',
        'heure_debut'          => 'required',
        'duree'                => 'required|numeric|min:1',
        'duree_jour'           => 'nullable|numeric|min:0',
        'duree_mois'           => 'nullable|numeric|min:0',
        'quantite_reservation' => 'required|integer|min:1',
        'equipements'          => 'array',
        'quantite_equipements' => 'array',
        'quantite_equipements.*' => 'integer|min:1',
    ]);

    $quantite = $request->quantite_reservation;
    $utilisateur = Utilisateur::where('email', $request->email_client)->firstOrFail();
    $espace = Espace::with('equipements')->findOrFail($request->Id_Espace);
    $abonnement = $request->Id_Abonnement ? Abonnement::find($request->Id_Abonnement) : null;

    // === Calcul des dates et du prix de base (inchangé) ===
    $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);

    if ($abonnement) {
        if ($abonnement->Type_Abonnement === 'journalier') {
            $duree = max(1, $request->duree_jour ?? 1);
            $fin = (clone $debut)->add(new \DateInterval("P{$duree}D"));
            $total = ($espace->tarif_journalier * $duree) * $quantite;
        } elseif ($abonnement->Type_Abonnement === 'mensuel') {
            $duree = max(1, $request->duree_mois ?? 1);
            $fin = (clone $debut)->add(new \DateInterval("P{$duree}M"));
            $total = ($espace->tarif_mensuel * $duree) * $quantite;
        } else {
            $duree = $request->duree;
            $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
            $total = ($espace->tarif_horaire * $duree) * $quantite;
        }
    } else {
        $duree = $request->duree;
        $fin = (clone $debut)->add(new \DateInterval("PT{$duree}H"));
        $total = ($espace->tarif_horaire * $duree) * $quantite;
    }

    // === Équipements avec quantité individuelle ===
    $equipementsSelectionnes = [];
    $prixEquipements = 0;

    if ($request->has('equipements') && is_array($request->equipements)) {
        foreach ($request->equipements as $idEquip) {
            $idEquip = (int)$idEquip;

            // Récupère la quantité choisie (1 par défaut si pas envoyé)
            $quantiteEq = (int)($request->quantite_equipements[$idEquip] ?? 1);

            $equip = Equipement::find($idEquip);
            if ($equip) {
                $sousTotal = $equip->prix * $quantiteEq;

                $equipementsSelectionnes[] = [
                    'id'           => $equip->Id_Equipement,
                    'nom'          => $equip->nom,
                    'prix_unitaire' => $equip->prix,
                    'quantite'     => $quantiteEq,
                    'sous_total'   => $sousTotal,
                ];
                $prixEquipements += $sousTotal;
            }
        }
    }

    $total += $prixEquipements;

    // === Vérification disponibilité places (inchangée) ===
    $reservationsChevauchantes = Reservation::where('Id_Espace', $espace->Id_Espace)
        ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee'])
        ->where(function ($q) use ($debut, $fin) {
            $q->where('date_debut', '<', $fin)->where('date_fin', '>', $debut);
        })
        ->sum('quantite_reservation');

    $placesPrises = $reservationsChevauchantes;
    $placesTotales = $espace->quantite;
    $placesRestantes = $placesTotales - $placesPrises;

    if ($quantite > $placesRestantes) {
        return redirect()->route('admin.reservations.create')
            ->withErrors(['quantite' => "Il reste seulement $placesRestantes place(s) disponible(s)."])
            ->withInput();
    }

    $disponible = ($placesRestantes >= $quantite);

    // === Retour à la vue ===
    return view('admin.reservations.preview', compact(
        'utilisateur', 'espace', 'abonnement', 'debut', 'fin', 'duree',
        'quantite', 'total', 'equipementsSelectionnes', 'prixEquipements',
        'disponible', 'placesPrises', 'placesTotales', 'placesRestantes', 'request'
    ));
}
    public function checkDispo(Request $request)
        {
            $espace = Espace::findOrFail($request->Id_Espace);
            $debut = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->date_debut . ' ' . $request->heure_debut);
            $fin   = $debut->copy()->addHours($request->duree);

            $count = Reservation::where('Id_Espace', $espace->Id_Espace)
                ->whereIn('Statut_Reservation', ['en_attente','confirmee','terminee'])
                ->where('date_debut', '<', $fin)
                ->where('date_fin', '>', $debut)
                ->count();

            return response()->json([
                'disponible' => $count < $espace->quantite,
                'prises' => $count,
                'total' => $espace->quantite,
                'restantes' => $espace->quantite - $count
            ]);
        }

        public function placesRestantes(Request $request, $id)
            {
                $espace = Espace::findOrFail($id);

                $date  = $request->query('date');            // ex: '2025-12-10'
                $heure = $request->query('heure');           // ex: '09:00'
                $duree = intval($request->query('duree', 1)); // par défaut 1 heure

                // Si date ou heure manquent, renvoyer la capacité totale
                if (!$date || !$heure) {
                    return response()->json([
                        'totale'    => (int) $espace->quantite,
                        'reservee'  => 0,
                        'restante'  => (int) $espace->quantite,
                    ]);
                }

                // Construire début/fin en Carbon (vérifier format)
                try {
                    $debut = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "$date $heure");
                } catch (\Exception $e) {
                    // Si le format est incorrect, renvoyer la capacité totale (ou une erreur selon ton choix)
                    return response()->json([
                        'totale'    => (int) $espace->quantite,
                        'reservee'  => 0,
                        'restante'  => (int) $espace->quantite,
                        'error'     => 'Format date/heure invalide'
                    ], 400);
                }

                $fin = $debut->copy()->addHours($duree);

                // Somme des quantités des réservations qui CHEVAUCHENT [debut, fin)
                $quantiteReservee = Reservation::where('Id_Espace', $id)
                    ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee'])
                    ->where('date_debut', '<', $fin)
                    ->where('date_fin', '>', $debut)
                    ->sum('quantite_reservation');

                $quantiteTotale = (int) $espace->quantite;
                $placesRestantes = max(0, $quantiteTotale - (int) $quantiteReservee);

                return response()->json([
                    'totale'   => $quantiteTotale,
                    'reservee' => (int) $quantiteReservee,
                    'restante' => (int) $placesRestantes,
                ]);
            }        
}