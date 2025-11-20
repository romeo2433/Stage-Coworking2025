@extends('admin.layout')

@section('title', 'Ajouter un espace')

@section('content')

<div class="reservation-wrapper" style="max-width: 750px; margin: auto;">
    
    <h4 class="text-center text-primary fw-bold mb-3" style="font-size:1.1rem;">
        <i class="fas fa-plus-circle"></i> Ajouter un nouvel espace
    </h4>

    <form action="{{ route('admin.espaces.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="card p-3 shadow-sm"
          style="font-size:0.85rem;">
          
        @csrf

        <div class="row g-2">

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nom de l’espace</label>
                <input type="text" name="Nom" class="form-control form-control-sm" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Statut</label>
                <select name="Statut" class="form-select form-select-sm" required>
                    <option value="disponible">Disponible</option>
                    <option value="indisponible">Indisponible</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Capacité</label>
                <input type="number" name="capacite" class="form-control form-control-sm" min="1" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Type d’espace</label>
                <select name="Id_Type" class="form-select form-select-sm" required>
                    <option value="">-- Choisir --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->Id_Type }}">{{ $type->Type_Espace }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tarif horaire</label>
                <input type="number" name="tarif_horaire" class="form-control form-control-sm" min="0" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tarif journalier</label>
                <input type="number" name="tarif_journalier" class="form-control form-control-sm" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tarif mensuel</label>
                <input type="number" name="tarif_mensuel" class="form-control form-control-sm" min="0" required>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small">Équipements</label>
                <div class="border rounded p-2" style="max-height:150px; overflow-y:auto; font-size:0.8rem;">
                    @foreach($equipements as $equipement)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" 
                                   name="equipements[]" 
                                   value="{{ $equipement->Id_Equipement }}"
                                   id="eq{{ $equipement->Id_Equipement }}">
                            <label class="form-check-label small" for="eq{{ $equipement->Id_Equipement }}">
                                {{ $equipement->nom }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12 text-end mt-2">
                <a href="{{ route('admin.espaces.photos') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
