@extends('admin.layout')
@section('title', 'Bienvenue')

@section('content')
<div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="text-center">
        <!-- Logo plus grand et avec effet -->
        <div class="mb-5">
            <img src="{{ asset('assets/img/ccia.png') }}" 
                 alt="Logo CCIA" 
                 class="img-fluid rounded-circle shadow-lg" 
                 style="height: 140px; width: 140px; border: 5px solid #fff;">
        </div>

        <!-- Message de bienvenue personnalisé -->
        <h1 class="display-4 fw-bold text-primary mb-3">
            Bienvenue !
        </h1>
        <p class="lead text-muted mb-5 px-4">
            Vous êtes connecté à la plateforme de gestion des réservations d'espaces de la CCIA.<br>
            Utilisez le menu latéral pour accéder aux différentes sections.
        </p>

        <!-- Petit appel à l'action discret -->
        <div class="mt-4">
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary btn-lg px-5">
                Commencer
            </a>
        </div>

        <!-- Optionnel : une illustration subtile en fond ou en bas -->
        <div class="mt-5 opacity-50">
            <img src="https://via.placeholder.com/600x200?text=Espaces+de+Réunion+&+Coworking" 
                 alt="Illustration" 
                 class="img-fluid rounded">
        </div>
    </div>
</div>
@endsection