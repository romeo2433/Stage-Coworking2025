@extends('admin.layout')
@section('title', 'Tableau de bord - Réservations')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-center fw-bold text-primary">Tableau de bord des Réservations</h3>

    <!-- Cartes résumé -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="fw-bold">Total Réservations</h6>
                    <h3 class="mb-0">{{ $statsReservations['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="fw-bold">Réservations Terminées</h6>
                    <h3 class="mb-0">{{ $statsReservations['terminee'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="fw-bold">En attente</h6>
                    <h3 class="mb-0">{{ $statsReservations['en_attente'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="fw-bold">Revenu Total (Ar)</h6>
                    <h3 class="mb-0">{{ number_format($revenuTotal, 0, ',', ' ') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique Donut : Répartition par statut -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center fw-bold text-secondary">Répartition des réservations par statut</h5>
                    <canvas id="statutChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique Line : Revenu mensuel -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center fw-bold text-secondary">Évolution du revenu mensuel</h5>
                    <canvas id="revenuChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique Bar : Réservations terminées par mois -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center fw-bold text-secondary">Nombre de réservations terminées par mois</h5>
                    <canvas id="reservationChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Donut Chart - Statut des réservations
    const statutData = @json($reservationsParStatut);
    new Chart(document.getElementById('statutChart'), {
        type: 'doughnut',
        data: {
            labels: statutData.map(item => item.label),
            datasets: [{
                data: statutData.map(item => item.value),
                backgroundColor: statutData.map(item => item.color),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: false }
            }
        }
    });

    // Line Chart - Revenu mensuel
    const revenus = @json($revenusParMois);
    new Chart(document.getElementById('revenuChart'), {
        type: 'line',
        data: {
            labels: revenus.map(r => r.label),
            datasets: [{
                label: 'Revenu (Ar)',
                data: revenus.map(r => r.total),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Bar Chart - Réservations terminées par mois
    const reservations = @json($reservationsParMois);
    new Chart(document.getElementById('reservationChart'), {
        type: 'bar',
        data: {
            labels: reservations.map(r => r.label),
            datasets: [{
                label: 'Réservations terminées',
                data: reservations.map(r => r.total),
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection