@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            @if(session('utilisateur'))
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Header coloré avec avatar -->
                    <div class="card-header bg-primary text-white text-center py-5 position-relative">
                        <h3 class="mb-1 fw-bold">Bienvenue dans votre espace client</h3>
                        <p class="mb-0 opacity-90">Gérez vos réservations et vos informations personnelles</p>
                    </div>

                    <!-- Corps de la carte -->
                    <div class="card-body p-5">
                        <h4 class="text-primary mb-4">
                            Bonjour, <strong>{{ session('utilisateur')->Prenom }} {{ session('utilisateur')->Nom }}</strong> !
                        </h4>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone-fill text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Numéro de téléphone</small>
                                        <p class="mb-0 fw-semibold">{{ session('utilisateur')->numero }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Email</small>
                                        <p class="mb-0 fw-semibold">{{ session('utilisateur')->email ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-building-fill text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Entreprise</small>
                                        <p class="mb-0 fw-semibold">{{ session('utilisateur')->Entreprise ?? 'Aucune' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <h3 class="text-muted mb-4">Vous n'êtes pas connecté</h3>
                    <a href="{{ route('connexion.create') }}" class="btn btn-primary btn-lg px-5">
                        Se connecter
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .avatar-circle img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 5px solid rgba(255,255,255,0.3);
    }
</style>
@endsection