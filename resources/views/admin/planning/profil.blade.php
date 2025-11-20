@extends('admin.layout')

@section('title', 'Planning du jour')

@section('content')

{{-- Messages de succès ou d'erreur --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="reservation-wrapper">
    <div class="card shadow-sm p-4 mb-5">
        <h4 class="mb-4 fw-bold text-primary text-center">
            <i class="fas fa-calendar-day me-2"></i> Planning du jour – {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}
        </h4>

        @if($reservations->isEmpty())
            <div class="alert alert-info text-center">Aucune réservation pour aujourd'hui.</div>
        @else
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Heure</th>
                            <th>Client</th>
                            <th>Espace</th>
                            <th>Durée (h)</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $r)
                            <tr class="text-center">
                                <td>{{ \Carbon\Carbon::parse($r->date_debut)->format('H:i') }}</td>
                                <td>{{ $r->utilisateur->Prenom ?? '—' }} {{ $r->utilisateur->Nom ?? '' }}</td>
                                <td>{{ $r->espace->Nom ?? '—' }}</td>
                                <td>{{ $r->duree_heures }}</td>
                                <td>
                                    <span class="badge 
                                        @if($r->Statut_Reservation === 'confirmee') bg-success
                                        @elseif($r->Statut_Reservation === 'En attente') bg-warning text-dark
                                        @elseif($r->Statut_Reservation === 'En cours') bg-info
                                        @else bg-secondary @endif">
                                        {{ ucfirst($r->Statut_Reservation) }}
                                    </span>
                                </td>
                                <td>
                                    @php $checkin = $r->checkin; @endphp
                                    
                                    {{-- Check-in --}}
                                    @if(!$checkin)
                                        <form action="{{ route('admin.planning.checkin', $r->Id_Reservation) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary mb-1">
                                                <i class="fas fa-sign-in-alt"></i> Check-in
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success mb-1">
                                            <i class="fas fa-clock"></i> Arrivé à {{ \Carbon\Carbon::parse($checkin->heure_arrivee)->format('H:i') }}
                                        </span>
                                    @endif

                                    {{-- Check-out --}}
                                    @if($checkin && !$checkin->heure_sortie)
                                        <form action="{{ route('admin.planning.checkout', $r->Id_Reservation) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger mb-1"
                                                @if($r->Statut_Reservation !== 'payee') disabled title="La réservation n'est pas encore payée" @endif>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
