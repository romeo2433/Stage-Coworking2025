@extends('admin.layout')

@section('title', 'Réservations en attente')

@section('content')
<div class="container-fluid px-0">
    <!-- SECTION RÉSERVATIONS EN ATTENTE -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0">📅 Réservations en attente</h4>
            <span class="badge bg-warning text-dark mt-2 mt-md-0">{{ $reservations->count() }} en attente</span>
        </div>

        <div class="card-body table-responsive">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

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
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->utilisateur?->Prenom }} {{ $reservation->utilisateur?->Nom }}</td>
                            <td>{{ $reservation->espace?->Nom }}</td>
                            <td>{{ $reservation->date_debut }}</td>
                            <td>{{ $reservation->date_fin }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.reservations.confirm', $reservation) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-success"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('admin.reservations.reject', $reservation) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucune réservation en attente</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION HISTORIQUE -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0">🕓 Historique des réservations</h4>
            <span class="badge bg-light text-dark mt-2 mt-md-0">{{ $historique->count() }} au total</span>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Utilisateur</th>
                        <th>Espace</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
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
                                @if($h->Statut_Reservation == 'confirmee')
                                    <span class="badge bg-success">Confirmée</span>
                                @else
                                    <span class="badge bg-dark">Terminer </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucune réservation dans l’historique</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
