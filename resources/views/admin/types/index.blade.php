@extends('admin.layout')

@section('title', "Types d'espaces")

@section('content')
<div class="container-sm mt-3" style="max-width: 650px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <h5 class="text-primary fw-bold mb-3 text-center">Gestion des Types d'espaces</h5>

            {{-- Message succès --}}
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulaire ajout --}}
            <form action="{{ route('admin.typesespaces.store') }}" method="POST" class="mb-3">
                @csrf
                <div class="input-group input-group-sm">
                    <input type="text" name="Type_Espace" class="form-control" placeholder="Nom du type d’espace" required>
                    <button type="submit" class="btn btn-success px-3">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter
                    </button>
                </div>
            </form>

            {{-- Liste des types --}}
            <div class="list-group list-group-flush small">
                <div class="list-group-item bg-light fw-semibold text-center py-2">
                    Liste des types d'espaces
                </div>

                @forelse($types as $type)
                    <div class="list-group-item py-1">
                        <form action="{{ route('admin.types.update', $type->Id_Type) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf
                            <input type="text" name="Type_Espace" value="{{ $type->Type_Espace }}" class="form-control form-control-sm" required>
                            <button type="submit" class="btn btn-primary btn-sm px-2">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-2">
                        Aucun type d’espace ajouté.
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
