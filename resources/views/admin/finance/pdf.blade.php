<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #777; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<h3>Historique des Paiements</h3>

<table>
    <thead>
        <tr>
            <th>Client</th>
            <th>Espace</th>
            <th>Référence</th>
            <th>Montant payé</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($paiements as $p)
        <tr>
            <td>{{ $p->reservation->utilisateur->Prenom ?? '' }} {{ $p->reservation->utilisateur->Nom ?? '' }}</td>
            <td>{{ $p->reservation->espace->Nom ?? '' }}</td>
            <td>{{ $p->Reference }}</td>
            <td>{{ number_format($p->montant_payer, 0, ',', ' ') }} Ar</td>
            <td>{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
