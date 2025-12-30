<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur; 
use App\Models\Espace;
use App\Models\Reservation;
use App\Models\Equipement;
use App\Models\TypeClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // Formulaire de création
    public function create()
        {
            return view('admin.utilisateurs.create');
        }
    public function index(Request $request)
        {
            $query = Utilisateur::with('typeClient')
                ->where('Id_Profil', 4); // filtre obligatoire
        
            // Recherche texte (Prenom, Nom, Email, Numéro)
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('Prenom', 'like', "%$q%")
                        ->orWhere('Nom', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('numero', 'like', "%$q%");
                });
            }
        
            // 🏷 Type de client
            if ($request->filled('Id_Type_Client')) {
                $query->where('Id_Type_Client', $request->Id_Type_Client);
            }
        
            $utilisateurs = $query
                ->orderBy('Id_Utilisateur', 'desc')
                ->paginate(10)
                ->appends($request->query()); 
        
            $typeClients = TypeClient::all();
        
            return view('admin.utilisateurs.show', compact('utilisateurs', 'typeClients'));
        }
        
        
        
        

    // Stocker le nouvel utilisateur
    public function store(Request $request)
        {
            // Validation
            $request->validate([
                'Id_Espace' => 'required|exists:espaces,Id_Espace',
                'email_client' => 'required|email|exists:utilisateurs,email',
                'date_debut' => 'required|date',
                'heure_debut' => 'required',
                'duree' => 'required|numeric|min:1',
                'equipements' => 'array'
            ]);

            // Récupérer l'utilisateur existant
            $utilisateur = Utilisateur::where('email', $request->email_client)->firstOrFail();

            // Préparer la réservation
            $espace = Espace::findOrFail($request->Id_Espace);
            $debut = new \DateTime($request->date_debut . ' ' . $request->heure_debut);
            $fin = (clone $debut)->add(new \DateInterval('PT' . $request->duree . 'H'));

            // Vérifier si l'espace est déjà réservé sur ce créneau
            $existing = Reservation::where('Id_Espace', $espace->Id_Espace)
                ->whereIn('Statut_Reservation', ['en_attente', 'confirmee', 'terminee'])
                ->where(function ($query) use ($debut, $fin) {
                    $query->where('date_debut', '<', $fin)
                        ->where('date_fin', '>', $debut);
                })
                ->exists();

            if ($existing) {
                return back()->withErrors([
                    'date_debut' => 'Cet espace est déjà réservé sur ce créneau. Veuillez choisir un autre horaire.'
                ])->withInput();
            }

            // Calculer le total
            $total = $espace->tarif_horaire * $request->duree;
            if ($request->has('equipements')) {
                foreach ($request->equipements as $idEquip) {
                    $equipement = Equipement::find($idEquip);
                    if ($equipement) {
                        $total += $equipement->prix;
                    }
                }
            }

            // Créer la réservation
            $reservation = new Reservation();
            $reservation->Id_Utilisateur = $utilisateur->Id_Utilisateur;
            $reservation->Id_Espace = $espace->Id_Espace;
            $reservation->date_debut = $debut;
            $reservation->date_fin = $fin;
            $reservation->duree = $request->duree;
            $reservation->montant_total = $total;
            $reservation->Statut_Reservation = 'confirmee';
            $reservation->save();

            // Attacher les équipements
            if ($request->has('equipements')) {
                $reservation->equipements()->sync($request->equipements);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Réservation créée avec succès.');
        }

        public function NouveauUtilisateur(Request $request)
        {
            $validated = $request->validate([
                'Prenom'         => 'required|string|max:255',
                'Nom'            => 'required|string|max:255',
                'email'          => 'required|email|unique:utilisateurs,email',
                'numero'         => 'required|string|max:20|unique:utilisateurs,numero',
                'Entreprise'     => 'nullable|string|max:255',
                'Id_Type_Client' => 'nullable|exists:type_clients,Id_Type_Client',
            ]);

            // Le mot de passe sera automatiquement hashé grâce au mutateur dans le modèle
            Utilisateur::create([
                'Prenom'           => $validated['Prenom'],
                'Nom'              => $validated['Nom'],
                'email'            => $validated['email'],
                'numero'           => $validated['numero'],
                'Entreprise'       => $validated['Entreprise'],
                'password'         => $validated['numero'], 
                'date_inscription' => Carbon::today(),
                'Id_Profil'        => 4, 
                'Id_Type_Client'   => $validated['Id_Type_Client'],
            ]);

            return redirect('admin/reservations/create')
                ->with('success', 'Utilisateur créé avec succès ! Mot de passe par défaut = son numéro de téléphone.');
        }

        public function update(Request $request, $id)
        {
            $utilisateur = Utilisateur::findOrFail($id);
        
            // Validation
            $request->validate([
                'Prenom' => 'required|string|max:255',
                'Nom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email,' . $id . ',Id_Utilisateur',
                'numero' => 'required|string|max:20|unique:utilisateurs,numero,' . $id . ',Id_Utilisateur',
                'Entreprise' => 'nullable|string|max:255',
                'Id_Type_Client' => 'required|exists:type_clients,Id_Type_Client',
            ]);
        
            // Mise à jour
            $utilisateur->update([
                'Prenom' => $request->Prenom,
                'Nom' => $request->Nom,
                'email' => $request->email,
                'numero' => $request->numero,
                'Entreprise' => $request->Entreprise,
                'Id_Type_Client' => $request->Id_Type_Client,
            ]);
        
            // Si la requête est AJAX → réponse JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès',
                    'utilisateur' => $utilisateur
                ]);
            }
        
            // Sinon → redirect classique
            return redirect()->route('admin.utilisateurs.show')
                             ->with('success', 'Utilisateur mis à jour avec succès.');
        }
        
    public function destroy($id)
        {
            $utilisateur = Utilisateur::findOrFail($id);
            $utilisateur->delete();
        
            return redirect()->route('admin.utilisateurs.show')
                ->with('success', 'Utilisateur supprimé avec succès.');
        }

        public function updateProfile(Request $request)
        {
            $request->validate([
                'Id_Utilisateur' => 'required|exists:utilisateurs,Id_Utilisateur',
                'Prenom' => 'required|string|max:255',
                'Nom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email,' 
                            . $request->Id_Utilisateur . ',Id_Utilisateur',
                'numero' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            $user = Utilisateur::findOrFail($request->Id_Utilisateur);

            // Mise à jour des champs de base
            $user->Prenom = $request->Prenom;
            $user->Nom = $request->Nom;
            $user->email = $request->email;
            $user->numero = $request->numero;

            // Mise à jour du mot de passe si l'utilisateur en a fourni un
            if ($request->filled('password')) {
                $user->password = $request->password; 
            }

            $user->save(); // Sauvegarde tout

            // Mettre à jour la session
            session(['utilisateur' => $user]);

            return back()->with('success', 'Profil mis à jour avec succès');
        }

}
