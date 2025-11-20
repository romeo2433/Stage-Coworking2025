// public/js/reservations.js

document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = document.querySelectorAll('.reservation-checkbox');
    const totalDisplay = document.getElementById('totalSelected');
    const selectAll = document.getElementById('selectAll');

    // Fonction pour mettre à jour le total sélectionné
    function updateTotal() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.montant);
            }
        });
        if (totalDisplay) {
            totalDisplay.textContent = total.toLocaleString('fr-FR');
        }
    }

    // Événement sur chaque checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    // Événement sur la checkbox "Tout sélectionner"
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateTotal();
        });
    }

});
