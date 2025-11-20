@extends('admin.layout')

@section('title', 'Gestion des espaces')

@section('content')

<div class="container py-2" style="max-width:900px;">

    <h4 class="text-center fw-bold text-primary text-uppercase mb-2" style="font-size:1.1rem;">
        Gestion des espaces
    </h4>

    {{-- Boutons --}}
    <div class="d-flex justify-content-end gap-1 mb-2">
        <a href="{{ route('admin.types.indexe') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-layer-group"></i> Types
        </a>
        <a href="{{ route('admin.espaces.create') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-plus"></i> Nouvel espace
        </a>
        <a href="{{ route('admin.equipements.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-tools"></i> Équipements
        </a>
    </div>

    {{-- Aucun type --}}
    @if($types->isEmpty())
        <p class="text-center text-muted small">Aucun type d’espace trouvé.</p>
    @endif

    {{-- AFFICHAGE PAR TYPE --}}
    @foreach($types as $type)
        <div class="mt-3">
            <h5 class="fw-bold text-secondary mb-2" style="font-size:1rem;">
                <i class="fas fa-folder-open text-primary"></i> {{ $type->Type_Espace }}
            </h5>

            {{-- Si aucun espace dans ce type --}}
            @if($type->espaces->isEmpty())
                <p class="text-muted small ps-4">Aucun espace pour ce type.</p>
            @else
                <div class="row g-2 ps-2">
                    @foreach($type->espaces as $espace)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-2 h-100">

                            {{-- Photo --}}
                            <img src="{{ $espace->photo 
                                ? asset('storage/espaces/' . $espace->photo)
                                : asset('images/no-photo.png') }}"
                                class="card-img-top rounded-top-2"
                                style="height:120px; object-fit:cover;"
                                alt="Aucune photo">

                            <div class="card-body p-2">

                                <h6 class="fw-bold text-primary mb-1">{{ $espace->Nom }}</h6>

                                <span class="badge bg-{{ $espace->Statut === 'disponible' ? 'success' : 'danger' }} mb-1">
                                    {{ ucfirst($espace->Statut) }}
                                </span>

                                <p class="small mb-1">
                                    <strong>Capacité :</strong> {{ $espace->capacite }} pers
                                </p>

                                {{-- Formulaire de changement photo --}}
                                <form action="{{ route('admin.espaces.updatePhoto', $espace->Id_Espace) }}"
                                      method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <input type="file" name="photo" accept="image/*"
                                           class="form-control form-control-sm mb-1" required>

                                    <button class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-image"></i> Mettre à jour
                                    </button>
                                </form>

                                {{-- Boutons --}}
                                <div class="d-flex gap-1 mt-1">
                                    <a href="{{ route('admin.espaces.edit', $espace->Id_Espace) }}" 
                                       class="btn btn-primary btn-sm w-50">
                                        Modifier
                                    </a>

                                    <form action="{{ route('admin.espaces.destroy', $espace->Id_Espace) }}"
                                          method="POST" class="w-50"
                                          onsubmit="return confirm('Supprimer cet espace ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm w-100">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
