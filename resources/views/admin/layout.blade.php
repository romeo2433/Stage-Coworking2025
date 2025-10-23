<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Google Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('template/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/css/templatemo-style.css') }}">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
        }

        /* Navbar */
        .navbar {
            background-color: #343a40 !important;
            padding: 0.75rem 1rem;
        }

        .navbar-brand h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        .navbar-nav .nav-link {
            color: #f8f9fa !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107 !important;
        }

        /* Contenu */
        main.container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }

        /* Footer */
        footer.tm-footer {
            background-color: #343a40;
            color: #fff;
            padding: 1rem 0;
        }

        footer a.tm-footer-link {
            color: #ffc107;
            text-decoration: none;
        }

        footer a.tm-footer-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
@yield('styles')

<body>
    

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Logo et titre -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('template/images/logo.png') }}" alt="Logo" width="40" class="me-2">
                <h1 class="tm-site-title mb-0 text-white">Product Admin</h1>
            </a>

            <!-- Bouton mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.paiements.index') }}">
                            <i class="fas fa-money-bill-wave me-2"></i> Paiements
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="{{ route('admin.reservations.index') }}">
                            <i class="far fa-user me-1"></i> Reservation
                        </a>
                    </li> 
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
            <p class="mb-0">
                &copy; {{ date('Y') }} <strong>Product Admin</strong> – Tous droits réservés.<br>
                Design : <a href="https://templatemo.com" class="tm-footer-link" rel="nofollow noopener">Template Mo</a>
            </p>
        </div>
    </footer>

    <!-- JS -->
    <script src="{{ asset('template/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('template/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
