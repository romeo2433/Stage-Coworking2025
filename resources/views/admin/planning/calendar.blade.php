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
    });
    calendar.render();
});
</script>
@endsection