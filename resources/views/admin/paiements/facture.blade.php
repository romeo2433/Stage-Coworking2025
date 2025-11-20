<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $paiement->Reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/img/ccia.png') }}" alt="Logo" width="40" class="me-2">
        <h2>FACTURE N° {{ $paiement->Reference }}</h2>
        <p>Date : {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}</p>
    </div>

    <p><strong>Client :</strong> {{ $reservation->utilisateur->Prenom }} {{ $reservation->utilisateur->Nom }}</p>
    <p><strong>Espace réservé :</strong> {{ $reservation->Espace->Nom }}</p>
    <p><strong>Durée :</strong> {{ $reservation->duree_heures }} h</p>

    <table>
        <tr><th>Description</th><th>Montant</th></tr>
        <tr>
            <td>Réservation du {{ $reservation->date_debut }} au {{ $reservation->date_fin }}</td>
            <td>{{ number_format($reservation->total, 0, ',', ' ') }} Ar</td>
        </tr>
    </table>

    <p><strong>Mode de paiement :</strong> Espèces</p>
    <h3 style="text-align:right;">Total payé : {{ number_format($paiement->montant_payer, 0, ',', ' ') }} Ar</h3>

    <p style="text-align:center; margin-top:20px;">Merci pour votre confiance !</p>
</body>
</html>
