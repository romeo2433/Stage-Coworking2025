@extends('layouts.app')

@section('title', 'Types d\'Espaces de Coworking')

@section('content')
<div class="container py-5">
    <!-- TITRE PRINCIPAL -->
    <h1 class="display-5 text-center fw-bold text-dark mb-5">
        Nos Espaces de Coworking
    </h1>

    <!-- BARRE DE FILTRES FIXE -->
    <div class="filters-bar d-flex flex-wrap justify-content-center gap-3 mb-5 py-4 shadow-sm">
        @foreach($types as $type)
            <button type="button"
                    class="btn btn-filter type-filter px-4 py-2 rounded-pill"
                    data-type-id="{{ $type->Id_Type }}">
                {{ $type->Type_Espace }}
            </button>
        @endforeach
        <button type="button" class="btn btn-filter type-filter px-4 py-2 rounded-pill active" data-type-id="all">
            Tous les espaces
        </button>
    </div>

    <!-- CONTENEUR DES ESPACES -->
    <div id="espaces-container" class="mt-5">
        @foreach($types as $type)
            <section class="type-section mb-6" id="type-{{ $type->Id_Type }}"
                     style="{{ !$loop->first ? 'display: none;' : '' }}">
                <h2 class="text-center fw-bold text-primary mb-5 position-relative d-inline-block px-4">
                    {{ $type->Type_Espace }}
                    <span class="underline-title"></span>
                </h2>

                @if($type->espaces->isEmpty())
                    <p class="text-center text-muted fst-italic py-5">Aucun espace disponible pour ce type actuellement.</p>
                @else
                    <div class="row g-4 g-xl-5 justify-content-center">
                        @foreach($type->espaces as $espace)
                            <div class="col-md-6 col-lg-4">
                                <div class="espace-card card h-100 border-0 shadow-lg rounded-4 overflow-hidden position-relative">
                                    <!-- Zone image avec overlay limité à l'image -->
                                    <div class="image-wrapper position-relative overflow-hidden">
                                        <img src="{{ $espace->photo ? asset('storage/espaces/' . $espace->photo) : asset('images/default.jpg') }}"
                                             class="card-img-top w-100"
                                             alt="{{ $espace->Nom }}"
                                             style="height: 240px; object-fit: cover; transition: transform 0.4s ease;">
                                
                                        <!-- Overlay uniquement sur l'image -->
                                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-4">
                                            <div class="overlay-content text-white">
                                                <p class="mb-2"><strong>Capacité :</strong> {{ $espace->capacite }} personnes</p>
                                                <p class="mb-2"><strong>Tarif horaire :</strong> {{ number_format($espace->tarif_horaire, 0) }} Ar</p>
                                                <p class="mb-2"><strong>Tarif journalier :</strong> {{ number_format($espace->tarif_journalier, 0) }} Ar</p>
                                                <p class="mb-3"><strong>Tarif mensuel :</strong> {{ number_format($espace->tarif_mensuel, 0) }} Ar</p>
                                                <p class="mb-3">
                                                    <strong>Statut :</strong>
                                                    <span class="badge rounded-pill {{ $espace->Statut == 'disponible' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ ucfirst($espace->Statut) }}
                                                    </span>
                                                </p>
                                
                                                @if($espace->equipements->isNotEmpty())
                                                    <div>
                                                        <strong>Équipements :</strong>
                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            @foreach($espace->equipements as $equipement)
                                                                <span class="badge bg-light text-dark rounded-pill">
                                                                    {{ $equipement->nom }}
                                                                    @if($equipement->pivot && $equipement->pivot->Nombre_Equipements > 1)
                                                                        (x{{ $equipement->pivot->Nombre_Equipements }})
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- Corps de la carte (toujours cliquable) -->
                                    <div class="card-body text-center py-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-bold text-dark mb-3">{{ $espace->Nom }}</h5>
                                        </div>
                                        <a href="{{ route('reservations.create', ['Id_Espace' => $espace->Id_Espace]) }}"
                                           class="btn btn-primary rounded-pill px-5 py-2 shadow-sm mt-auto">
                                            Réserver cet espace
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</div>

<!-- CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="/css/types-espaces.css">
<script src="/js/types-espaces.js"></script>
@endsection