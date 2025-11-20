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
<style>
   /* ----- Style desktop (inchangé) ----- */
   .sidebar {
  background: rgba(150, 170, 34, 0.9); /* bleu foncé semi-transparent */
  backdrop-filter: blur(5px);         /* effet de verre */
  box-shadow: 0 8px 32px 0 rgba(203, 224, 11, 0.15);
  border-radius: 20px 0 20px 0;
  animation: fadeInLeft 0.7s;
  width: 270px;
}
@keyframes fadeInLeft {
  0% { transform: translateX(-40px); opacity: 0;}
  100% { transform: translateX(0); opacity: 1;}
}
.sidebar-nav .nav-link {
  font-weight: 500;
  letter-spacing: 1px;
  color: #f7fafc;
  margin-bottom: 10px;
  border-radius: 12px;
  transition: background 0.3s, color 0.3s, transform 0.2s;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
  background: linear-gradient(90deg, #3b82f6 24%, #a6e7ff 88%);
  color: #233876;
  transform: translateX(8px) scale(1.05);
  box-shadow: 0 2px 15px rgba(22, 67, 178, 0.07);
}
.sidebar-nav i {
  background: #f0f3fa;
  color: #2563eb;
  border-radius: 50%;
  padding: 7px;
  margin-right: 12px;
  transition: background 0.3s, color 0.3s;
}

.sidebar-nav .nav-link:hover i {
  background: #233876;
  color: #fff;
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
    console.log("Toggle click"); 
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  });

  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });
});
</script>
    