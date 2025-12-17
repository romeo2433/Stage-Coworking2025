@extends('admin.layout')
@section('title', 'État Analytique')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>État Analytique des Paiements</h2>
        <div>
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

    <form method="GET" class="mb-4 card p-3">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label>Début</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="form-control">
            </div>
            <div class="col-auto">
                <label>Fin</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>

    <div class="alert alert-info">
        <strong>Période :</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total des paiements :</strong> {{ number_format($totalGeneral, 0, ',', ' ') }} Ar
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Top 5 Clients (Dépenses)</div>
                <div class="card-body">
                    <canvas id="chartClients"></canvas>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Top 5 Espaces (Chiffre d'affaires)</div>
                <div class="card-body">
                    <canvas id="chartEspaces"></canvas>
                </div>
            </div>
        </div>
       
    </div>

    <!-- Analyse par espace -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Analyse par Espace</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
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
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Analyse par Client</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
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

    <!-- Analyse par mode de paiement (si le champ existe) -->
    @if($paiementsParMode->count() > 0)
    <div class="card">
        <div class="card-header bg-warning text-dark">Répartition par Mode de Paiement</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Mode</th>
                            <th>Nbre</th>
                            <th>Total</th>
                            <th>Pourcentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiementsParMode as $mode => $data)
                            <tr>
                                <td>{{ ucfirst($mode ?? 'Non spécifié') }}</td>
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
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx1 = document.getElementById('chartEspaces').getContext('2d');
    new Chart(ctx1, {
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
        options: { scales: { y: { beginAtZero: true } } }
    });

    const ctx2 = document.getElementById('chartClients').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: @json($chartClientsLabels),
            datasets: [{
                data: @json($chartClientsData),
                backgroundColor: [
                    '#28a745', '#20c997', '#17a2b8', '#ffc107', '#fd7e14'
                ]
            }]
        }
    });
</script>
<style>
    @media print {
        /* Configuration générale de la page */
        @page {
            size: A4 portrait;      /* Portrait pour plus de lisibilité, ou 'landscape' si tu veux plus large */
            margin: 15mm 10mm;
        }
    
        body {
            font-size: 10pt !important;
            line-height: 1.3;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    
        .container {
            max-width: 100% !important;
            padding: 5px !important;
        }
    
        h2, h3 {
            font-size: 13pt !important;
            margin: 15px 0 10px 0 !important;
            page-break-after: avoid;   /* Évite de sauter une page juste après un titre */
        }
    
        .card {
            break-inside: avoid-page;  /* Très important : empêche de couper une card entre deux pages */
            break-before: auto;
            break-after: auto;
            margin-bottom: 20px !important;
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            page-break-inside: avoid;  /* Renforce l'interdiction de coupure interne */
        }
    
        .card-header {
            font-size: 12pt !important;
            padding: 8px !important;
            page-break-after: avoid;
        }
    
        .card-body {
            page-break-inside: avoid;
        }
    
        table {
            font-size: 9pt !important;
            width: 100% !important;
            page-break-inside: avoid;  /* Le tableau entier reste sur une seule page si possible */
        }
    
        thead { display: table-header-group; } /* Répète l'en-tête si tableau sur plusieurs pages (rare ici) */
        tfoot { display: table-footer-group; }
    
        /* Réduction des graphiques pour gagner de la place */
        canvas {
            max-height: 220px !important;
            width: 100% !important;
            margin: 10px 0;
        }
    
        /* Cacher les éléments inutiles à l'impression */
        .no-print, form, .btn {
            display: none !important;
        }
    
        /* Forcer un saut de page propre après certaines sections si besoin */
        .page-break-before {
            page-break-before: always;
        }
    
        /* Petite astuce : réduire les marges internes */
        .table th, .table td {
            padding: 6px 8px !important;
        }
    
        .alert {
            padding: 10px !important;
            margin-bottom: 15px !important;
        }
    }
    </style>

@endsection