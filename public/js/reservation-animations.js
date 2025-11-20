// public/js/reservation-animations.js

document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.reservation-card');

    // Animation séquentielle à l'apparition
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150); // décalage entre les cartes
    });

    // Petit effet "pulse" quand on clique
    cards.forEach(card => {
        card.addEventListener('click', () => {
            card.classList.add('pulse');
            setTimeout(() => card.classList.remove('pulse'), 500);
        });
    });
});
