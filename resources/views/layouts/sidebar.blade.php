<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Tableau de bord -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Profil</span>
            </a>
        </li><!-- End Dashboard -->

        <!-- Types d'espaces -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('types_espaces.index') }}">
                <i class="bi bi-building"></i>
                <span>Types d'espaces</span>
            </a>
        </li><!-- End Types d'espaces -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('reservations.my') }}">
                <i class="bi bi-calendar-check"></i> <!-- tu peux choisir une icône appropriée -->
                <span>Mes Réservations</span>
            </a>
        </li><!-- End Mes Réservations -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('calendrier.index') }}">
              <i class="bi bi-calendar-event"></i>
              <span>Calendrier</span>
            </a>
          </li><!-- End Calendrier Nav -->        

       

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('connexion.create') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </li><!-- End Logout -->
    </ul>
</aside>



<script>
document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.querySelector('.toggle-sidebar-btn');
  const sidebar = document.querySelector('.sidebar');
  const content = document.querySelector('.main-content');
  const footer = document.querySelector('.footer');

  // Créer overlay pour mobile
  let overlay = document.querySelector('.mobile-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.classList.add('mobile-overlay');
    document.body.appendChild(overlay);
  }

  toggleBtn.addEventListener('click', () => {
    if (window.innerWidth <= 1199) {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    } else {
      sidebar.classList.toggle('closed');
      content.classList.toggle('expanded');
      footer.classList.toggle('expanded');
    }
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  });

  // Resize window
  window.addEventListener('resize', () => {
    if (window.innerWidth > 1199) {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    }
  });
});

</script>