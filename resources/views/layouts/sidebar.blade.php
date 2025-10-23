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

        <li class="nav-heading">Administration</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('connexion.create') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </li><!-- End Logout -->
    </ul>
</aside>
<style>
   /* ----- Style desktop (inchangé) ----- */
.sidebar {
  width: 250px;
  position: fixed;
  top: 60px;
  left: 0;
  height: calc(100vh - 60px);
  background-color: #fff;
  transition: transform 0.3s ease;
  z-index: 1000;
  box-shadow: 0 0 10px rgba(165, 34, 34, 0.507);
}

.main-content {
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  background-color: rgba(255, 255, 255, 0.8); 
    padding: 20px;
    border-radius: 10px;
}
.dashboard-container {
  display: flex;
  overflow: visible; 
}


/* ----- Mode sombre 
body.dark-mode {
  background-color: #121212;
  color: #f1f1f1;
}
body.dark-mode .sidebar {
  background-color: #1a1a1a;
}
body.dark-mode a {
  color: #90caf9;
}----- */

/* ----- Responsive mobile ----- */
@media (max-width: 991px) {
  .sidebar {
    transform: translateX(-100%);
    position: fixed;
    top: 60px;
    left: 0;
    width: 250px;
    height: calc(100vh - 60px);
    background-color: #1f2937;
    z-index: 1110;
    box-shadow: 2px 0 8px rgba(0,0,0,0.3);
    transition: transform 0.3s ease-in-out;
  }

  .sidebar.active {
    transform: translateX(0);
  }

  .main-content {
    margin-left: 0 !important;
  }

  .mobile-overlay {
    display: none;
    position: fixed;
    top: 60px;
    left: 0;
    width: 100%;
    height: calc(100vh - 60px);
   
    z-index: 1100; /* en dessous de la sidebar */
  }

  .mobile-overlay.active {
    display: block;
  }
}




</style>


<script>
   document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.querySelector(".toggle-sidebar-btn"); 
  const sidebar = document.querySelector(".sidebar");

  if (!toggleBtn || !sidebar) return;

  // Créer overlay
  const overlay = document.createElement("div");
  overlay.classList.add("mobile-overlay");
  document.body.appendChild(overlay);

  toggleBtn.addEventListener("click", () => {
    console.log("Toggle click"); // Vérifie dans la console mobile
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  });

  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });
});
</script>
    