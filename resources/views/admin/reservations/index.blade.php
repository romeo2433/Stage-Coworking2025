@extends('admin.layout')

@section('title', 'Réservations en attente')

@section('content')
@if($reservations->isNotEmpty())
    <!-- RÉSERVATIONS EN ATTENTE -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-hourglass-split me-1"></i>Réservations en attente
                </h5>
                <span class="badge bg-warning text-dark">{{ $reservations->count() }} en attente</span>
            </div>

            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Client</th>
                                <th>Espace</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th width="100" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->utilisateur?->Prenom }} {{ $reservation->utilisateur?->Nom }}</td>
                                    <td>{{ $reservation->espace?->Nom }}</td>
                                    <td>{{ $reservation->date_debut }}</td>
                                    <td>{{ $reservation->date_fin }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <form action="{{ route('admin.reservations.confirm', $reservation) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-success btn-sm" onclick="return confirm('Confirmer cette réservation ?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reservations.reject', $reservation) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Rejeter cette réservation ?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
    <!--  HISTORIQUE DES RÉSERVATIONS -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-secondary text-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-1"></i>Historique des réservations
                </h5>
                <span class="badge bg-light text-dark">{{ $historique->count() }} au total</span>
            </div>

            <!-- FORMULAIRE DE RECHERCHE -->
            <div class="search-section bg-light rounded p-3 mb-2">
                <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-2">
                    <div class="col-md-2"><input type="text" name="utilisateur" class="form-control form-control-sm" placeholder="Client" value="{{ request('utilisateur') }}"></div>
                    <div class="col-md-2">
                        <select name="espace" id="espace" class="form-select form-select-sm">
                            <option value="">-- Tous les espaces --</option>
                            @foreach($espaces as $e)
                                <option value="{{ $e->Id_Espace }}" {{ request('espace') == $e->Id_Espace ? 'selected' : '' }}>
                                    {{ $e->Nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>  
                    <div class="col-md-2">
                        <select name="reserver_par" class="form-select form-select-sm">
                            <option value="">-- Réservé par --</option>
                            @foreach($createurs as $c)
                                <option value="{{ $c->Id_Utilisateur }}" 
                                    {{ request('reserver_par') == $c->Id_Utilisateur ? 'selected' : '' }}>
                                    {{ $c->Prenom }} {{ $c->Nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>                                      
                    <div class="col-md-2"><input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}"></div>
                    <div class="col-md-2"><input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}"></div>
                    <div class="col-md-2">
                        <select name="statut" class="form-control form-control-sm">
                            <option value="">Statut</option>
                            <option value="confirmee" @selected(request('statut')=='confirmee')>Confirmée</option>
                            <option value="terminee" @selected(request('statut')=='terminee')>Terminée</option>
                            <option value="annulee" @selected(request('statut')=='annulee')>Annulée</option>
                            <option value="payee" @selected(request('statut')=='payee')>Payer</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-primary btn-sm w-50">Rechercher</button>
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary btn-sm w-50">Reset</a>
                    </div>
                </form>
            </div>
            @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Espace</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Durée</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Réservé par</th>
                                <th>Check-in / Check-out</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($historique as $h)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">
                                            #{{ $h->Id_Reservation }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        @if($h->reference)
                                            <span class="badge bg-info">
                                                {{ $h->reference }}
                                            </span>
                                        @else
                                            <em class="text-muted">—</em>
                                        @endif
                                    </td>
                                    
                                    <td style="cursor:pointer"
                                    onclick="showReservation({{ $h->Id_Reservation }})">
                                    {{ $h->utilisateur?->Prenom }} {{ $h->utilisateur?->Nom }}
                                    </td>
                                    <td>{{ $h->espace?->Nom }}</td>
                                    <div id="infoEspace" class="mt-3"></div>
                                    <td>{{ \Carbon\Carbon::parse($h->date_debut)->format('d/m/Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($h->date_fin)->format('d/m/Y H:i') }}</td>                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span>
                                                {{ $h->duree_heures }}
                                                @if($h->abonnement)
                                                    @if($h->abonnement->Type_Abonnement === 'journalier')
                                                        jour(s)
                                                    @elseif($h->abonnement->Type_Abonnement === 'mensuel')
                                                        mois
                                                    @else
                                                        h
                                                    @endif
                                                @else
                                                    h
                                                @endif
                                            </span>
                                    
                                            {{-- ICÔNE MODIFIER --}}
                                            @if($h->Statut_Reservation === 'confirmee')
                                            <i class="bi bi-pencil text-muted"
                                               style="cursor:pointer"
                                               title="Modifier la durée"
                                               onclick="openEditModal(
                                                    {{ $h->Id_Reservation }},
                                                    '{{ $h->Id_Abonnement }}',
                                                    {{ $h->duree_heures }}
                                               )"
                                               onmouseover="this.classList.add('text-warning')"
                                               onmouseout="this.classList.remove('text-warning')">
                                            </i>
                                        @endif
                                        
                                        </div>
                                    </td>
                                    
                                    <td>{{ number_format($h->total, 0, ',', ' ') }} Ar</td>
                                    <td>
                                        @switch($h->Statut_Reservation)
                                            @case('confirmee') <span class="badge bg-success">Confirmée</span> @break
                                            @case('en_attente') <span class="badge bg-warning text-dark">En attente</span> @break
                                            @case('terminee') <span class="badge bg-secondary">Terminée</span> @break
                                            @case('payee') <span class="badge bg-info">Payée</span> @break
                                            @case('annulee') <span class="badge bg-danger">Annulée</span> @break
                                            @default <span class="badge bg-dark">Inconnu</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($h->createur)
                                            <strong>{{ $h->createur->Prenom }} {{ $h->createur->Nom }}</strong>
                                        @else
                                            <em class="text-muted">Système / Client</em>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $checkin = \App\Models\Checkin::where('Id_Reservation', $h->Id_Reservation)->first();
                                            $dateDebut = \Carbon\Carbon::parse($h->date_debut);
                                            $aujourdHui = \Carbon\Carbon::today(); // Date du jour (sans heure)
                                            $estAujourdHuiOuPasse = $dateDebut->isSameDay($aujourdHui) || $dateDebut->isPast();
                                        @endphp
                                    
                                        @if(!in_array($h->Statut_Reservation, ['annulee']) && $estAujourdHuiOuPasse)
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                    
                                                {{-- CHECK-IN --}}
                                                @if(!$checkin)
                                                    <form method="POST" action="{{ route('admin.planning.checkin', $h->Id_Reservation) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-outline-primary btn-sm" type="submit">
                                                            <i class="bi bi-box-arrow-in-right"></i> Entrée
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-success">
                                                        Arrivé : {{ \Carbon\Carbon::parse($checkin->heure_arrivee)->format('H:i') }}
                                                    </span>
                                                @endif
                                    
                                                {{-- CHECK-OUT --}}
                                                @if($checkin && !$checkin->heure_sortie)
                                                    <form method="POST" action="{{ route('admin.planning.checkout', $h->Id_Reservation) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-outline-danger btn-sm"
                                                            @if($h->Statut_Reservation !== 'payee') disabled @endif>
                                                            <i class="bi bi-box-arrow-right"></i> Sortie
                                                        </button>
                                                    </form>
                                                @elseif($checkin && $checkin->heure_sortie)
                                                    <span class="badge bg-danger">
                                                        Sorti : {{ \Carbon\Carbon::parse($checkin->heure_sortie)->format('H:i') }}
                                                    </span>
                                                @endif
                                    
                                            </div>
                                        @else
                                            {{-- Optionnel : afficher un message si la réservation est future --}}
                                            <span class="text-muted small">
                                                @if(!$estAujourdHuiOuPasse)
                                                    <em>À venir le {{ $dateDebut->format('d/m/Y') }}</em>
                                                @else
                                                    <em>Annulée</em>
                                                @endif
                                            </span>
                                        @endif
                                    </td>   
                                    <td>
                                        @if($h->Statut_Reservation === 'confirmee')
                                            <a href="{{ route('admin.finance.index', ['reservation_id' => $h->Id_Reservation]) }}"
                                               class="btn btn-success btn-sm me-1">
                                                <i class="bi bi-cash"></i> Payer
                                            </a>
                                    
                                            {{-- @if(Auth::check() && Auth::user()->Id_Profil == 1)--}}
                                            <button type="button"
                                            class="btn btn-warning btn-sm me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#annulerModal{{ $h->Id_Reservation }}">
                                            <i class="bi bi-x-circle"></i> 
                                        </button>
                                                
                                    
                                                <form action="{{ route('admin.reservation.detruire', $h->Id_Reservation) }}"
                                                      method="POST" style="display:inline;"
                                                      onsubmit="return confirm('SUPPRIMER définitivement ? Irreversible !');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i> 
                                                    </button>
                                                </form>
                                                @elseif($h->Statut_Reservation === 'annulee')
                                                <!-- Réservation annulée : on affiche le motif à la place des boutons -->
                                                <div class="text-start">
                                                    @if($h->observation)
                                                        <small class="d-block text-muted">
                                                            <strong>Motif :</strong><br>
                                                            {{ $h->observation }}
                                                        </small>
                                                    @else
                                                        <small class="d-block text-muted fst-italic">
                                                            Aucun motif indiqué
                                                        </small>
                                                    @endif
                                           {{-- @endif--}}
                                        @endif
                                    </td>
                                </tr>
                                     <!-- Modal d'annulation pour la réservation {{ $h->Id_Reservation }} -->
                    <div class="modal fade" id="annulerModal{{ $h->Id_Reservation }}" tabindex="-1" aria-labelledby="annulerModalLabel{{ $h->Id_Reservation }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="annulerModalLabel{{ $h->Id_Reservation }}">
                                        Annuler la réservation n°{{ $h->Id_Reservation }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>

                                <form action="{{ route('admin.reservation.annuler', $h->Id_Reservation) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="modal-body">
                                        <p>Voulez-vous vraiment annuler cette réservation ?</p>
                                        
                                        <div class="mb-3">
                                            <label for="observation{{ $h->Id_Reservation }}" class="form-label">
                                                Observation / Motif d'annulation <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control" 
                                                    name="observation" 
                                                    id="observation{{ $h->Id_Reservation }}" 
                                                    rows="4" 
                                                    required 
                                                    placeholder="Ex: Client n'a pas venu, demande d'annulation, etc."></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-x-circle"></i> Confirmer l'annulation
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucune réservation trouvée</td>
                                </tr>
                             </div>

                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $historique->links('pagination::bootstrap-5') }}
                    </div>  
                    @foreach($reservations as $h)                    
                    @endforeach


                   
                    <!-- MODALE DÉTAILS RÉSERVATION -->
                    <div class="modal fade" id="reservationDetailsModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Détails de la réservation</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body" id="reservationDetailsContent">
                                    <!-- Le contenu chargé par AJAX viendra ici -->
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                   
                    <!-- MODAL MODIFIER DURÉE -->
                    <div class="modal fade" id="editDureeModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form id="editDureeForm" method="POST"> 
                                @csrf
                                @method('PUT') 
                    
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Modifier la durée</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                    
                                    <div class="modal-body">
                                        <label for="duree" class="form-label">Nouvelle durée</label>
                                        <input type="number" min="1" class="form-control" name="duree" id="modalDuree" required>
                                        
                                        <!-- Zone pour afficher les erreurs côté frontend -->
                                        <div id="dureeError" class="text-danger mt-2"></div>
                    
                                        <input type="hidden" name="Id_Abonnement" id="modalAbonnement">
                                    </div>
                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-warning">Mettre à jour</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function openEditModal(idReservation, idAbonnement, duree) {
        const dureeInput = document.getElementById('modalDuree');
        const dureeAbonnement = document.getElementById('modalAbonnement');
        const dureeError = document.getElementById('dureeError');
    
        // remplir les champs
        dureeInput.value = duree;
        dureeAbonnement.value = idAbonnement || '';
        dureeError.textContent = '';
    
        // mettre à jour l'action du formulaire
        let url = "{{ route('admin.reservations.update_duree', ':id') }}";
        url = url.replace(':id', idReservation);
        document.getElementById('editDureeForm').action = url;
    
        // récupérer la durée max via AJAX
        fetch("{{ route('admin.reservations.duree_max', ':id') }}".replace(':id', idReservation))
            .then(res => res.json())
            .then(data => {
                const max = data.duree_max;
                dureeInput.max = max;
                dureeInput.min = 1;
    
                if (parseInt(dureeInput.value) > max) {
                    dureeInput.value = max;
                }
    
                // empêcher de dépasser la durée max
                dureeInput.addEventListener('input', function() {
                    if (parseInt(this.value) > max) this.value = max;
                    if (parseInt(this.value) < 1) this.value = 1;
                });
            });
    
        // ouvrir le modal
        new bootstrap.Modal(document.getElementById('editDureeModal')).show();
    }
    
    // gérer la soumission avec AJAX pour afficher les erreurs sans recharger
    document.getElementById('editDureeForm').addEventListener('submit', function(e){
        e.preventDefault();
    
        const form = this;
        const formData = new FormData(form);
        const errorDiv = document.getElementById('dureeError');
        errorDiv.textContent = '';
    
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(resp => {
            if(resp.status === 'error'){
                errorDiv.textContent = resp.message;
            } else {
                // update OK
                bootstrap.Modal.getInstance(document.getElementById('editDureeModal')).hide();
                location.reload();
            }
        })
        .catch(err => console.error(err));
    });
    </script>
    <script src="{{ asset('js/AfficheEspace.js') }}"></script>

    
@endsection
<!--Hatreto mody mandroso miandry vokam-pikirizana --}}