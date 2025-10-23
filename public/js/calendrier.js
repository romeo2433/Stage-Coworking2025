document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'fr',
      height: 650,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: calendarEventsUrl,
      dateClick: function(info) {
        fetch(`/api/espaces-disponibles?date=${info.dateStr}`)
          .then(r => r.json())
          .then(data => {
            alert('Espaces libres : ' + (data.length ? data.join(', ') : 'Aucun espace libre'));
          });
      }
    });
  
    calendar.render();
  });
  