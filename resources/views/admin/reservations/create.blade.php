@extends('admin.layout')
@section('title', 'Créer une réservation')
@section('content')

<div class="container-fluid mt-4">
    <div class="row">
        {{-- Colonne gauche : Liste des utilisateurs --}}
        <div class="col-lg-5">
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
                <a href="{{ route('admin.utilisateurs.show') }}" class="btn btn-info">
                    <i class="fas fa-users"></i> Voir tous les utilisateurs
                </a>
                <a href="{{ route('admin.reservations.create') }}" class="btn btn-secondary">Réinitialiser</a>

                <form method="GET" action="{{ route('admin.reservations.create') }}" class="mb-3 mt-3">
                    <label class="form-label fw-bold">Rechercher un client :</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Tapez un nom ou email..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                </form>

                <h4 class="fw-bold mb-3">Choisir un client :</h4>
                <ul class="list-group">
                    @foreach($utilisateurs as $user)
                        <li class="list-group-item list-group-item-action user-item cursor-pointer"
                            data-email="{{ $user->email }}"
                            data-nom="{{ $user->Prenom }} {{ $user->Nom }}"
                            data-telephone="{{ $user->numero ?? '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $user->Prenom }} {{ $user->Nom }}</div>
                                    <div class="text-muted small">📧 {{ $user->email }}</div>
                                    @if(!empty($user->numero))
                                        <div class="text-muted small">📞 {{ $user->numero }}</div>
                                    @endif
                                    <div class="text-muted small">🏷️ {{ $user->typeClient->type ?? 'Non défini' }}</div>
                                </div>
                                <span class="badge bg-primary">Client</span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $utilisateurs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        {{-- Colonne droite : Formulaire de réservation --}}
        <div class="col-lg-7">
            <div class="reservation-form small" style="display:none;">
                {{-- Bouton Fermer en haut à droite --}}
                <div class="position-relative">
                    <button type="button" id="btn-close-form" class="btn-close position-absolute top-0 end-0 mt-2 me-3" aria-label="Fermer le formulaire"></button>
                </div>
                {{-- Nouvelle ligne en haut avec nom et téléphone du client --}}
                
                    <h6 class="mb-1 fw-bold text-primary" id="client-nom"></h6>
                    <p class="mb-0 text-muted" id="client-telephone">
                        <i class="fas fa-phone me-2"></i> Numéro : -
                    </p>
            

                <h2 class="text-center fw-bold text-primary text-uppercase mb-3">Nouvelle Réservation</h2>
                <form action="{{ route('admin.reservations.preview') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <!-- Email (maintenant readonly) -->
                        <div>
                            <label class="form-label fw-bold">E-mail</label>
                            <input type="email" name="email_client" id="email_client" class="form-control form-control-sm" readonly required>
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

                        <!-- === LES 3 CHAMPS DURÉE === -->
                        <div id="champ-duree-heures" class="mb-3">
                            <label class="form-label fw-bold">Durée (heures)</label>
                            <input type="number" name="duree" id="duree" min="1" value="1" class="form-control form-control-sm" required>
                        </div>
                        <div id="champ-duree-jours" class="mb-3" style="display: none;">
                            <label class="form-label fw-bold">Durée (jours)</label>
                            <input type="number" name="duree_jour" id="duree_jour" min="1" value="1" class="form-control form-control-sm">
                        </div>
                        <div id="champ-duree-mois" class="mb-3" style="display: none;">
                            <label class="form-label fw-bold">Durée (mois)</label>
                            <input type="number" name="duree_mois" id="duree_mois" min="1" value="1" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label class="form-label fw-bold">Quantité disponible </label>
                            <input type="number" name="quantite_reservation" id="quantite_reservation"
                                   class="form-control form-control-sm" min="1" value="1" required>
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
                    <div class="col-md-13">
                        <button type="submit" class="btn btn-success w-100 btn-sm">Enregistré</button>
                    </div>
                </form>
            </div>

            {{-- Message quand aucun client sélectionné --}}
            <div id="no-selection-message" class="text-center text-muted mt-5">
                <h4>← Sélectionnez un client à gauche</h4>
                <p>Le formulaire de réservation apparaîtra ici.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Sélection du client → ouverture du formulaire
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', () => {
            const email = item.dataset.email;
            const nom = item.dataset.nom;
            const telephone = item.dataset.telephone || 'Non renseigné';

            // Remplir les champs
            document.getElementById('email_client').value = email;

            // Afficher nom et téléphone
            document.getElementById('client-nom').textContent = nom;
            document.getElementById('client-telephone').innerHTML = 
                '<i class="fas fa-phone me-2"></i> Numéro : ' + telephone;

            // Afficher le formulaire
            document.querySelector('.reservation-form').style.display = 'block';
            document.getElementById('no-selection-message').style.display = 'none';

            // Mettre en surbrillance
            document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Nouveau : Bouton pour refermer le formulaire
    document.getElementById('btn-close-form').addEventListener('click', () => {
        // Cacher le formulaire
        document.querySelector('.reservation-form').style.display = 'none';

        // Réafficher le message d'attente
        document.getElementById('no-selection-message').style.display = 'block';

        // Retirer la surbrillance du client
        document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));

        // Optionnel : vider l'email (ou pas, selon ce que tu veux)
        // document.getElementById('email_client').value = '';
    });
</script>

<style>
    .user-item { cursor: pointer; }
    .user-item:hover { background-color: #f8f9fa; }
    .user-item.active { background-color: #e7f3ff; border-left: 4px solid #007bff; }
</style>

<script src="{{ asset('js/AdminReservation.js') }}"></script>
@endsection