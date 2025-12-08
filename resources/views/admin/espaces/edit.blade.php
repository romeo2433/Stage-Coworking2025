@extends('admin.layout')

@section('title', 'Modifier un espace')

@section('content')
<div class="container mt-3" style="max-width: 750px;"> {{-- Réduction de la largeur --}}
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">

            <h4 class="fw-bold text-primary text-center mb-3" style="font-size:1.1rem;">
                Modifier l’espace
            </h4>

            {{-- Erreurs --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">

                {{-- PHOTO --}}
                <div class="col-md-4 text-center">
                    <img src="{{ $espace->photo 
                        ? asset('storage/espaces/' . $espace->photo)
                        : asset('images/no-photo.png') }}"
                        class="img-fluid rounded shadow-sm"
                        style="height:160px; width:100%; object-fit:cover;"
                        alt="Aucune photo">

                    <p class="small text-muted mt-2 mb-1">Photo actuelle</p>

                    <a href="{{ route('admin.espaces.photos') }}" class="btn btn-sm btn-secondary w-100">
                        Changer la photo
                    </a>
                </div>

                {{-- FORMULAIRE --}}
                <div class="col-md-8">
                    <form action="{{ route('admin.espaces.update', $espace->Id_Espace) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-2">

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Nom</label>
                                <input type="text" name="Nom" class="form-control form-control-sm" 
                                       value="{{ old('Nom', $espace->Nom) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Statut</label>
                                <select name="Statut" class="form-select form-select-sm" required>
                                    <option value="disponible" {{ $espace->Statut == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="Fermé" {{ $espace->Statut == 'Fermé' ? 'selected' : '' }}>Fermé</option>
                                    <option value="maintenance" {{ $espace->Statut == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Type d’espace</label>
                                <select name="Id_Type" class="form-select form-select-sm" required>
                                    @foreach($types as $type)
                                        <option value="{{ $type->Id_Type }}" 
                                            {{ $espace->Id_Type == $type->Id_Type ? 'selected' : '' }}>
                                            {{ $type->Type_Espace }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Capacité</label>
                                <input type="number" name="capacite" min="1"
                                       class="form-control form-control-sm"
                                       value="{{ old('capacite', $espace->capacite) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Quantité</label>
                                <input type="number" name="quantite" value="{{ $espace->quantite }}" 
                                       class="form-control form-control-sm" min="1" required>
                            </div>
                            

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Horaire (Ar)</label>
                                <input type="number" name="tarif_horaire"
                                       class="form-control form-control-sm"
                                       value="{{ old('tarif_horaire', $espace->tarif_horaire) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Journalier (Ar)</label>
                                <input type="number" name="tarif_journalier"
                                       class="form-control form-control-sm"
                                       value="{{ old('tarif_journalier', $espace->tarif_journalier) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Mensuel (Ar)</label>
                                <input type="number" name="tarif_mensuel"
                                       class="form-control form-control-sm"
                                       value="{{ old('tarif_mensuel', $espace->tarif_mensuel) }}" required>
                            </div>

                            {{-- ÉQUIPEMENTS --}}
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Équipements disponibles</label>
                                <div class="border rounded p-2 small" style="max-height:150px; overflow-y:auto;">
                                    @foreach($equipements as $equipement)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="equipements[]" 
                                                value="{{ $equipement->Id_Equipement }}"
                                                {{ $espace->equipements->contains('Id_Equipement', $equipement->Id_Equipement) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $equipement->nom }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-success btn-sm px-4">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>

                            <a href="{{ route('admin.espaces.photos') }}" class="btn btn-outline-secondary btn-sm ms-1">
                                <i class="fas fa-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
