@extends('admin.layout')

@section('title', 'Créer un nouveau client')

@section('content')
<div class="reservation-wrapper">
    <div class="reservation-form small">
        <h2 class="text-center text-primary mb-4 fw-bold">Créer un nouveau client</h2>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.utilisateurs.NouveauUtilisateur') }}" method="POST" class="p-3 shadow-sm rounded bg-white">
            @csrf
            <div class="row g-3">
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prénom</label>
                    <input type="text" name="Prenom" class="form-control" required>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="Nom" class="form-control" required>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Numéro</label>
                    <input type="text" name="numero" class="form-control" required>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type de client</label>
                    <select name="Id_Type_Client" class="form-select">
                        <option value="1">Occasionnel</option>
                        <option value="2">Abonné</option>
                    </select>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Entreprise</label>
                    <input type="text" name="Entreprise" class="form-control">
                </div>
        
                <div class="col-12 text-end">
                    <button class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Créer l’utilisateur
                    </button>
                </div>
            </div>
        </form>
                
    </div>
</div>
@endsection
