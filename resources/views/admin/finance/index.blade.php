@extends('admin.layout')

@section('title', 'Réservations en Espèces')

@section('content')

<div class="row">
    <!-- PREMIÈRE PARTIE - RÉSERVATIONS -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gradient-primary text-white py-2">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-check me-1"></i>Gestion des Réservations
                </h5>
            </div>
            <div class="card-body p-3">
                {{-- FORMULAIRE DE RECHERCHE --}}
                <div class="search-section bg-light rounded p-3 mb-3">
                    <form method="GET" action="{{ route('admin.finance.index') }}" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="reference" class="form-control form-control-sm" placeholder="Référence de réservation" value="{{ request('reference') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nom_utilisateur" class="form-control form-control-sm" placeholder="Nom du client" value="{{ request('nom_utilisateur') }}">
                        </div>
                        <div class="col-md-4 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-50">
                                <i class="bi bi-search me-1"></i>Rechercher
                            </button>
                            <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary btn-sm w-50">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>

                @if($reservations->isEmpty())
                    <div class="alert alert-info text-center py-2">
                        <i class="bi bi-info-circle me-1"></i>Aucune réservation trouvée.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="30" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Client</th>
                                <th>Dates</th>
                                <th>Référence</th>
                                <th>Espace</th>
                                <th>Statut</th>
                                <th>Montant</th>
                                <th>Paiement</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $res)
                                @php
                                    $paiement = $res->paiement ?? ($res->paiements->first() ?? null);
                                    $montant = $res->total - ($res->paiements->sum('montant_payer') ?? 0);
                                @endphp

                                <form action="{{ route('admin.reservations.payer', $res->Id_Reservation) }}" method="POST">
                                    @csrf
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input reservation-checkbox" data-montant="{{ $montant }}">
                                        </td>
                                        <td>
                                            <small class="fw-semibold">{{ $res->utilisateur->Prenom ?? '' }} {{ $res->utilisateur->Nom ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ \Carbon\Carbon::parse($res->date_debut)->format('d/m/Y H:i') }}</small>
                                            <small class="d-block">{{ \Carbon\Carbon::parse($res->date_fin)->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <small class="badge bg-light text-dark border">#{{ $res->Reference ?? $res->reference ?? '—' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $res->Espace->Nom ?? '—' }}</small>
                                        </td>
                                        <td>
                                            @if($res->Statut_Reservation === 'confirmee')
                                                <span class="badge bg-success badge-sm">Confirmée</span>
                                            @elseif($res->Statut_Reservation === 'terminee')
                                                <span class="badge bg-secondary badge-sm">Terminée</span>
                                            @else
                                                <span class="badge bg-warning text-dark badge-sm">{{ ucfirst($res->Statut_Reservation) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="fw-bold text-success">{{ number_format($montant, 0, ',', ' ') }} Ar</small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <input type="text" name="Reference" class="form-control form-control-xs" placeholder="Réf. paiement" value="{{ $paiement->Reference ?? '' }}">
                                                <input type="datetime-local" name="date_paiement" class="form-control form-control-xs" 
                                                    value="{{ $paiement && $paiement->date_paiement ? date('Y-m-d', strtotime($paiement->date_paiement)) : '' }}">
                                                <select name="Id_Mode" class="form-select form-select-xs">
                                                    @foreach($modes as $mode)
                                                        <option value="{{ $mode->Id_Mode }}" {{ ($paiement->Id_Mode ?? '') == $mode->Id_Mode ? 'selected' : '' }}>
                                                            {{ $mode->Type_Mode }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm w-100 py-1">
                                                <i class="bi bi-currency-dollar me-1"></i>
                                                {{ $paiement ? 'Sauvegarder' : 'Payer' }}
                                            </button>
                                        </td>
                                    </tr>
                                </form>        
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="total-section bg-light rounded p-2 mt-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0 fw-semibold small">Total sélectionné :</h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="text-success fw-bold mb-0" id="totalSelected">0 Ar</h6>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DEUXIÈME PARTIE - HISTORIQUE DES PAIEMENTS -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-success text-white py-2">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-1"></i>Historique des Paiements
                </h5>
            </div>
            <div class="card-body p-3">
                <!-- FORMULAIRE DE RECHERCHE AVANCÉE -->
                <div class="search-section bg-light rounded p-3 mb-3">
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
                        <div class="col-md-3">
                            <div class="row g-1">
                                <div class="col-6">
                                    <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-6">
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
                        <div class="col-md-12">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search me-1"></i>Rechercher
                                </button>
                                <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- STATISTIQUES -->
                <div class="stats-section mb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card bg-light border-0 py-1">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Total Général</small>
                                            <h6 class="mb-0 text-success fw-bold" id="total-paid">{{ number_format($totalPaid, 0, ',', ' ') }} Ar</h6>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-2">
                                            <i class="bi bi-wallet2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 py-1">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Total Sélectionné</small>
                                            <h6 class="mb-0 text-primary fw-bold" id="selected-sum">0 Ar</h6>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-2">
                                            <i class="bi bi-check-square"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($paiements->isEmpty())
                    <div class="alert alert-info text-center py-2">
                        <i class="bi bi-info-circle me-1"></i>Aucun paiement trouvé.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="30" class="text-center">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Client</th>
                                <th>Espace</th>
                                <th>Référence</th>
                                <th>Mode</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paiements as $paiement)
                                @php
                                    $totalReservation = $paiement->reservation?->total ?? 0;
                                    $pourcentagePaye = $totalReservation > 0 ? ($paiement->montant_payer / $totalReservation) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="form-check-input select-row"
                                               name="selected[]"
                                               value="{{ $paiement->Id_Paiement ?? $paiement->id ?? $paiement->Reference }}"
                                               data-amount="{{ (float) $paiement->montant_payer }}">
                                    </td>
                                    <td>
                                        <small class="fw-semibold">{{ $paiement->reservation?->utilisateur?->Prenom }} {{ $paiement->reservation?->utilisateur?->Nom }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $paiement->reservation?->espace?->Nom ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <small class="d-block fw-bold">#{{ $paiement->Reference }}</small>
                                        <small class="text-muted">Res: #{{ $paiement->reservation?->reference ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <small class="badge bg-light text-dark border">{{ $paiement->mode?->Type_Mode ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <small class="fw-bold text-success">{{ number_format($paiement->montant_payer, 0, ',', ' ') }} Ar</small>
                                    </td>
                                    <td>
                                        @if($paiement->statut_paiement == 'paye')
                                            <span class="badge bg-success badge-sm">Payé</span>
                                        @elseif($paiement->statut_paiement == 'partiel')
                                            <span class="badge bg-warning text-dark badge-sm">Partiel</span>
                                        @else
                                            <span class="badge bg-secondary badge-sm">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') : '—' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/Total.js') }}"></script>
<script src="{{ asset('js/PrixAngro.js') }}"></script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}
.search-section {
    border-left: 3px solid #007bff;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.5rem;
}
.table td {
    padding: 0.5rem;
    font-size: 0.85rem;
}
.card {
    border-radius: 8px;
}
.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.form-control-xs {
    padding: 0.2rem 0.5rem;
    font-size: 0.8rem;
    height: calc(1.5em + 0.4rem);
}
.form-select-xs {
    padding: 0.2rem 0.5rem;
    font-size: 0.8rem;
    height: calc(1.5em + 0.4rem);
}
.btn-close-sm {
    padding: 0.5rem;
    background-size: 0.6rem;
}
</style>
@endsection