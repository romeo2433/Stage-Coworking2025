@extends('admin.layout')

@section('title', 'Détails de l’espace')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="{{ $espace->photo ? asset('storage/espaces/' . $espace->photo) : asset('images/default.jpg') }}"
                     class="img-fluid rounded-start h-100 w-100" 
                     alt="{{ $espace->Nom }}" 
                     style="object-fit: cover;">
            </div>
            <div class="col-md-7">
                <div class="card-body p-4">
                    <h3 class="fw-bold text-primary mb-3">{{ $espace->Nom }}</h3>
                    <p><strong>Statut :</strong> 
                        <span class="badge bg-{{ $espace->Statut === 'disponible' ? 'success' : 'danger' }}">
                            {{ ucfirst($espace->Statut) }}
                        </span>
                    </p>

                    <p><strong>Type d’espace :</strong> {{ $espace->type->Type_Espace ?? 'Non défini' }}</p>
                    <p><strong>Capacité :</strong> {{ $espace->capacite }} personnes</p>
                    <p><strong>Tarif horaire :</strong> {{ number_format($espace->tarif_horaire, 0, ',', ' ') }} Ar</p>
                    <p><strong>Tarif journalier :</strong> {{ number_format($espace->tarif_journalier, 0, ',', ' ') }} Ar</p>
                    <p><strong>Tarif mensuel :</strong> {{ number_format($espace->tarif_mensuel, 0, ',', ' ') }} Ar</p>

                    <hr>

                    <h5 class="fw-bold text-secondary mt-3 mb-2">Équipements inclus :</h5>
                    @if($espace->equipements->isEmpty())
                        <p class="text-muted fst-italic">Aucun équipement disponible pour cet espace.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($espace->equipements as $equipement)
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    {{ $equipement->nom }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.espaces.photos') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@endsection
