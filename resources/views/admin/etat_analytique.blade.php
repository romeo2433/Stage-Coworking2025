@extends('admin.layout')
@section('title', 'État Analytique')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>État Analytique des Paiements</h2>
        <div class="no-print">
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <a href="{{ route('admin.etat_analytique.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm me-2">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.etat_analytique.excel', request()->query()) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    <form method="GET" class="mb-4 card p-3 no-print">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Début</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="form-control">
            </div>
            <div class="col-auto">
                <label class="form-label">Fin</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>

    <div class="alert alert-info mb-4">
        <strong>Période :</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total des paiements :</strong> {{ number_format($totalGeneral, 0, ',', ' ') }} Ar
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-header bg-success text-white">Top 5 Clients</div>
                <div class="card-body chart-container">
                    <canvas id="chartClients"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">Top 5 Espaces (Chiffre d'affaires)</div>
                <div class="card-body chart-container">
                    <canvas id="chartEspaces"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyse par espace -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">Analyse par Espace</div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Espace</th>
                            <th>Nbre Paiements</th>
                            <th>Total Montant</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiementsParEspace as $espace => $data)
                            <tr>
                                <td>{{ $espace }}</td>
                                <td>{{ $data['count'] }}</td>
                                <td>{{ number_format($data['total'], 0, ',', ' ') }} Ar</td>
                                <td>{{ $totalGeneral > 0 ? round(($data['total'] / $totalGeneral) * 100, 2) : 0 }} %</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th>{{ $paiements->count() }}</th>
                            <th>{{ number_format($totalGeneral, 0, ',', ' ') }} Ar</th>
                            <th>100 %</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Analyse par client -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Analyse par Client</div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Client</th>
                            <th>Nbre Paiements</th>
                            <th>Total Montant</th>
                            <th>Pourcentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiementsParClient as $client => $data)
                            <tr>
                                <td>{{ $client }}</td>
                                <td>{{ $data['count'] }}</td>
                                <td>{{ number_format($data['total'], 0, ',', ' ') }} Ar</td>
                                <td>{{ $totalGeneral > 0 ? round(($data['total'] / $totalGeneral) * 100, 2) : 0 }} %</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique Espaces (bar)
    const ctxEspaces = document.getElementById('chartEspaces').getContext('2d');
    new Chart(ctxEspaces, {
        type: 'bar',
        data: {
            labels: @json($chartEspacesLabels),
            datasets: [{
                label: 'Chiffre d\'affaires (Ar)',
                data: @json($chartEspacesData),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            maintainAspectRatio: false
        }
    });

    // Graphique Clients (doughnut)
    const ctxClients = document.getElementById('chartClients').getContext('2d');
    new Chart(ctxClients, {
        type: 'doughnut',
        data: {
            labels: @json($chartClientsLabels),
            datasets: [{
                data: @json($chartClientsData),
                backgroundColor: ['#28a745', '#20c997', '#17a2b8', '#ffc107', '#fd7e14']
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        padding: 15
                    }
                }
            }
        }
    });
</script>

<style>
    .chart-container {
        position: relative;
        height: 400px;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm 10mm;
        }

        body {
            font-size: 10pt;
            line-height: 1.3;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .no-print {
            display: none !important;
        }

        .chart-container {
            height: 200px !important;
        }

        .card {
            break-inside: avoid;
            margin-bottom: 20px;
            box-shadow: none;
            border: 1px solid #dee2e6;
        }

        table {
            font-size: 9pt;
        }

        .table th, .table td {
            padding: 6px 8px;
        }
    }
</style>
@endsection