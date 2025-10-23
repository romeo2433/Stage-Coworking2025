<?php

namespace App\Http\Controllers\InscriptionLogin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;

class ConnexionController extends Controller
{
    // Afficher le formulaire de connexion
    public function create()
    {
        return view('connexion.create');
    }

    // Traitement de la connexion
    public function store(Request $request)
    {
        $validated = $request->validate([
            'identifiant' => 'required|string|max:100',
            'password' => 'required|string|min:3',
        ]);

        // Rechercher l'utilisateur par numéro ou email
        $utilisateur = Utilisateur::where('numero', $validated['identifiant'])
                                  ->orWhere('email', $validated['identifiant'])
                                  ->first();

        if (!$utilisateur) {
            return back()->with('error', 'Numéro ou email non reconnu.')->withInput();
        }

        if ($utilisateur->password !== $validated['password']) {
            return back()->with('error', 'Mot de passe incorrect.')->withInput();
        }

        // Stocker la session avec des noms cohérents (minuscules)
        session([
            'utilisateur' => $utilisateur,
            'profil' => $utilisateur->Id_Profil,
        ]);

        // Redirection selon le profil
        if ($utilisateur->Id_Profil == 1) {
            return redirect()->route('admin.dashboard')->with('success', 'Bienvenue, administrateur !');
        } else {
            return redirect()->route('user.dashboard')->with('success', 'Connexion réussie !');
        }
    }

    // Déconnexion
    public function logout()
    {
        session()->forget('utilisateur');
        session()->forget('profil');
        return redirect()->route('connexion.create')->with('success', 'Déconnexion réussie !');
    }
}
