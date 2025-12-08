document.addEventListener('DOMContentLoaded', function () {
    // === TOUS LES ÉLÉMENTS ===
    const espaceSelect         = document.getElementById('Id_Espace');
    const dateInput            = document.getElementById('date_debut');
    const heureSelect          = document.getElementById('heure_debut');
    const dureeInput           = document.getElementById('duree');
    const abonnementSelect     = document.getElementById('Id_Abonnement');
    const equipementsContainer = document.getElementById('equipements-container');
    const totalInput           = document.getElementById('total');
    const totalHidden          = document.getElementById('total_hidden');

    // LES 3 CHAMPS DURÉE
    const champHeures = document.getElementById('champ-duree-heures');
    const champJours  = document.getElementById('champ-duree-jours');
    const champMois   = document.getElementById('champ-duree-mois');

    let tarifHoraire = 0;
    let equipements  = [];
    let quantiteMaxEspace = 1;
    

    // GESTION AFFICHAGE SELON ABONNEMENT
    function gererChampsAbonnement() {
        const selectedOption = abonnementSelect.options[abonnementSelect.selectedIndex];
        const type = selectedOption?.dataset.type || '';

        // Cacher tous les champs
        champHeures.style.display = 'none';
        champJours.style.display  = 'none';
        champMois.style.display   = 'none';

        heureSelect.disabled = false;
        heureSelect.required = true;

        if (type === 'journalier') {
            champJours.style.display = 'block';
            //heureSelect.disabled = true;
            heureSelect.required = false;
            heureSelect.value = '';
        }
        else if (type === 'mensuel') {
            champMois.style.display = 'block';
            //heureSelect.disabled = true;
            heureSelect.required = false;
            heureSelect.value = '';
        }
        else {
            champHeures.style.display = 'block';
            heureSelect.disabled = false;
            heureSelect.required = true;
        }

        calculerTotal(); // Important
    }

    // CALCUL TOTAL — CORRIGÉ À 100%
    function calculerTotal() {
        let total = 0;
    
        // 1️⃣ Équipements
        equipements.forEach(e => {
            if (e.checkbox?.checked) total += parseFloat(e.prix || 0);
        });
    
        // 2️⃣ Abonnement ou tarif horaire
        const selectedOption = abonnementSelect.options[abonnementSelect.selectedIndex];
        const quantite = parseInt(document.getElementById('quantite_reservation')?.value) || 1;
    
        if (selectedOption && selectedOption.value !== '') {
            const type = selectedOption.dataset.type || '';
    
            if (type === 'journalier') {
                const tarif = parseFloat(selectedOption.dataset.tarif_journalier || 0);
                const jours = parseInt(document.getElementById('duree_jour')?.value) || 1;
                total += (tarif * jours);
            }
            else if (type === 'mensuel') {
                const tarif = parseFloat(selectedOption.dataset.tarif_mensuel || 0);
                const mois = parseInt(document.getElementById('duree_mois')?.value) || 1;
                total += (tarif * mois);
            }
            else {
                // Tarif horaire
                const heures = parseFloat(dureeInput.value) || 1;
                total += tarifHoraire * heures;
            }
        } else {
            // Aucun abonnement sélectionné → tarif horaire
            const heures = parseFloat(dureeInput.value) || 1;
            total += tarifHoraire * heures;
        }
    
        // 3️⃣ Multiplier par la quantité
        total *= quantite;
    
        totalInput.value = total.toLocaleString('fr-FR') + ' Ar';
        totalHidden.value = Math.round(total);
    }
    
    

    // CHARGER ÉQUIPEMENTS
    function chargerEquipements() {
        const id = espaceSelect.value;
        if (!id) {
            equipementsContainer.innerHTML = '<p class="text-muted">Sélectionnez un espace.</p>';
            tarifHoraire = 0;
            calculerTotal();
            return;
        }
        fetch(`/admin/espaces/${id}/details`)
            .then(r => r.json())
            .then(data => {
                tarifHoraire = parseFloat(data.tarif_horaire || 0);
                quantiteMaxEspace = data.quantite || 1;
                equipementsContainer.innerHTML = `<p><strong>Tarif horaire :</strong> ${tarifHoraire.toLocaleString('fr-FR')} Ar</p><hr>`;
                equipements = [];
                

                if (data.equipements?.length > 0) {
                    data.equipements.forEach(e => {
                        const div = document.createElement('div');
                        div.className = 'form-check mb-2';
                        const cb = document.createElement('input');
                        cb.type = 'checkbox'; cb.className = 'form-check-input'; cb.name = 'equipements[]';
                        cb.value = e.Id_Equipement; cb.dataset.prix = e.prix;
                        const label = document.createElement('label');
                        label.className = 'form-check-label ms-2';
                        label.textContent = `${e.nom} (+${parseInt(e.prix).toLocaleString('fr-FR')} Ar)`;
                        div.append(cb, label);
                        equipementsContainer.appendChild(div);
                        equipements.push({ checkbox: cb, prix: e.prix });
                        cb.addEventListener('change', calculerTotal);
                    });
                } else {
                    equipementsContainer.innerHTML += '<p class="text-muted">Aucun équipement.</p>';
                }
                calculerTotal();
            });
    }

    // CHARGER HEURES
    function chargerHeuresDisponibles() {
        const id = espaceSelect.value;
        const date = dateInput.value;
        if (!id || !date) {
            heureSelect.innerHTML = '<option value="">-- Choisir --</option>';
            return;
        }
        fetch(`/admin/espaces/${id}/heures-disponibles?date=${date}`)
            .then(r => r.json())
            .then(data => {
                heureSelect.innerHTML = '<option value="">-- Choisir --</option>';
                if (data.disponibles?.length > 0) {
                    data.disponibles.forEach(h => heureSelect.add(new Option(h, h)));
                } else {
                    heureSelect.add(new Option('Aucune heure disponible', '', true, true));
                    heureSelect.disabled = true;
                }
            });
    }

    // ÉVÉNEMENTS
    abonnementSelect.addEventListener('change', gererChampsAbonnement);
    espaceSelect.addEventListener('change', () => { chargerEquipements(); chargerHeuresDisponibles(); });
    dateInput.addEventListener('change', chargerHeuresDisponibles);
    dureeInput.addEventListener('input', calculerTotal);
    document.getElementById('duree_jour')?.addEventListener('input', calculerTotal);
    document.getElementById('duree_mois')?.addEventListener('input', calculerTotal);
    document.getElementById('quantite_reservation').addEventListener('input', calculerTotal);


    // INIT
    chargerEquipements();
    gererChampsAbonnement();
});