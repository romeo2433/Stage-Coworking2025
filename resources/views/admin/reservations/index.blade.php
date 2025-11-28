@extends('admin.layout')

@section('title', 'Réservations en attente')

@section('content')
<link rel="stylesheet" href="{{ asset('css/main.css') }}">
<div class="reservation-wrapper">
    <!-- SECTION RÉSERVATIONS EN ATTENTE -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0"> Réservations en attente</h4>
            <span class="badge bg-warning text-dark mt-2 mt-md-0">{{ $reservations->count() }} en attente</span>
        </div>

        <div class="card-body table-responsive">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($reservations->isNotEmpty())
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Utilisateur</th>
                        <th>Espace</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th class="text-center">Actions</th>
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
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.reservations.confirm', $reservation) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-success" title="Confirmer"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('admin.reservations.reject', $reservation) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-danger" title="Rejeter"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center text-muted py-3">
                <i class="fas fa-info-circle me-1"></i>
                Aucune réservation en attente.
            </div>
        @endif
        

    <!-- SECTION HISTORIQUE -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0"> Historique des réservations</h4>
            <span class="badge bg-light text-dark mt-2 mt-md-0">{{ $historique->count() }} au total</span>
        </div>

        <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-2 mb-4">
            <div class="col">
                <input type="text" name="utilisateur" class="form-control" placeholder="Utilisateur" value="{{ request('utilisateur') }}">
            </div>
            <div class="col">
                <input type="text" name="espace" class="form-control" placeholder="Espace" value="{{ request('espace') }}">
            </div>
            <div class="col">
                <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="{{ request('date_debut') }}">
            </div>
            <div class="col">
                <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="{{ request('date_fin') }}">
            </div>
            <div class="col">
                <select name="statut" class="form-control">
                    <option value="">Statut</option>
                    <option value="confirmee" @if(request('statut')=='confirmee')selected @endif>Confirmée</option>
                    <option value="terminee" @if(request('statut')=='terminee')selected @endif>Terminée</option>
                    <option value="annulee" @if(request('statut')=='annulee')selected @endif>Annulée</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Rechercher</button>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>
        
        

        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Utilisateur</th>
                        <th>Espace</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Check-in / Check-out</th>  
                    </tr>
                </thead>
        
                <tbody>
                    @forelse($historique as $h)
                        <tr>
                            <td>{{ $h->utilisateur?->Prenom }} {{ $h->utilisateur?->Nom }}</td>
                            <td>{{ $h->espace?->Nom }}</td>
                            <td>{{ $h->date_debut }}</td>
                            <td>{{ $h->date_fin }}</td>
                            <td>
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
                            </td>                            
                            <td>
                                @switch($h->Statut_Reservation)
                                    @case('confirmee')
                                        <span class="badge bg-success">Confirmée</span>
                                        @break
                                    @case('en_attente')
                                        <span class="badge bg-warning text-dark">En attente</span>
                                        @break
                                    @case('terminee')
                                        <span class="badge bg-secondary">Terminée</span>
                                        @break
                                    @case('payee')
                                        <span class="badge bg-warning">Payée</span>
                                        @break
                                    @case('annulee')
                                        <span class="badge bg-danger">Annulée</span>
                                        @break
                                    @default
                                        <span class="badge bg-dark">Inconnu</span>
                                @endswitch
                            </td>
        
                            
                            <td>
                                @php
                                    // Récupère le checkin lié à la réservation
                                    $checkin = \App\Models\Checkin::where('Id_Reservation', $h->Id_Reservation)->first();
                                @endphp
                            
                                {{--  BOUTON CHECK-IN --}}
                                @if(!$checkin)
                                    <form action="{{ route('admin.planning.checkin', $h->Id_Reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary mb-1">
                                            <i class="fas fa-sign-in-alt"></i> Check-in
                                        </button>
                                    </form>
                            
                                {{--  AFFICHAGE HEURE ARRIVÉE --}}
                                @else
                                    <span class="badge bg-success mb-1">
                                        <i class="fas fa-clock"></i> Arrivé à {{ \Carbon\Carbon::parse($checkin->heure_arrivee)->format('H:i') }}
                                    </span>
                                @endif
                            
                                <br>
                             
                                @if($checkin && !$checkin->heure_sortie)
                                    <form action="{{ route('admin.planning.checkout', $h->Id_Reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-1"
                                            @if($h->Statut_Reservation !== 'payee') disabled title="La réservation n'est pas encore payée" @endif>
                                            <i class="fas fa-sign-out-alt"></i> Check-out
                                        </button>
                                    </form>
                              
                                @elseif($checkin && $checkin->heure_sortie)
                                    <span class="badge bg-danger mb-1">
                                        <i class="fas fa-clock"></i> Sorti à {{ \Carbon\Carbon::parse($checkin->heure_sortie)->format('H:i') }}
                                    </span>
                                @endif
                            </td>                            
                        </tr>
        
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Aucune réservation dans l’historique</td>
                        </tr>
                    @endforelse
                </tbody>
        
            </table>
        </div>
@endsection
