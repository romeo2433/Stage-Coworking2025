@extends('admin.layout')

@section('title', 'Historique des paiements')

@section('content')

<div class="reservation-wrapper" >
    <div class="card shadow-sm mb-5" >
        <div class="card p-3 mb-4" style="max-width: 950px; margin: auto;">
            <form method="GET" action="{{ route('admin.paiements.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="client" class="form-control form-control-sm" placeholder="Client" value="{{ request('client') }}">
                </div>
        
                <div class="col-md-3">
                    <input type="text" name="reference" class="form-control form-control-sm" placeholder="Référence" value="{{ request('reference') }}">
                </div>
        
                <div class="col-md-3">
                    <select name="espace" class="form-control form-control-sm">
                        <option value="">-- Tous les espaces --</option>
                        @foreach($espaces as $espace)
                            <option value="{{ $espace->Id_Espace }}" {{ (string) request('espace') === (string) $espace->Id_Espace ? 'selected' : '' }}>
                                {{ $espace->Nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col">
                            <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col">
                            <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
                        </div>
                    </div>
                </div>                
                <div class="col-md-3">
                    <select name="mois" class="form-control form-control-sm">
                        <option value="">Mois</option>
                        @foreach([
                            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                        ] as $num => $name)
                            <option value="{{ $num }}" {{ request('mois') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <div class="col-md-3">
                    <select name="annee" class="form-control form-control-sm">
                        <option value="">Année</option>
                        @for ($year = now()->year; $year >= now()->year - 10; $year--)
                            <option value="{{ $year }}" {{ request('annee') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
        
                <div class="col-md-12 d-flex align-items-center gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">Rechercher</button>
                    <a href="{{ route('admin.paiements.index') }}" class="btn btn-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
        
    
    <h2 class="mb-4">Historique des paiements - Espaces payés</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mt-3">
        <h5><strong>Total général :</strong> 
            <span id="total-paid">{{ number_format($totalPaid, 0, ',', ' ') }} Ar</span>
        </h5>
    
        <h5><strong>Total sélectionné :</strong> 
            <span id="selected-sum">0 Ar</span>
        </h5>
    </div>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>
                    <input type="checkbox" id="select-all">
                </th>
                <th>Client</th>
                <th>Espace</th>
                <th>Référence paiement</th>
                <th>Référence réservation</th>
                <th>Mode</th>
                <th>Montant payé</th>
                <th>Statut</th>
                <th>Date paiement</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paiements as $paiement)
                @php
                    $totalReservation = $paiement->reservation?->total ?? 0;
                    $pourcentagePaye = $totalReservation > 0 ? ($paiement->montant_payer / $totalReservation) * 100 : 0;
                @endphp
                <tr>
                    <td>
                        <input type="checkbox"
                               class="select-row"
                               name="selected[]"
                               value="{{ $paiement->Id_Paiement ?? $paiement->id ?? $paiement->Reference }}"
                               data-amount="{{ (float) $paiement->montant_payer }}">
                    </td>
                    <td>{{ $paiement->reservation?->utilisateur?->Prenom }} {{ $paiement->reservation?->utilisateur?->Nom }}</td>
                    <td>{{ $paiement->reservation?->espace?->Nom ?? '—' }}</td>
                    <td>#{{ $paiement->Reference }}</td>
                    <td>#{{ $paiement->reservation?->reference ?? '—' }}</td>
                    <td>{{ $paiement->mode?->Type_Mode ?? '—' }}</td>
                    <td class="amount-cell">{{ number_format($paiement->montant_payer, 0, ',', ' ') }} Ar</td>
                    <td>
                        @if($paiement->statut_paiement == 'paye')
                            <span class="badge bg-success">Payé</span>
                        @elseif($paiement->statut_paiement == 'partiel')
                            <span class="badge bg-warning text-dark">Partiel</span>
                        @else
                            <span class="badge bg-secondary">En attente</span>
                        @endif
                    </td>
                    <td>{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') : '—' }}</td>
                </tr>
                
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Aucun paiement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
   
</div>
</div>
<script src="{{ asset('js/Total.js') }}"></script>
@endsection