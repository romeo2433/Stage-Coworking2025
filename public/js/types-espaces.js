document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.type-filter');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const typeId = this.dataset.typeId;

            // Affichage des sections
            document.querySelectorAll('.type-section').forEach(section => {
                section.style.display = typeId === 'all' || section.id === `type-${typeId}` ? 'block' : 'none';
            });

            // Bouton actif
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-filter');
            });
            this.classList.add('active');

            // Scroll fluide
            if (typeId !== 'all') {
                document.getElementById(`type-${typeId}`).scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Animation au scroll (Animate.css)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__fadeInUp');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.espace-card').forEach(card => {
        observer.observe(card);
    });
});