<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Token Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        .main-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .qr-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .qr-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .qr-body {
            padding: 40px;
            text-align: center;
        }

        #qr-container {
            position: relative;
            display: inline-block;
        }

        #qr-container img {
            max-width: 400px;
            width: 100%;
            height: auto;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .token-info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #4CAF50;
        }

        .token-code {
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .status-badge {
            font-size: 1em;
            padding: 8px 16px;
            border-radius: 20px;
        }

        .fullscreen-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.2em;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .fullscreen-btn:hover {
            background: rgba(0,0,0,0.9);
            transform: scale(1.1);
        }

        .activity-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .activity-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .activity-body {
            padding: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        /* Fullscreen Modal Styles */
        .fullscreen-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .fullscreen-modal.active {
            display: flex;
        }

        .fullscreen-content {
            text-align: center;
            color: white;
            max-width: 90%;
            max-height: 90%;
        }

        .fullscreen-qr {
            max-width: 80vh;
            max-height: 80vh;
            width: auto;
            height: auto;
            border: 5px solid white;
            border-radius: 20px;
            background: white;
            padding: 20px;
        }

        .close-fullscreen {
            position: absolute;
            top: 20px;
            right: 30px;
            background: none;
            border: none;
            color: white;
            font-size: 3em;
            cursor: pointer;
            z-index: 10000;
        }

        .close-fullscreen:hover {
            color: #ff6b6b;
        }

        .fullscreen-info {
            margin-top: 20px;
            font-size: 1.5em;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }

            .qr-body {
                padding: 20px;
            }

            #qr-container img {
                max-width: 300px;
            }

            .fullscreen-qr {
                max-width: 90vw;
                max-height: 70vh;
            }
        }

        /* Animation untuk loading */
        .loading-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Progress bar styling */
        .progress {
            background-color: rgba(255,255,255,0.3);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease;
            border-radius: 10px;
        }

        /* Alert styling */
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 15px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="text-white mb-0">
                <i class="fas fa-qrcode me-2"></i>
                QR Token Absensi
            </h1>
            <p class="text-white-50">Scan QR code untuk melakukan absensi</p>
        </div>

        <!-- QR Code Section -->
        <div class="qr-card">
            <div class="qr-header">
                <h5 class="mb-1">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Code untuk Absensi
                </h5>
                <small>Status: <span id="token-status" class="status-badge bg-success">Aktif</span></small>
            </div>
            <div class="qr-body">
                <div id="qr-container">
                    <button class="fullscreen-btn" onclick="openFullscreen()" title="Fullscreen">
                        <i class="fas fa-expand"></i>
                    </button>
                    <img src="{{ $qrImage }}" alt="QR Code" id="qr-image">
                </div>

                <div class="token-info">
                    <div><strong>Token Aktif:</strong></div>
                    <div class="token-code" id="token-display">{{ $token }}</div>
                </div>

                <div id="used-info" style="display: none;" class="alert alert-info mt-3">
                    <h6><i class="fas fa-check-circle me-2"></i>Token Telah Digunakan!</h6>
                    <p><strong>Digunakan oleh:</strong> <span id="used-by"></span></p>
                    <p><strong>Waktu:</strong> <span id="used-at"></span></p>
                    <button class="btn btn-primary" onclick="generateNewToken()">
                        <i class="fas fa-refresh me-2"></i>Generate Token Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="activity-card">
            <div class="activity-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Log Aktivitas
                </h5>
            </div>
            <div class="activity-body">
                <div id="activity-log">
                    <div class="alert alert-info">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Menunggu scan QR...</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Modal -->
    <div id="fullscreen-modal" class="fullscreen-modal">
        <button class="close-fullscreen" onclick="closeFullscreen()">×</button>
        <div class="fullscreen-content">
            <img src="{{ $qrImage }}" alt="QR Code" class="fullscreen-qr" id="fullscreen-qr">
            <div class="fullscreen-info">
                <div><strong>Token:</strong> <span id="fullscreen-token">{{ $token }}</span></div>
                <div class="mt-2">
                    <span id="fullscreen-status" class="status-badge bg-success">Aktif</span>
                </div>
                <div class="mt-3">
                    <small class="text-white-50">Tekan ESC atau klik × untuk keluar dari fullscreen</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentToken = '{{ $token }}';
        let checkInterval;

        // Fullscreen functions
        function openFullscreen() {
            const modal = document.getElementById('fullscreen-modal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Save fullscreen state
            sessionStorage.setItem('isFullscreen', 'true');
        }

        function closeFullscreen() {
            const modal = document.getElementById('fullscreen-modal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';

            // Clear fullscreen state
            sessionStorage.removeItem('isFullscreen');
        }

        // Check and restore fullscreen state on page load
        function checkFullscreenState() {
            const isFullscreen = sessionStorage.getItem('isFullscreen');
            if (isFullscreen === 'true') {
                // Delay sedikit untuk memastikan DOM sudah ready
                setTimeout(() => {
                    const modal = document.getElementById('fullscreen-modal');
                    if (modal) {
                        modal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        console.log('Fullscreen state restored');
                    }
                }, 100);
            }
        }

        // Enhanced fullscreen toggle with better state management
        function toggleFullscreen() {
            const modal = document.getElementById('fullscreen-modal');
            const isCurrentlyFullscreen = modal.classList.contains('active');

            if (isCurrentlyFullscreen) {
                closeFullscreen();
            } else {
                openFullscreen();
            }
        }

        // Close fullscreen with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFullscreen();
            }
        });

        // Fungsi untuk check status token
        function checkTokenStatus() {
            console.log('Checking token status for:', currentToken);

            fetch('/attendance/check-token-status?token=' + currentToken, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Token status response:', data);

                const statusElement = document.getElementById('token-status');
                const fullscreenStatus = document.getElementById('fullscreen-status');
                const qrContainer = document.getElementById('qr-container');
                const usedInfo = document.getElementById('used-info');
                const activityLog = document.getElementById('activity-log');

                switch(data.status) {
                    case 'used':
                        statusElement.innerHTML = '<span class="status-badge bg-warning">Terpakai</span>';
                        fullscreenStatus.innerHTML = '<span class="status-badge bg-warning">Terpakai</span>';
                        qrContainer.style.display = 'none';
                        usedInfo.style.display = 'block';
                        document.getElementById('used-by').textContent = data.used_by || 'Unknown';
                        document.getElementById('used-at').textContent = data.used_at || 'Unknown';

                        activityLog.innerHTML = `
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Token berhasil digunakan!</strong><br>
                                <strong>User:</strong> ${data.used_by || 'Unknown'}<br>
                                <strong>Waktu:</strong> ${data.used_at || 'Unknown'}
                            </div>
                        `;

                        // Stop checking
                        clearInterval(checkInterval);

                        // Auto-generate new token after 3 seconds dengan feedback visual
                        let countdown = 3;
                        const countdownInterval = setInterval(() => {
                            activityLog.innerHTML = `
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Token berhasil digunakan!</strong><br>
                                    <strong>User:</strong> ${data.used_by || 'Unknown'}<br>
                                    <strong>Waktu:</strong> ${data.used_at || 'Unknown'}<br>
                                    <hr>
                                    <small><i class="fas fa-refresh fa-spin me-1"></i>Generating token baru dalam ${countdown} detik...</small>
                                </div>
                            `;
                            countdown--;
                            if (countdown < 0) {
                                clearInterval(countdownInterval);
                                generateNewToken();
                            }
                        }, 1000);
                        break;

                    case 'expired':
                        statusElement.innerHTML = '<span class="status-badge bg-danger">Kadaluwarsa</span>';
                        fullscreenStatus.innerHTML = '<span class="status-badge bg-danger">Kadaluwarsa</span>';
                        activityLog.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Token kadaluwarsa!</strong><br>
                                Silakan generate token baru.
                                <br><br>
                                <button class="btn btn-primary btn-sm" onclick="generateNewToken()">
                                    <i class="fas fa-refresh me-1"></i>Generate Token Baru
                                </button>
                            </div>
                        `;
                        clearInterval(checkInterval);
                        break;

                    case 'invalid':
                        statusElement.innerHTML = '<span class="status-badge bg-danger">Invalid</span>';
                        fullscreenStatus.innerHTML = '<span class="status-badge bg-danger">Invalid</span>';
                        activityLog.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Token tidak valid!</strong><br>
                                Silakan generate token baru.
                                <br><br>
                                <button class="btn btn-primary btn-sm" onclick="generateNewToken()">
                                    <i class="fas fa-refresh me-1"></i>Generate Token Baru
                                </button>
                            </div>
                        `;
                        clearInterval(checkInterval);
                        break;

                    case 'active':
                        statusElement.innerHTML = '<span class="status-badge bg-success">Aktif</span>';
                        fullscreenStatus.innerHTML = '<span class="status-badge bg-success">Aktif</span>';

                        // Pastikan QR container terlihat saat aktif
                        qrContainer.style.display = 'block';
                        usedInfo.style.display = 'none';

                        const now = new Date();
                        const timeString = now.toLocaleTimeString('id-ID');
                        activityLog.innerHTML = `
                            <div class="alert alert-info">
                                <i class="fas fa-hourglass-half me-2"></i>
                                <strong>Menunggu scan QR...</strong><br>
                                <small>Last check: ${timeString}</small>
                            </div>
                        `;
                        break;

                    default:
                        console.log('Unknown status:', data.status);
                }
            })
            .catch(error => {
                console.error('Error checking token status:', error);
                const activityLog = document.getElementById('activity-log');
                activityLog.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${error.message}<br>
                        <small>Check console for details</small>
                        <br><br>
                        <button class="btn btn-warning btn-sm" onclick="checkTokenStatus()">
                            <i class="fas fa-retry me-1"></i>Coba Lagi
                        </button>
                    </div>
                `;
            });
        }

        // Fungsi untuk generate token baru
        function generateNewToken() {
            // Stop current interval
            if (checkInterval) {
                clearInterval(checkInterval);
            }

            const activityLog = document.getElementById('activity-log');
            const qrContainer = document.getElementById('qr-container');
            const usedInfo = document.getElementById('used-info');
            const isFullscreen = sessionStorage.getItem('isFullscreen') === 'true';

            // Reset tampilan
            qrContainer.style.display = 'block';
            usedInfo.style.display = 'none';

            // Show loading
            activityLog.innerHTML = `
                <div class="alert alert-info loading-pulse">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    <strong>Generating new token...</strong>
                    <br><small>Mohon tunggu sebentar...</small>
                </div>
            `;

            // If in fullscreen mode, try to get new token via AJAX instead of reload
            if (isFullscreen) {
                generateTokenViaAjax();
            } else {
                // Simulate loading progress for normal mode
                let progress = 0;
                const loadingInterval = setInterval(() => {
                    progress += 20;
                    if (progress <= 100) {
                        activityLog.innerHTML = `
                            <div class="alert alert-info loading-pulse">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                <strong>Generating new token...</strong>
                                <br><small>Progress: ${progress}%</small>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" style="width: ${progress}%"></div>
                                </div>
                            </div>
                        `;
                    } else {
                        clearInterval(loadingInterval);
                        // Reload page after loading animation
                        window.location.reload();
                    }
                }, 200);
            }
        }

        // Generate token via AJAX (untuk mode fullscreen)
        function generateTokenViaAjax() {
            fetch('/attendance/generate-new-token', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    // If route doesn't exist (404) or other error, use reload method
                    if (response.status === 404) {
                        throw new Error('AJAX_NOT_AVAILABLE');
                    }
                    throw new Error('Failed to generate new token');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.token && data.qrImage) {
                    // Update current token
                    currentToken = data.token;

                    // Update QR images
                    document.getElementById('qr-image').src = data.qrImage;
                    document.getElementById('fullscreen-qr').src = data.qrImage;

                    // Update token displays
                    document.getElementById('token-display').textContent = data.token;
                    document.getElementById('fullscreen-token').textContent = data.token;

                    // Update status
                    document.getElementById('token-status').innerHTML = '<span class="status-badge bg-success">Aktif</span>';
                    document.getElementById('fullscreen-status').innerHTML = '<span class="status-badge bg-success">Aktif</span>';

                    // Update activity log
                    document.getElementById('activity-log').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Token baru berhasil dibuat!</strong><br>
                            <small>${new Date().toLocaleTimeString('id-ID')}</small>
                        </div>
                    `;

                    // Restart checking
                    restartTokenCheck();
                } else {
                    throw new Error(data.message || 'Invalid response from server');
                }
            })
            .catch(error => {
                console.error('Error generating new token:', error);

                if (error.message === 'AJAX_NOT_AVAILABLE') {
                    // Fallback: reload page but maintain fullscreen
                    document.getElementById('activity-log').innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-refresh fa-spin me-2"></i>
                            <strong>Memuat ulang dengan mode fullscreen...</strong>
                        </div>
                    `;

                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Show error and fallback to reload
                    document.getElementById('activity-log').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>AJAX gagal, akan reload page...</strong><br>
                            <small>${error.message}</small>
                        </div>
                    `;

                    // Fallback to page reload after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            });
        }

        // Fungsi untuk restart checking (jika diperlukan)
        function restartTokenCheck() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }

            // Start new interval
            checkInterval = setInterval(checkTokenStatus, 3000);

            // Do immediate check
            setTimeout(() => {
                checkTokenStatus();
            }, 500);
        }

        // Start checking token status
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, starting token check for:', currentToken);

            // Pastikan elemen-elemen ada
            const statusElement = document.getElementById('token-status');
            const qrContainer = document.getElementById('qr-container');
            const usedInfo = document.getElementById('used-info');

            if (!statusElement || !qrContainer || !usedInfo) {
                console.error('Required elements not found');
                return;
            }

            // Check and restore fullscreen state
            checkFullscreenState();

            // Reset tampilan ke kondisi awal
            qrContainer.style.display = 'block';
            usedInfo.style.display = 'none';
            statusElement.innerHTML = '<span class="status-badge bg-success">Aktif</span>';

            // Initial check setelah 1 detik
            setTimeout(() => {
                checkTokenStatus();
            }, 1000);

            // Start interval checking setiap 3 detik
            if (checkInterval) {
                clearInterval(checkInterval);
            }
            checkInterval = setInterval(checkTokenStatus, 3000);

            // Debug log
            console.log('Token checking started with interval ID:', checkInterval);
        });

        // Cleanup interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            console.log('Page unloading, clearing interval:', checkInterval);
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        });

        // Handle page visibility change (optional - untuk pause/resume checking)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                console.log('Page hidden, clearing interval');
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            } else {
                console.log('Page visible, restarting interval');
                if (!checkInterval) {
                    restartTokenCheck();
                }
            }
        });
    </script>
</body>
</html>
