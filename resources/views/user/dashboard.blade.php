@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
<div class="container">
    <h2>Bienvenue sur votre espace client</h2>

    @if(session('utilisateur'))
    <p>Bonjour, <strong>{{ session('utilisateur')->Prenom }} {{ session('utilisateur')->Nom }}</strong></p>
    <p>Numéro : {{ session('utilisateur')->numero }}</p>
    <p>Email : {{ session('utilisateur')->email ?? 'Non renseigné' }}</p>
    <p>Entreprise : {{ session('utilisateur')->Entreprise ?? 'Aucune' }}</p>
@else   
    <p><a href="{{ route('connexion.create') }}">Se connecter</a></p>
@endif

@endsection