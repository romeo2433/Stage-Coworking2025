<div class="row">
    <div class="col-md-6">
        <h6><strong>Client</strong></h6>
        <p>{{ $reservation->utilisateur?->Prenom }} {{ $reservation->utilisateur?->Nom }}<br>
           <small class="text-muted">{{ $reservation->utilisateur?->email }}</small></p>
    </div>
    <div class="col-md-6">
        <h6><strong>Espace</strong></h6>
        <p>{{ $reservation->espace?->Nom }} ({{ $reservation->quantite_reservation }} place(s))</p>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-6">
        <h6><strong>Période</strong></h6>
        <p>
            Du {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y à H:i') }}<br>
            Au {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y à H:i') }}
        </p>
        <p><strong>Durée :</strong> {{ $reservation->duree_heures }} heure(s)</p>
    </div>
    <div class="col-md-6">
        <h6><strong>Montant</strong></h6>
        <p class="fs-5 fw-bold text-success">{{ number_format($reservation->total, 0, ',', ' ') }} Ar</p>
        <p><strong>Statut :</strong> 
            <span class="badge bg-{{ $reservation->Statut_Reservation === 'confirmee' ? 'success' : 'warning' }}">
                {{ ucfirst($reservation->Statut_Reservation) }}
            </span>
        </p>
    </div>
</div>

@if($reservation->equipements->isNotEmpty())
<hr>
<h6><strong>Équipements supplémentaires</strong></h6>
<ul>
    @foreach($reservation->equipements as $equip)
        <li>{{ $equip->Nom }} ({{ $equip->prix }} Ar)</li>
    @endforeach
</ul>
@endif

<hr>
<small class="text-muted">
    Réservation créée le {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y à H:i') }}
    @if($reservation->createur)
        par <strong>{{ $reservation->createur->Prenom }} {{ $reservation->createur->Nom }}</strong>
    @endif
</small>