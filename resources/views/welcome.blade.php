<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Penggajian</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #09ff00 0%, #31aa3e 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-overlay: rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(10px) rotate(-1deg); }
        }

        .welcome-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .welcome-card {
            max-width: 480px;
            width: 100%;
            padding: 40px 35px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transform: translateY(0);
            transition: all 0.3s ease;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 12px 40px rgba(0, 0, 0, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .welcome-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: var(--success-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
            animation: pulse 2s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        .welcome-logo i {
            font-size: 3.5rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
            text-align: center;
            background: linear-gradient(135deg, #66ea92, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: #718096;
            text-align: center;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .welcome-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-custom {
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-dashboard {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
            color: white;
        }

        .btn-login {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(79, 172, 254, 0.5);
            color: white;
        }

        /* Features section */
        .features-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #4a5568;
        }

        .feature-item i {
            color: #66ea6f;
            margin-right: 12px;
            font-size: 1.2rem;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .welcome-card {
                padding: 30px 25px;
                margin: 10px;
            }

            .welcome-title {
                font-size: 1.7rem;
            }

            .welcome-logo {
                width: 100px;
                height: 100px;
            }

            .welcome-logo i {
                font-size: 3rem;
            }
        }

        /* Loading animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeOut 0.5s ease-out 1.5s forwards;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
    <!-- Loading Screen -->
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="welcome-container">
        <div class="welcome-card">
            <div class="welcome-logo">
                <i class="fas fa-user-tie"></i>
            </div>

            <h1 class="welcome-title">CV Purwa Putera</h1>
            <p class="welcome-subtitle">
                Sistem ini dirancang untuk memudahkan manajemen absensi, cuti, gaji, potongan, dan tunjangan karyawan secara efisien dan akurat.
                <br>
                Silakan masuk untuk mengakses fitur-fitur yang tersedia.
            </p>

            <div class="welcome-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-custom btn-dashboard">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-custom btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Masuk ke Sistem
                        </a>
                    @endauth
                @endif
            </div>


        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax effect on mouse move
            document.addEventListener('mousemove', function(e) {
                const card = document.querySelector('.welcome-card');
                const { clientX, clientY } = e;
                const { innerWidth, innerHeight } = window;

                const x = (clientX / innerWidth - 0.5) * 10;
                const y = (clientY / innerHeight - 0.5) * 10;

                card.style.transform = `translateY(-5px) rotateX(${y}deg) rotateY(${x}deg)`;
            });

            // Reset transform on mouse leave
            document.addEventListener('mouseleave', function() {
                const card = document.querySelector('.welcome-card');
                card.style.transform = 'translateY(-5px) rotateX(0) rotateY(0)';
            });

            // Button click effect
            document.querySelectorAll('.btn-custom').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });

        // Add ripple animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
