@extends('admin.layout')

@section('title', 'Statistiques')

@section('content')
<div class="row text-center mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 rounded-3 p-2">
            <div class="card-body p-2 text-center">
                <h6 class="fw-bold mb-1">Clients</h6>
                <p class="h4 text-primary mb-0">{{ $totalClients }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 rounded-3 p-2">
            <div class="card-body p-2 text-center">
                <h6 class="fw-bold mb-1">Réservations</h6>
                <p class="h4 text-success mb-0">{{ $totalReservations }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 rounded-3 p-2">
            <div class="card-body p-2 text-center">
                <h6 class="fw-bold mb-1">Revenu total (Ar)</h6>
                <p class="h4 text-warning mb-0">{{ number_format($revenuTotal, 0, ',', ' ') }}</p>
            </div>
        </div>
    </div>
</div>


    {{-- Graphique 1 : Revenu mensuel --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-center mb-3 text-secondary">
                Revenu mensuel (toutes années)
            </h5>
            <canvas id="revenuChart" height="120"></canvas>
        </div>
    </div>

    {{-- Graphique 2 : Réservations terminées --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-center mb-3 text-secondary">
                Réservations terminées par mois
            </h5>
            <canvas id="reservationChart" height="120"></canvas>
        </div>
    </div>
</div>

{{-- Inclusion des scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Variables PHP vers JS --}}
<script>
    window.revenusParMois = @json($revenusParMois);
    window.reservationsParMois = @json($reservationsParMois);
</script>

{{-- Script externe --}}
<script src="{{ asset('js/statistiques.js') }}"></script>
@endsection
