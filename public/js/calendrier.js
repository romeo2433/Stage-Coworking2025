document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
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

                  const message = data.length
                      ? "Espaces libres :<br><b>" + data.join(', ') + "</b>"
                      : "Aucun espace libre pour cette date.";

                  Swal.fire({
                      title: info.dateStr,
                      html: message,
                      icon: data.length ? 'success' : 'warning'
                  });

              });
      }
  });

  calendar.render();
});
