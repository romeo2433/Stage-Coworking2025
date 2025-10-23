document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.type-filter');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const typeId = this.dataset.typeId;

            // Affichage conditionnel des sections
            if (typeId === 'all') {
                document.querySelectorAll('.type-section').forEach(s => s.style.display = 'block');
            } else {
                document.querySelectorAll('.type-section').forEach(s => s.style.display = 'none');
                document.getElementById(`type-${typeId}`).style.display = 'block';
            }

            // Style actif du bouton
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.add('btn-primary');
            this.classList.remove('btn-outline-primary');

            // Scroll fluide
            if (typeId !== 'all') {
                document.getElementById(`type-${typeId}`).scrollIntoView({ behavior: 'smooth' });
            }

            // Réinitialiser les animations
            setTimeout(initScrollAnimations, 300);
        });
    });

    initScrollAnimations();
});

// Fonction pour animer au scroll
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.scroll-animate');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const animation = el.dataset.animation;
                const delay = el.dataset.delay || 0;
                setTimeout(() => {
                    el.classList.add('animate__animated', `animate__${animation}`);
                    el.style.opacity = '1';
                }, delay);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    animatedElements.forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.animate-card');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));
});

