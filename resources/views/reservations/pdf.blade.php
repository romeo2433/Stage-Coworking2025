<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation #{{ $reservation->Id_Reservation }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        h2 { text-align: center; color: #333; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #999; padding: 8px; }
    </style>
</head>
<body>
    <h2>Récapitulatif de Réservation</h2>

    <div class="section">
        <strong>Numéro :</strong> {{ $reservation->Id_Reservation }} <br>
        <strong>Client :</strong> {{ $reservation->utilisateur->Prenom }} {{ $reservation->utilisateur->Nom }} <br>
        <strong>Email :</strong> {{ $reservation->utilisateur->email }}
    </div>

    <div class="section">
        <strong>Espace réservé :</strong> {{ $reservation->espace->Nom_Espace }} <br>
        <strong>Date début :</strong> {{ $reservation->date_debut }} <br>
        <strong>Date fin :</strong> {{ $reservation->date_fin }}
    </div>

    <div class="section">
        <h4>Équipements :</h4>
        <ul>
            @foreach($reservation->equipements as $eq)
                <li>{{ $eq->nom }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <strong>Montant total :</strong> {{ number_format($reservation->total, 0, ',', ' ') }} Ar
    </div>
</body>
</html>
