<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #777; padding: 8px; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>

<h3 style="text-align:center">Détail Paiement</h3>

<table>
    <tr>
        <th>Client</th>
        <td>{{ $paiement->reservation->utilisateur->Prenom }} {{ $paiement->reservation->utilisateur->Nom }}</td>
    </tr>
    <tr>
        <th>Espace</th>
        <td>{{ $paiement->reservation->espace->Nom }}</td>
    </tr>
    <tr>
        <th>Référence Paiement</th>
        <td>{{ $paiement->Reference }}</td>
    </tr>
    <tr>
        <th>Montant Payé</th>
        <td>{{ number_format($paiement->montant_payer, 0, ',', ' ') }} Ar</td>
    </tr>
    <tr>
        <th>Date Paiement</th>
        <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y ') }}</td>
    </tr>
    <tr>
        <th>Mode Paiement</th>
        <td>{{ $paiement->mode->Type_Mode }}</td>
    </tr>
</table>

</body>
</html>
