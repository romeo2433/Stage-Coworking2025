@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-primary">Bienvenue sur le tableau de bord administrateur </h2>
    <p>
        Bonjour {{ session('Utilisateur')->Prenom ?? '' }} {{ session('Utilisateur')->Nom ?? '' }}    
    </p>
    <a href="{{ route('logout') }}" class="btn btn-danger mt-3">Déconnexion</a>
</div>

@endsection