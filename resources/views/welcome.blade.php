<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Penggajian</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: #01930a;
        }

        .navbar {
            background-color: #3498db;
        }

        .navbar-brand {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #ffffff;
        }

        .welcome-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .welcome-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .welcome-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        .welcome-links a {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #ffffff;
            font-weight: bold;
        }

        .welcome-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>


    <div class="welcome-container">
        <div class="welcome-card">
            <div class="text-center">
                {{-- <img src="/img/hi.jfif" alt="Logo" class="welcome-logo"> --}}
            </div>
            <p class="text-center">Selamat datang di Aplikasi Penggajian<br/>
            </p>
            <div class="welcome-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success">Masuk</a>

                    @endauth
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
