<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @include('layouts.header')
    <div class="dashboard-container d-flex">
        @include('layouts.sidebar')
        <main class="main-content flex-grow-1 p-4" id="main-content">
            @yield('content')
        </main>
    </div>
    @include('layouts.footer')
   
</body>
</html>
