function verifierCheckbox() {
    const checkbox = document.getElementById('not_robot');
    if (!checkbox.checked) {
        alert("Veuillez confirmer que vous n'êtes pas un robot avant de continuer.");
        return false; // Empêche l’envoi du formulaire
    }
    return true;
}