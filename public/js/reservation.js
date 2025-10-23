document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date_debut');
    const heureSelect = document.getElementById('heure_debut');
    const dureeInput = document.getElementById('duree');
    const dateFinInput = document.getElementById('date_fin');
    const totalInput = document.getElementById('total');
    const tarifHoraire = parseFloat(document.getElementById('tarif_horaire').value);
    const equipementCheckboxes = document.querySelectorAll('.equipement-checkbox');
    const espaceId = document.getElementById('espace_id').value;

    // 🔹 Charger les heures disponibles quand on change la date
    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        heureSelect.innerHTML = '<option>Chargement...</option>';
        fetch(`/espaces/${espaceId}/disponibilites?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                heureSelect.innerHTML = '';
                if (data.availableSlots && data.availableSlots.length > 0) {
                    data.availableSlots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        heureSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'Aucune heure disponible';
                    heureSelect.appendChild(option);
                }
            })
            .catch(() => {
                heureSelect.innerHTML = '<option>Erreur de chargement</option>';
            });
    });

    // 🔹 Mettre à jour la date/heure de fin
    function updateDateFin() {
        const date = dateInput.value;
        const heureDebut = heureSelect.value;
        const duree = parseFloat(dureeInput.value);

        if (date && heureDebut && duree > 0) {
            const dateTimeDebut = new Date(`${date}T${heureDebut}`);
            const dateTimeFin = new Date(dateTimeDebut.getTime() + duree * 60 * 60 * 1000);
            const h = String(dateTimeFin.getHours()).padStart(2, '0');
            const m = String(dateTimeFin.getMinutes()).padStart(2, '0');
            dateFinInput.value = `${date} ${h}:${m}`;
        } else {
            dateFinInput.value = '';
        }
    }

    // 🔹 Calculer le total à payer
    function calculerTotal() {
        const duree = parseFloat(dureeInput.value);
        if (!isNaN(duree)) {
            let total = tarifHoraire * duree;

            equipementCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.prix);
                }
            });

            totalInput.value = total.toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' Ar';
        } else {
            totalInput.value = '';
        }
    }

    // 🔹 Écouteurs d’événements
    heureSelect.addEventListener('change', () => { updateDateFin(); calculerTotal(); });
    dureeInput.addEventListener('input', () => { updateDateFin(); calculerTotal(); });
    equipementCheckboxes.forEach(cb => cb.addEventListener('change', calculerTotal));
});
