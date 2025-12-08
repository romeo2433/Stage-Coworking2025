@extends('admin.layout')
@section('title', 'Créer une réservation')
@section('content')
<div class="reservation-wrapper">
   
    {{-- Liste des utilisateurs --}}
    <div class="mb-3">
        @if($errors->any())
        <div class="alert alert-danger p-2 mb-2">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nouveau client
        </a>
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary">Réinitialiser</a>
        <form method="GET" action="{{ route('admin.reservations.create') }}" class="mb-3">
            <label class="form-label fw-bold">Rechercher un client :</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Tapez un nom ou email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
        </form>

        <h4 class="fw-bold">Choisir un client :</h4>
        <ul class="list-group">
            @foreach($utilisateurs as $user)
                <li class="list-group-item list-group-item-action user-item" data-email="{{ $user->email }}">
                    {{ $user->Prenom }} {{ $user->Nom }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Formulaire de réservation --}}
    <div class="reservation-form small mt-3" style="display:none;">
        <h2 class="text-center fw-bold text-primary text-uppercase mb-3">Nouvelle Réservation</h2>

       

        <form action="{{ route('admin.reservations.preview') }}" method="POST">
            @csrf
            <div class="form-grid">

                <!-- Email -->
                <div>
                    <label class="form-label fw-bold">E-mail</label>
                    <input type="email" name="email_client" id="email_client" class="form-control form-control-sm" required>
                </div>

                <!-- Abonnement -->
                <div>
                    <label class="form-label fw-bold">Abonnement</label>
                    <select name="Id_Abonnement" id="Id_Abonnement" class="form-select form-select-sm">
                        <option value="">-- Aucun abonnement --</option>
                        @foreach($abonnements as $abo)
                            <option value="{{ $abo->Id_Abonnement }}"
                                    data-type="{{ $abo->Type_Abonnement }}"
                                    data-tarif_journalier="{{ $abo->tarif_journalier ?? 0 }}"
                                    data-tarif_mensuel="{{ $abo->tarif_mensuel ?? 0 }}">
                                {{ $abo->Nom_Abonnement }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Espace -->
                <div>
                    <label class="form-label fw-bold">Espace</label>
                    <select name="Id_Espace" id="Id_Espace" class="form-select form-select-sm" required>
                        <option value="">-- Choisir --</option>
                        @foreach($espaces as $espace)
                            <option value="{{ $espace->Id_Espace }}">{{ $espace->Nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" required>
                </div>

                <!-- Heure -->
                <div>
                    <label class="form-label fw-bold">Heure</label>
                    <select name="heure_debut" id="heure_debut" class="form-select form-select-sm" required>
                        <option value="">-- Choisir --</option>
                    </select>
                </div>
                <div>
                    <label class="form-label fw-bold">Quantité</label>
                    <input type="number" name="quantite_reservation" id="quantite_reservation" 
                           class="form-control form-control-sm" min="1" value="1" required>
                </div>
                

                <!-- === LES 3 CHAMPS DURÉE (exactement comme ça) === -->
                <div id="champ-duree-heures" class="mb-3">
                    <label class="form-label fw-bold">Durée (heures)</label>
                    <input type="number" name="duree" id="duree" min="1" max="8" value="1" class="form-control form-control-sm" required>
                </div>

                <div id="champ-duree-jours" class="mb-3" style="display: none;">
                    <label class="form-label fw-bold">Durée (jours)</label>
                    <input type="number" name="duree_jour" id="duree_jour" min="1" value="1" class="form-control form-control-sm">
                </div>

                <div id="champ-duree-mois" class="mb-3" style="display: none;">
                    <label class="form-label fw-bold">Durée (mois)</label>
                    <input type="number" name="duree_mois" id="duree_mois" min="1" value="1" class="form-control form-control-sm">
                </div>
                

                <!-- Équipements -->
                <div class="equipements-zone">
                    <label class="form-label fw-bold">Équipements</label>
                    <div id="equipements-container">
                        <p class="text-muted small">Sélectionnez un espace pour voir la liste.</p>
                    </div>
                </div>

                <!-- Total -->
                <div>
                    <label class="form-label fw-bold">Total (Ar)</label>
                    <input type="text" id="total" class="form-control form-control-sm" readonly value="0 Ar">
                    <input type="hidden" name="total" id="total_hidden" value="0">
                </div>
            </div>

            <!-- Indicateur disponibilité -->
            <div id="disponibilite-indicator" class="mt-3 mb-3" style="display:none;">
                <div class="alert" role="alert" id="dispo-alert">
                    <strong id="dispo-text"></strong>
                    <span id="dispo-places" class="badge bg-primary fs-6"></span>
                </div>
            </div>

            <!-- Bouton -->
            <div class="col-md-6">
                <button type="submit" class="btn btn-success w-100 btn-sm">Créer directement</button>
            </div>
        </form>
    </div>
</div>
<script>
    // Juste pour afficher le formulaire au clic sur un client
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', () => {
            document.getElementById('email_client').value = item.dataset.email;
            document.querySelector('.reservation-form').style.display = 'block';
            document.querySelector('.reservation-form').scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
<script src="{{ asset('js/AdminReservation.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/AdminReservation.js') }}"></script>
@endsection