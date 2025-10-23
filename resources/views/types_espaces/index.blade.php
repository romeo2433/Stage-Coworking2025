@extends('layouts.app')

@section('title', 'Types d\'Espaces de Coworking')

@section('content')
<div class="container py-5">

    <!-- TITRE PRINCIPAL -->
    <h2 class="mb-5 text-center fw-bold text-uppercase text-primary">
        Espaces Disponibles
    </h2>

   <!-- BOUTONS DE FILTRE -->
    <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 filters-bar">
        @foreach($types as $type)
            <button type="button" 
                    class="btn btn-outline-primary btn-lg px-4 py-2 type-filter" 
                    data-type-id="{{ $type->Id_Type }}">
                {{ $type->Type_Espace }}
            </button>
        @endforeach
        <button type="button" class="btn btn-primary btn-lg px-4 py-2 type-filter" data-type-id="all">
            Tous les espaces
        </button>
    </div>


    <!-- CONTENEUR DES ESPACES -->
    <div id="espaces-container">
        @foreach($types as $type)
            <div class="type-section mb-5" id="type-{{ $type->Id_Type }}" 
                 style="{{ !$loop->first ? 'display: none;' : '' }}">

                 <h3 class="mb-4 text-center fw-bold text-primary border-bottom border-3 pb-2">
                    {{ $type->Type_Espace }}
                </h3>
                
                
                @if($type->espaces->isEmpty())
                    <p class="text-center text-muted fst-italic">Aucun espace disponible pour ce type.</p>
                @else
                    <div class="row g-4">
                        @foreach($type->espaces as $espace)
                        <div class="col-md-6 col-lg-4">
                            <div class="card espace-card h-100 shadow-sm border-0 animate-card">
                                <div class="image-container">
                                    <img src="{{ $espace->photo 
                                        ? asset('storage/espaces/' . $espace->photo) 
                                        : asset('images/default.jpg') }}" 
                                        class="card-img-top" 
                                        alt="{{ $espace->Nom }}"
                                        style="height: 220px; object-fit: cover; border-radius: 10px 10px 0 0;">
                                    
                                    <div class="overlay">
                                        <div class="overlay-content">
                                            <p><strong>Capacité :</strong> {{ $espace->capacite }} personnes</p>
                                            <p><strong>Tarif journalier :</strong> {{ number_format($espace->tarif_journalier, 2) }} Ar</p>
                                            <p><strong>Tarif mensuel :</strong> {{ number_format($espace->tarif_mensuel, 2) }} Ar</p>
                                            <p><strong>Statut :</strong> 
                                                <span class="badge {{ $espace->Statut == 'disponible' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($espace->Statut) }}
                                                </span>
                                            </p>
                        
                                            @if($espace->equipements->isNotEmpty())
                                                <div class="mt-2">
                                                    <strong>Équipements :</strong>
                                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                                        @foreach($espace->equipements as $equipement)
                                                            <span class="badge bg-info text-dark">
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
                        
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold">{{ $espace->Nom }}</h5>
                                    <p class="text-muted mb-0">
                                        <strong>Tarif horaire :</strong> {{ number_format($espace->tarif_horaire, 2) }} Ar
                                    </p>
                                    <a href="{{ route('reservations.create', ['Id_Espace' => $espace->Id_Espace]) }}" 
                                        class="btn btn-primary w-100 mt-3">
                                         Réserver
                                     </a>
                                </div>
                            </div>
                        </div>
                                             
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- ✅ Liens CSS et JS externes --}}
<link rel="stylesheet" href="{{ asset('css/types-espaces.css') }}">
<script src="{{ asset('js/types-espaces.js') }}"></script>

{{-- CDN pour les animations --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection
