@extends('admin.layout')

@section('title', 'Créer une réservation')

@section('content')
<div class="reservation-wrapper">

    {{-- Liste des utilisateurs --}}
    <div class="mb-3">
        <h4 class="fw-bold">Choisir un client :</h4>
        <ul class="list-group">
            @foreach($utilisateurs as $user)
                <li class="list-group-item list-group-item-action user-item" 
                    data-email="{{ $user->email }}">
                    {{ $user->Prenom }} {{ $user->Nom }} - {{ $user->email }}
                    {{ $user->Entreprise ? '('.$user->Entreprise.')' : '' }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Formulaire de réservation --}}
    <div class="reservation-form small mt-3" style="display:none;">
        <h2 class="text-center fw-bold text-primary text-uppercase mb-3">
            Nouvelle Réservation
        </h2>

        @if($errors->any())
            <div class="alert alert-danger p-2 mb-2">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reservations.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                {{-- E-mail rempli automatiquement --}}
                <div>
                    <label class="form-label fw-bold">E-mail</label>
                    <input type="email" name="email_client" id="email_client" class="form-control form-control-sm" required>
                </div>
                <div>
                    <label class="form-label fw-bold">Espace</label>
                    <select name="Id_Espace" id="Id_Espace" class="form-select form-select-sm" required>
                        <option value="">-- Choisir --</option>
                        @foreach($espaces as $espace)
                            <option value="{{ $espace->Id_Espace }}">{{ $espace->Nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" required>
                </div>
                <div>
                    <label class="form-label fw-bold">Heure</label>
                    <select name="heure_debut" id="heure_debut" class="form-select form-select-sm" required>
                        <option value="">-- Choisir --</option>
                    </select>
                </div>
                <div>
                    <label class="form-label fw-bold">Durée (h)</label>
                    <input type="number" name="duree" id="duree" min="1" max="8" value="1" class="form-control form-control-sm" required>
                </div>
                <div class="equipements-zone">
                    <label class="form-label fw-bold">Équipements</label>
                    <div id="equipements-container">
                        <p class="text-muted small">Sélectionnez un espace pour voir la liste.</p>
                    </div>
                </div>
                <div>
                    <label class="form-label fw-bold">Total (Ar)</label>
                    <input type="text" id="total" class="form-control form-control-sm" readonly value="0 Ar">
                    <input type="hidden" name="total" id="total_hidden" value="0">
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100 btn-sm mt-2">Créer la réservation</button>
        </form>
    </div>
</div>
{{-- Script pour pré-remplir le mail et afficher le formulaire --}}
<script>
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', () => {
            const email = item.dataset.email;
            const form = document.querySelector('.reservation-form');
            form.style.display = 'block';
            document.getElementById('email_client').value = email;
            form.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
<script src="{{ asset('js/AdminReservation.js') }}"></script>
@endsection
