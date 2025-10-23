@extends('layouts.app')

@section('title', 'Réserver : ' . $espace->Nom)

@section('content')
<div class="container">
    <h2>Réserver : {{ $espace->Nom }}</h2>
    <img src="{{ $espace->photo 
        ? asset('storage/espaces/' . $espace->photo) 
        : asset('images/default.jpg') }}" 
        class="card-img-top" 
        alt="{{ $espace->Nom }}"
        style="height: 220px; object-fit: cover; border-radius: 10px 10px 0 0;">

    <form action="{{ route('reservations.store') }}" method="POST">
        @csrf
        <input type="hidden" name="Id_Espace" value="{{ $espace->Id_Espace }}">
        <input type="hidden" id="tarif_horaire" value="{{ $espace->tarif_horaire }}">
        <input type="hidden" id="espace_id" value="{{ $espace->Id_Espace }}">

        <label>Date :</label>
        <input type="date" name="date_debut" id="date_debut" required><br><br>
        
        <label>Heure de début :</label>
        <select name="heure_debut" id="heure_debut" required>
            <option value="">-- Sélectionnez une date d’abord --</option>
        </select><br><br>
        
        <label>Durée (heures) :</label>
        <input type="number" name="duree" id="duree" required min="1" max="8" value="1"><br><br>

        <label>Date/Heure de fin :</label>
        <input type="text" id="date_fin" readonly style="background:#f0f0f0"><br><br>
        <label><strong>Tarif horaire :</strong> {{ number_format($espace->tarif_horaire, 2) }} Ar</label>

        <label>Équipements disponibles :</label><br>
        @if($espace->equipements->isEmpty())
            <p>Aucun équipement disponible pour cet espace.</p>
        @else
            @foreach($espace->equipements as $equipement)
                <input type="checkbox" name="equipements[]" 
                       class="equipement-checkbox" 
                       value="{{ $equipement->Id_Equipement }}" 
                       data-prix="{{ $equipement->prix }}">
                {{ $equipement->nom }} ({{ number_format($equipement->prix, 0, ',', ' ') }} Ar)<br>
            @endforeach
        @endif

        <br>

        <label>Total à payer (Ar) :</label>
        <input type="text" id="total" name="total" readonly style="background:#f0f0f0"><br><br>

        <button type="submit">Valider</button>
    </form>
</div>

<script src="{{ asset('js/reservation.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/reservation.css') }}">
@endsection
