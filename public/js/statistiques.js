document.addEventListener("DOMContentLoaded", function () {
    // --- Revenu mensuel ---
    const ctx = document.getElementById('revenuChart').getContext('2d');
    const revenusParMois = window.revenusParMois; // récupéré via Blade

    const labels = revenusParMois.map(item => item.label);
    const dataValues = revenusParMois.map(item => item.total);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenu mensuel (Ar)',
                data: dataValues,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value.toLocaleString() + ' Ar';
                        }
                    }
                }
            }
        }
    });

    // --- Réservations terminées ---
    const ctx2 = document.getElementById('reservationChart').getContext('2d');
    const reservationsParMois = window.reservationsParMois;

    const labels2 = reservationsParMois.map(item => item.label);
    const dataValues2 = reservationsParMois.map(item => item.total);

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: labels2,
            datasets: [{
                label: 'Réservations terminées',
                data: dataValues2,
                fill: true,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
