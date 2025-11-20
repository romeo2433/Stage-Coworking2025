@extends('admin.layout')

@section('title', 'Gestion des Équipements')

@section('content')
<div class="container-sm mt-2 compact-admin" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <h5 class="text-primary fw-bold mb-3 text-center">Gestion des Équipements</h5>

            {{-- Messages --}}
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire ajout Type --}}
            <form action="{{ route('admin.typesequipements.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="Type" class="form-control form-control-sm" placeholder="Nouveau type…" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark btn-sm w-100">
                            <i class="fas fa-plus"></i> Ajouter Type
                        </button>                        
                    </div>
                </div>
            </form>

            {{-- Liste des Types --}}
            <hr>
            <h6 class="fw-bold mt-3">Liste des Types d’équipements</h6>

            <ul class="list-group list-group-flush mt-2">
                @forelse($typesEquipements as $type)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1">

                        {{-- UPDATE TYPE --}}
                        <form action="{{ route('admin.typesequipements.update', $type->Id_Type) }}" 
                              method="POST" class="d-flex gap-2 w-75">
                            @csrf
                            @method('PUT')

                            <input type="text" name="Type" value="{{ $type->Type }}"
                                   class="form-control form-control-sm" required>

                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>

                        {{-- DELETE TYPE --}}
                        <form action="{{ route('admin.typesequipements.destroy', $type->Id_Type) }}" 
                              method="POST" onsubmit="return confirm('Supprimer ce type ?');">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Aucun type ajouté.</li>
                @endforelse
            </ul>

            <hr class="my-4">

            {{-- Formulaire ajout d’un Équipement --}}
            <form action="{{ route('admin.equipements.store') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="nom" class="form-control form-control-sm" placeholder="Nom" required>
                    </div>

                    <div class="col-md-3">
                        <input type="number" name="prix" class="form-control form-control-sm" placeholder="Prix (Ar)" step="0.01" required>
                    </div>
            
                    <div class="col-md-3">
                        <select name="Id_Type" class="form-select form-select-sm" required>
                            <option value="">-- Type --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->Id_Type }}">{{ $type->Type }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-plus-circle me-1"></i> Ajouter
                        </button>
                    </div>
                </div>
            </form>

            {{-- Liste des équipements --}}
            <div class="list-group list-group-flush small">
                <div class="list-group-item bg-light fw-semibold text-center py-2">
                    Liste des Équipements
                </div>

                @forelse($equipements as $eq)
                    <div class="list-group-item py-1 d-flex justify-content-between">

                        <form action="{{ route('admin.equipements.update', $eq->Id_Equipement) }}" method="POST" class="d-flex gap-2 align-items-center w-75">
                            @csrf
                            @method('PUT')

                            <input type="text" name="nom" value="{{ $eq->nom }}" class="form-control form-control-sm" required>
                            <input type="number" name="prix" value="{{ $eq->prix }}" step="0.01" class="form-control form-control-sm" required>

                            <button type="submit" class="btn btn-primary btn-sm px-2">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>

                        <form action="{{ route('admin.equipements.destroy', $eq->Id_Equipement) }}" 
                              method="POST"
                              onsubmit="return confirm('Supprimer cet équipement ?');">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm px-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-2">
                        Aucun équipement ajouté.
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('admin.espaces.photos') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
