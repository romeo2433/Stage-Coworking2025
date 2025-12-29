@extends('admin.layout')

@section('title', 'Planning général')

@section('content')
<div class="container-fluid mt-4">
    <form method="GET" action="{{ route('admin.planning.calendar') }}" class="row g-2 mb-3">
        <div class="col-md-2">
            <select name="espace" class="form-control form-control-sm">
                <option value="">-- Tous les espaces --</option>
                @foreach($espaces as $espace)
                    <option value="{{ $espace->Id_Espace }}" {{ $selectedEspace == $espace->Id_Espace ? 'selected' : '' }}>
                        {{ $espace->Nom }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <div class="col-md-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $selectedDate }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            <a href="{{ route('admin.planning.calendar') }}" class="btn btn-secondary btn-sm">Réinitialiser</a>
        </div>
        <div class="col-md-2">
            <select name="heure" class="form-control form-control-sm">
                <option value="">-- Heure de disponibiliter --</option>
                @if($selectedEspace && $selectedDate)
                    @foreach($toutesLesHeures as $h)
                        <option value="{{ $h['heure'] }}" {{ $selectedHeure == $h['heure'] ? 'selected' : '' }}>
                            {{ $h['heure'] }} - {{ $h['statut'] }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </form>
    
    @if($disponibilite)
        <div class="alert alert-info mt-2">
            @if($selectedHeure)
                Statut pour l'espace choisi à la date et l'heure sélectionnées : <strong>{{ $disponibilite }}</strong>
            @else
                Statut général pour l'espace à la date sélectionnée : <strong>{{ $disponibilite }}</strong>
            @endif
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-3 text-primary">
                <i class="fas fa-calendar-alt"></i> Planning des Espaces
            </h4>

            <div id='calendar'></div>
        </div>
    </div>
</div>
<!-- Modale des détails de réservation -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventModalLabel">
                    <i class="fas fa-info-circle"></i> Détails de la réservation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Client :</strong> <span id="modal-client"></span></p>
                        <p><strong>Téléphone :</strong> <span id="modal-telephone"></span></p>
                        <p><strong>Email :</strong> <span id="modal-email"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Espace :</strong> <span id="modal-espace"></span></p>
                        <p><strong>Début :</strong> <span id="modal-debut"></span></p>
                        <p><strong>Fin :</strong> <span id="modal-fin"></span></p>
                        <p><strong>Statut :</strong> <span id="modal-statut" class="badge bg-secondary"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
{{-- FullCalendar --}}
<link rel="stylesheet" href="https://unpkg.com/fullcalendar@6.1.8/index.global.min.css">
<script src="https://unpkg.com/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json($events),
    
            // Action au clic sur un événement
            eventClick: function(info) {
                // Récupération des données personnalisées
                var props = info.event.extendedProps;
    
                // Remplissage de la modale
                document.getElementById('modal-client').textContent = props.client;
                document.getElementById('modal-telephone').textContent = props.telephone;
                document.getElementById('modal-email').textContent = props.email;
                document.getElementById('modal-espace').textContent = props.espace;
                document.getElementById('modal-debut').textContent = props.date_debut;
                document.getElementById('modal-fin').textContent = props.date_fin;
                document.getElementById('modal-statut').textContent = props.statut;
    
                // Optionnel : changer la couleur du badge selon le statut
                var statutBadge = document.getElementById('modal-statut');
                statutBadge.className = 'badge bg-secondary';
                if (props.statut === 'confirmee') {
                    statutBadge.classList.replace('bg-secondary', 'bg-success');
                } else if (props.statut === 'annulee') {
                    statutBadge.classList.replace('bg-secondary', 'bg-danger');
                } else if (props.statut === 'terminee') {
                    statutBadge.classList.replace('bg-secondary', 'bg-warning');
                }
    
                // Afficher la modale (Bootstrap 5)
                var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                modal.show();
            }
        });
    
        calendar.render();
    });
    </script>
@endsection