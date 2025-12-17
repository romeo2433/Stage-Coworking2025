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
                    <input type="text" name="Prenom" class="form-control" value="{{ old('Prenom') }}" required placeholder="Ex : Jean">
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="Nom" class="form-control" value="{{ old('Nom') }}" required placeholder="Ex : Dupont">
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="exemple@email.com">
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Numéro</label>
                    <input type="text" name="numero" class="form-control" value="{{ old('numero') }}" required placeholder="Ex : 034 00 000 00">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type de client</label>
                    <select name="Id_Type_Client" class="form-select">
                        <option value="1" selected>Occasionnel</option>
                        <option value="2">Abonné</option>
                    </select>
                </div>                
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Entreprise</label>
                    <input type="text" name="Entreprise" class="form-control" value="{{ old('Entreprise') }}" placeholder="Nom de l’entreprise (facultatif)">
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        minlength="6" 
                        required 
                        placeholder="Au moins 6 caractères"
                    >
                    <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères.</small>
                </div>
        
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        class="form-control" 
                        minlength="6" 
                        required 
                        placeholder="Confirmez le mot de passe"
                    >
                </div>
        
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-user-plus me-1"></i> Créer l’utilisateur
                    </button>
                </div>
            </div>
        </form>        
    </div>
</div>
@endsection
