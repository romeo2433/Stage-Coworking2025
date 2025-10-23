@extends('layouts.app')

@section('title', 'Calendrier des Réservations')

@section('content')
<div class="pagetitle">
  <h1>Calendrier des Réservations</h1>
</div>

<section class="section dashboard">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Visualisation des espaces réservés et disponibles</h5>

      <!-- Zone du calendrier -->
      <div id="calendar"></div>

    </div>
  </div>
</section>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Injection d’une variable JS -->
<script>
  const calendarEventsUrl = "{{ route('api.reservations') }}";
</script>

<!-- Ton script séparé -->
<script src="{{ asset('js/calendrier.js') }}"></script>
@endsection
