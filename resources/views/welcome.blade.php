<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CV PURWA PUTRA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #05ec14;
            --secondary: #6b7280;
            --accent: #f3f4f6;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border: #e5e7eb;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container-fluid {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .welcome-card {
            background: white;
            border-radius: 16px;
            padding: 60px 50px;
            max-width: 420px;
            width: 100%;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo i {
            font-size: 2.2rem;
            color: white;
        }

        .title {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .btn-primary-custom {
            background: var(--primary);
            border: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 1rem;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            margin-bottom: 16px;
            width: 100%;
            justify-content: center;
        }

        .btn-primary-custom:hover {
            background: #3730a3;
            transform: translateY(-1px);
            color: white;
        }

        .btn-secondary-custom {
            background: transparent;
            border: 1px solid var(--border);
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 1rem;
            color: var(--text-primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            width: 100%;
            justify-content: center;
        }

        .btn-secondary-custom:hover {
            background: var(--accent);
            border-color: var(--secondary);
            color: var(--text-primary);
        }

        .features {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
        }

        .feature-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .feature-icon {
            width: 28px;
            height: 28px;
            background: var(--accent);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-card {
                padding: 50px 40px;
                margin: 0 15px;
            }

            .title {
                font-size: 1.75rem;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .welcome-card {
                padding: 40px 30px;
            }

            .title {
                font-size: 1.5rem;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .logo i {
                font-size: 2rem;
            }
        }

        /* Loading */
        .loading {
            position: fixed;
            inset: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeOut 0.3s ease-out 1s forwards;
        }

        .spinner {
            width: 32px;
            height: 32px;
            border: 2px solid var(--border);
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    <!-- Loading -->
    <div class="loading">
        <div class="spinner"></div>
    </div>

    <div class="container-fluid">
        <div class="welcome-card">
            <div class="logo">
                <i class="fas fa-user-tie"></i>
            </div>

            <h1 class="title">CV Purwa Putra</h1>
            <p class="subtitle">
                Sistem manajemen karyawan yang memudahkan pengelolaan absensi, cuti, dan payroll dengan efisien.
            </p>

            <div class="d-grid gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn-primary-custom">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary-custom">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Masuk ke Sistem
                        </a>
                    @endauth
                @endif
            </div>

            <div class="features">
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <span>Absensi</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <span>Cuti</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <span>Payroll</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <span>Laporan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple hover effects
            const buttons = document.querySelectorAll('.btn-primary-custom, .btn-secondary-custom');

            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Logo hover effect
            const logo = document.querySelector('.logo');
            logo.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });

            logo.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
