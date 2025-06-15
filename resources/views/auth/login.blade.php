<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="E-KINERJA adalah aplikasi penggajian karyawan yang dirancang untuk memudahkan proses pengelolaan gaji, absensi, dan informasi karyawan. Aplikasi ini menyediakan fitur lengkap untuk menghitung gaji secara otomatis, mengelola data karyawan, dan menghasilkan laporan penggajian yang akurat.">
    <meta name="keywords" content="Aplikasi Penggajian Karyawan, Aplikasi Gaji, Aplikasi Payroll, Sistem Informasi Gaji, Sistem Informasi Penggajian">
    <meta name="author" content="sesil">

    <title>E-KINERJA LOGIN </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f6f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid #e8f4e8;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            padding: 40px 30px 30px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 400;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            width: 100%;
        }

        .form-control:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
            outline: none;
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
        }

        .password-toggle:hover {
            color: #22c55e;
            background: #f3f4f6;
        }

        .form-check {
            margin: 24px 0;
        }

        .form-check-input:checked {
            background-color: #22c55e;
            border-color: #22c55e;
        }

        .form-check-label {
            color: #6b7280;
            font-size: 14px;
        }

        .btn-login {
            background: #22c55e;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            width: 100%;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover {
            background: #16a34a;
        }

        .btn-login:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #22c55e;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
            color: #dc2626;
            font-size: 13px;
            margin-top: 4px;
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
                max-width: none;
            }

            .login-header {
                padding: 30px 20px 20px;
            }

            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1 class="login-title">Sistem Penggajian E-KINERJA</h1>
                    <p class="login-subtitle">Silakan masuk ke akun Anda</p>
                </div>

                <div class="login-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                                placeholder="Masukkan email Anda"
                            >
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-field">
                                <input
                                    id="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password Anda"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="far fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-login">
                            Masuk
                        </button>

                        Hubungi admin jika Anda lupa password atau tidak memiliki akun.

                        {{-- Uncomment if you want to add a "Forgot Password" link

                        {{-- @if (Route::has('password.request'))
                            <div class="forgot-password">
                                <a href="{{ route('password.request') }}">
                                    Lupa password?
                                </a>
                            </div> --}}
                        {{-- @endif --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'far fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'far fa-eye';
            }
        }
    </script>
</body>

</html>
