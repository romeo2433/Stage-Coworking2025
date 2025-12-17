<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État Analytique des Paiements</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            margin: 20mm 15mm;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 18pt;
            margin-bottom: 5mm;
            color: #2c3e50;
        }
        h2 {
            text-align: center;
            font-size: 16pt;
            margin: 10mm 0 5mm 0;
            color: #2980b9;
        }
        h3 {
            font-size: 13pt;
            color: #27ae60;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 3px;
            margin-top: 12mm;
        }
        .header-info {
            text-align: center;
            margin-bottom: 15mm;
            font-size: 12pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12mm 0;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #bdc3c7;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            background-color: #ecf0f1 !important;
            font-weight: bold;
            font-size: 12pt;
        }
        .top-item {
            background-color: #fffacd !important;
            font-weight: bold;
        }
        .footer {
            position: running(footer);
            text-align: center;
            font-size: 9pt;
            color: #7f8c8d;
            margin-top: 20mm;
        }
        @page {
            margin: 20mm 15mm 25mm 15mm;
            @footnote {
                content: "Imprimé le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }} - Page " counter(page);
                border-top: 1px solid #bdc3c7;
                padding-top: 5px;
            }
        }
    </style>
</head>
<body>

    <h1>État Analytique des Paiements</h1>

    <div class="header-info">
        <p><strong>Période :</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
        <p><strong>Total général des paiements :</strong> {{ number_format($totalGeneral, 0, ',', ' ') }} Ar</p>
    </div>

    <!-- Analyse par Espace -->
    <h3>Analyse par Espace</h3>
    <table>
        <thead>
            <tr>
                <th>Espace</th>
                <th style="width:15%; text-align:center;">Nombre de paiements</th>
                <th style="width:20%;" class="text-right">Total (Ar)</th>
                <th style="width:15%;" class="text-right">% du CA</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @foreach($paiementsParEspace as $espace => $data)
                <tr @if($rank <= 5) class="top-item" @endif>
                    <td>{{ $espace }} @if($rank <= 5) (Top {{ $rank }}) @endif</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $totalGeneral > 0 ? round(($data['total'] / $totalGeneral) * 100, 2) : 0 }} %</td>
                </tr>
                @php $rank++; @endphp
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $paiementsParEspace->sum('count') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral, 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>100 %</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Analyse par Client -->
    <h3>Analyse par Client</h3>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th style="width:15%; text-align:center;">Nombre de paiements</th>
                <th style="width:20%;" class="text-right">Total (Ar)</th>
                <th style="width:15%;" class="text-right">% du CA</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @foreach($paiementsParClient as $client => $data)
                <tr @if($rank <= 5) class="top-item" @endif>
                    <td>{{ $client }} @if($rank <= 5) (Top {{ $rank }}) @endif</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $totalGeneral > 0 ? round(($data['total'] / $totalGeneral) * 100, 2) : 0 }} %</td>
                </tr>
                @php $rank++; @endphp
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $paiementsParClient->sum('count') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral, 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>100 %</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Répartition par Mode de Paiement -->
    @if($paiementsParMode->count() > 0)
    <h3>Répartition par Mode de Paiement</h3>
    <table>
        <thead>
            <tr>
                <th>Mode de paiement</th>
                <th style="width:20%; text-align:center;">Nombre</th>
                <th style="width:25%;" class="text-right">Total (Ar)</th>
                <th style="width:20%;" class="text-right">% du total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiementsParMode as $mode => $data)
                <tr>
                    <td>{{ ucfirst($mode ?? 'Non spécifié') }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $totalGeneral > 0 ? round(($data['total'] / $totalGeneral) * 100, 2) : 0 }} %</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $paiementsParMode->sum('count') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral, 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>100 %</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">
        Document généré automatiquement — Imprimé le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>