<?php

namespace App\Http\Controllers\InscriptionLogin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

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
            'password'    => 'required|string|min:3',
        ]);

        // Rechercher l'utilisateur par numéro OU email
        $utilisateur = Utilisateur::where('numero', $validated['identifiant'])
                                  ->orWhere('email', $validated['identifiant'])
                                  ->first();

        // Si utilisateur non trouvé OU mot de passe incorrect
       if (!$utilisateur || 
    (!Hash::check($validated['password'], $utilisateur->password) && 
     $utilisateur->password !== $validated['password'])) {
    
    return back()
        ->withErrors(['identifiant' => 'Identifiant ou mot de passe incorrect.'])
        ->withInput($request->only('identifiant'));
}

        // Connexion réussie → mise en session
        session([
            'utilisateur' => $utilisateur,
            'profil'      => $utilisateur->Id_Profil,
        ]);

        // Redirection selon le profil
        if (in_array($utilisateur->Id_Profil, [1, 3])) { // Admin ou super admin
            return redirect()->route('admin.dashboard')
                ->with('success', 'Bienvenue dans l’espace administrateur !');
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Connexion réussie ! Bienvenue !');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        session()->forget(['utilisateur', 'profil']);
        // Optionnel : régénérer le token de session pour plus de sécurité
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('connexion.create')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}