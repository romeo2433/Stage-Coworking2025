@extends('layouts.app')

@section('title', 'Paiement')

@section('content')
<div class="container">
    <h2>Paiement de la réservation #{{ $reservation->reference }}</h2>
    <p><strong>Montant total :</strong> {{ number_format($reservation->total, 0, ',', ' ') }} Ar</p>

    <form action="{{ route('paiements.store') }}" method="POST">
        @csrf
        <input type="hidden" name="Id_Reservation" value="{{ $reservation->Id_Reservation }}">

        <div class="mb-3">
            <label for="Id_Mode" class="form-label">Mode de paiement :</label>
            <select name="Id_Mode" id="Id_Mode" class="form-select" required>
                <option value="">-- Choisir un mode --</option>
                @foreach($modes as $mode)
                    {{-- On standardise le type: espece / en_ligne --}}
                    <option value="{{ $mode->Id_Mode }}" data-type="{{ strtolower($mode->Type_Mode) }}">{{ $mode->Type_Mode }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="montant_payer" class="form-label">Montant à payer :</label>
            <input type="number" name="montant_payer" id="montant_payer" class="form-control" step="0.01" value="{{ $reservation->total }}">
        </div>

        <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
    </form>
</div>

<script>
    const selectMode = document.getElementById('Id_Mode');
    const montantInput = document.getElementById('montant_payer');

    selectMode.addEventListener('change', function() {
        const selectedOption = selectMode.selectedOptions[0];
        const typeMode = selectedOption.dataset.type;

    if (typeMode === 'espèces') {
    montantInput.value = {{ $reservation->total }};
    montantInput.readOnly = true;
    }
    else {
        montantInput.readOnly = false;
    }

    });
</script>
@endsection
