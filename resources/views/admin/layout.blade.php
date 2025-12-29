{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- CSS GLOBAL (hérité partout) -->
    <link rel="stylesheet" href="/css/main.css">

    <!-- Google Fonts (si tu veux) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome (pour les icônes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 (via CDN ou local) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

     <!-- Icone -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Ou la version slim si tu veux -->
<!-- <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script> -->


<!-- Ton script personnalisé APRÈS jQuery -->
    <script src="{{ asset('js/ton-script.js') }}"></script> <!-- ou inline -->


    <!-- STYLES SPÉCIFIQUES (surcharge possible) -->
    @yield('styles')
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('assets/img/ccia.png') }}" alt="Logo" width="40" class="me-2">
                <h1 class="tm-site-title mb-0 text-white">Product Admin</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
            
                    <!-- Dashboard -->
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Bienvenue
                        </a>
                    </li>
            
                    <!-- Création client -->
                   
            
                    <!-- Nouvelle réservation -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.reservations.create') }}" 
                           class="nav-link {{ request()->routeIs('admin.reservations.create') ? 'active' : '' }}">
                            <i class="fas fa-calendar-plus me-2"></i> Nouvelle réservation
                        </a>
                    </li>
            
                    <!-- Réservations en attente -->
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ request()->routeIs('admin.reservations.index') ? 'active' : '' }}" 
                           href="{{ route('admin.reservations.index') }}">
                            <i class="far fa-user me-1"></i> Réservations 
                        </a>
                    </li>
            
                    <!-- PAGE FUSIONNÉE PAIEMENTS + RÉSERVATIONS PAYÉES -->
                    <li class="nav-item mx-2">
                        <a class="nav-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}" 
                           href="{{ route('admin.finance.index') }}">
                            <i class="fas fa-hand-holding-usd me-1"></i> Paiements
                        </a>
                    </li>
            
                   
            
                    <!-- Planning général -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.planning.calendar') }}" 
                           class="nav-link {{ request()->routeIs('admin.planning.calendar') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt me-2"></i> Planning général
                        </a>
                    </li>
            
                    <!-- Paramètres Espaces -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.espaces.photos') }}" 
                           class="nav-link {{ request()->routeIs('admin.espaces.photos') ? 'active' : '' }}">
                            <i class="fas fa-image me-2"></i> Paramètres d'Espaces
                        </a>
                    </li>
            
                    <!-- Statistiques -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.statistiques.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.statistiques.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-line me-2"></i> Tableau de Bord
                        </a>
                    </li>
            
                    <!-- Déconnexion -->
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </a>
                    </li>
            
                </ul>
            </div>
            
        </div>
    </nav>

    <!-- CONTENU PRINCIPAL -->
    <main class="container">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="tm-footer">
        <div class="text-center small">
            &copy; 2025 Chambre de Commerce et d’Industrie d’Antananarivo.<br>
            Tous droits réservés. |
            <a href="https://www.cci.mg" target="_blank">cci.mg</a>
            Plus de collaboration contactez Developper <a href="https://www.linkedin.com/in/mahefa-rom%C3%A9o-irivelo-3b4663362/" target="_blank">Dev E-Toerana</a> 
        </div>
    </footer>
   
    

    <!-- JS Scripts spécifiques -->
    <script src="/template/js/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

     
    @yield('scripts')
</body>
</html>