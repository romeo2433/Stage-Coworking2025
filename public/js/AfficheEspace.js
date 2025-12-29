function showReservation(reservationId) {
    const modalBody = document.getElementById('reservationDetailsContent');

    // Afficher un loader
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-3">Chargement des détails...</p>
        </div>
    `;

    // Charger le contenu via AJAX
    fetch(`/admin/reservation/${reservationId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Réservation non trouvée');
            }
            return response.text();
        })
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Erreur lors du chargement des détails : ${err.message}
                </div>
            `;
        });

    // Ouvrir la modale Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
    modal.show();
}