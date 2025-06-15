@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Scan QR Code & Lokasi
                    </h4>
                </div>

                <div class="card-body p-4">
                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Progress Indicator -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Progress:</small>
                            <small class="text-muted" id="progress-text">0/2 langkah selesai</small>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div id="progress-bar" class="progress-bar bg-success rounded-pill" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Scanner Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light rounded-3">
                                <div class="card-body text-center p-4">
                                    <div id="scan-controls">
                                        <i class="fas fa-camera text-success fs-1 mb-3"></i>
                                        <h5 class="mb-3">QR Code Scanner</h5>
                                        <button type="button" id="startScanBtn" class="btn btn-success btn-lg rounded-pill px-4">
                                            <i class="fas fa-camera me-2"></i>
                                            Mulai Scan QR Code
                                        </button>
                                    </div>

                                    <!-- QR Scanner Container -->
                                    <div id="reader-container" class="mt-4" style="display: none;">
                                        <div class="border rounded-3 p-3 bg-white">
                                            <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                                            <button type="button" id="stopScanBtn" class="btn btn-outline-secondary btn-sm mt-3 rounded-pill">
                                                <i class="fas fa-stop me-1"></i>
                                                Stop Scanner
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Cards -->
                    <div class="row g-3 mb-4">
                        <!-- QR Code Status -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm text-white rounded-3">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-qrcode text-info" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-3">Status QR Code</h6>
                                    <p class="card-text" id="token-preview">
                                        <span class="text-muted">Menunggu scan...</span>
                                    </p>
                                    <div id="token-status" class="mt-3">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Belum discan</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Status -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 text-white">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-map-marker-alt text-warning" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-3">Status Lokasi</h6>
                                    <p class="card-text" id="location-preview">
                                        <span class="text-muted">Mendeteksi lokasi...</span>
                                    </p>
                                    <div id="location-status" class="mt-3 mb-3">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Mendeteksi...</span>
                                    </div>
                                    <button type="button" id="getLocationBtn" class="btn btn-outline-warning btn-sm rounded-pill">
                                        <i class="fas fa-location-arrow me-1"></i>
                                        Izinkan Lokasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Token Input -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-warning bg-opacity-10 rounded-3 text-white">
                                <div class="card-body p-4">
                                    <div class="text-center mb-4">
                                        <i class="fas fa-keyboard text-white" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2 mb-0">Input Token Manual</h5>
                                        <small class="text-white">Masukkan token jika tidak bisa scan QR Code</small>
                                    </div>

                                    <form method="POST" action="{{ route('attendance.scanSubmit') }}" class="needs-validation" novalidate>
                                        @csrf
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-8">
                                                {{-- <label for="manual-token" class="form-label fw-semibold">Token</label> --}}
                                                <input type="text" class="form-control form-control-lg rounded-pill"
                                                       id="manual-token" name="token"
                                                       placeholder="Masukkan token dari QR Code atau manual" required>
                                                <div class="invalid-feedback">Token tidak boleh kosong.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                                                    <i class="fas fa-paper-plane me-2"></i>
                                                    Kirim
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" id="manual-location" name="location">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auto Submit Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="POST" action="{{ route('attendance.scanSubmit') }}">
                                @csrf
                                <input type="hidden" id="token" name="token">
                                <input type="hidden" id="location" name="location">

                                <div class="d-grid">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg rounded-pill py-3" disabled>
                                        <i class="fas fa-paper-plane me-2"></i>
                                        <span id="submit-text">Menunggu Data...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="card border-0 bg-info bg-opacity-10 text-white rounded-3">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-info-circle text-info fs-4 me-3"></i>
                                <h6 class="mb-0 fw-semibold">Petunjuk Penggunaan</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <ol class="mb-0">
                                        <li class="mb-2">Klik tombol "Mulai Scan QR Code" untuk mengaktifkan kamera</li>
                                        <li class="mb-2">Izinkan akses kamera saat diminta browser</li>
                                        <li class="mb-2">Arahkan kamera ke QR Code yang ingin discan</li>
                                    </ol>
                                </div>
                                <div class="col-md-6">
                                    <ol start="4" class="mb-0">
                                        <li class="mb-2">Izinkan akses lokasi untuk mendeteksi posisi Anda</li>
                                        <li class="mb-2">Klik "Kirim Data" setelah kedua data berhasil didapat</li>
                                        <li class="mb-0">Atau gunakan input manual jika diperlukan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Load html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
    let tokenScanned = false;
    let locationFetched = false;
    let html5QrCode = null;
    let scannerStarted = false;

    // Fungsi untuk ekstrak token dari URL atau QR code
    function extractTokenFromQRCode(qrCodeMessage) {
        console.log('Original QR Code message:', qrCodeMessage);

        let token = qrCodeMessage;

        try {
            // Cek apakah QR code berisi URL
            if (qrCodeMessage.includes('http://') || qrCodeMessage.includes('https://')) {
                const url = new URL(qrCodeMessage);

                // Coba ekstrak token dari parameter URL
                const tokenFromParam = url.searchParams.get('token');
                if (tokenFromParam) {
                    token = tokenFromParam;
                    console.log('Token extracted from URL parameter:', token);
                } else {
                    // Jika tidak ada parameter token, coba ekstrak dari path
                    const pathSegments = url.pathname.split('/');
                    const lastSegment = pathSegments[pathSegments.length - 1];

                    // Cek apakah segment terakhir adalah token (biasanya string panjang)
                    if (lastSegment && lastSegment.length > 10) {
                        token = lastSegment;
                        console.log('Token extracted from URL path:', token);
                    } else {
                        // Coba cari di semua segments yang mungkin token
                        for (let i = pathSegments.length - 1; i >= 0; i--) {
                            const segment = pathSegments[i];
                            // Token biasanya string alfanumerik dengan panjang tertentu
                            if (segment && segment.length >= 10 && /^[a-zA-Z0-9_-]+$/.test(segment)) {
                                token = segment;
                                console.log('Token found in path segment:', token);
                                break;
                            }
                        }
                    }
                }
            } else {
                // Jika bukan URL, cek apakah ada format khusus token
                // Misalnya: "TOKEN:abc123" atau "token=abc123"
                if (qrCodeMessage.includes('TOKEN:')) {
                    token = qrCodeMessage.split('TOKEN:')[1].trim();
                } else if (qrCodeMessage.includes('token=')) {
                    token = qrCodeMessage.split('token=')[1].split('&')[0].trim();
                } else {
                    // Gunakan original message sebagai token
                    token = qrCodeMessage.trim();
                }
            }
        } catch (error) {
            console.error('Error parsing QR code:', error);
            // Fallback ke original message
            token = qrCodeMessage.trim();
        }

        console.log('Final extracted token:', token);
        return token;
    }

    function updateProgress() {
        const progress = (tokenScanned ? 50 : 0) + (locationFetched ? 50 : 0);
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const completed = (tokenScanned ? 1 : 0) + (locationFetched ? 1 : 0);

        if (progressBar && progressText) {
            progressBar.style.width = progress + '%';
            progressText.textContent = `${completed}/2 langkah selesai`;
        }

        updateSubmitButton();
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submit-text');

        if (!submitBtn || !submitText) return;

        if (tokenScanned && locationFetched) {
            submitBtn.disabled = false;
            submitBtn.className = 'btn btn-success btn-lg rounded-pill py-3';
            submitText.textContent = 'Kirim Data Absensi';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'btn btn-secondary btn-lg rounded-pill py-3';
            if (!tokenScanned && !locationFetched) {
                submitText.textContent = 'Menunggu QR Code & Lokasi...';
            } else if (!tokenScanned) {
                submitText.textContent = 'Menunggu QR Code...';
            } else if (!locationFetched) {
                submitText.textContent = 'Menunggu Lokasi...';
            }
        }
    }

    async function checkCameraPermission() {
        try {
            // Check if camera permission is already granted
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            stream.getTracks().forEach(track => track.stop()); // Stop the stream immediately
            return true;
        } catch (error) {
            console.log('Camera permission not granted:', error);
            return false;
        }
    }

    async function requestCameraPermission() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            });
            stream.getTracks().forEach(track => track.stop()); // Stop the stream
            return true;
        } catch (error) {
            console.error('Camera permission denied:', error);
            return false;
        }
    }

    async function startQRScanner() {
        if (scannerStarted) return;

        const readerContainer = document.getElementById('reader-container');
        const startBtn = document.getElementById('startScanBtn');

        if (!readerContainer || !startBtn) {
            console.error('Scanner elements not found');
            return;
        }

        // Check camera permission first
        const hasPermission = await checkCameraPermission();
        if (!hasPermission) {
            const granted = await requestCameraPermission();
            if (!granted) {
                showToast('Akses kamera diperlukan untuk scan QR Code. Silakan izinkan akses kamera dan coba lagi.', 'error');
                return;
            }
        }

        // Show scanner container
        readerContainer.style.display = 'block';
        startBtn.style.display = 'none';

        try {
            html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true,
                showZoomSliderIfSupported: true,
                defaultZoomValueIfSupported: 2
            };

            const cameraConfig = {
                facingMode: "environment"
            };

            await html5QrCode.start(
                cameraConfig,
                config,
                qrCodeMessage => {
                    console.log('QR Code scanned raw:', qrCodeMessage);

                    // Ekstrak token dari QR code message
                    const extractedToken = extractTokenFromQRCode(qrCodeMessage);

                    // Set token ke form utama
                    const tokenInput = document.getElementById("token");
                    if (tokenInput) {
                        tokenInput.value = extractedToken;
                    }

                    // Set token ke form manual juga
                    const manualTokenInput = document.getElementById("manual-token");
                    if (manualTokenInput) {
                        manualTokenInput.value = extractedToken;
                    }

                    // Update display
                    const tokenPreview = document.getElementById("token-preview");
                    const tokenStatus = document.getElementById("token-status");

                    if (tokenPreview) {
                        tokenPreview.innerHTML = `<strong class="text-success">${extractedToken}</strong>`;
                    }

                    if (tokenStatus) {
                        tokenStatus.innerHTML = '<span class="badge bg-success rounded-pill px-3 py-2">Berhasil discan</span>';
                    }

                    tokenScanned = true;
                    updateProgress();
                    stopQRScanner();

                    showToast(`QR Code berhasil discan! Token: ${extractedToken}`, 'success');
                },
                errorMessage => {
                    // Silent error handling for scanning failures
                    console.log('QR scan error:', errorMessage);
                }
            );

            scannerStarted = true;
            showToast('Kamera berhasil diaktifkan. Arahkan ke QR Code.', 'success');

        } catch (err) {
            console.error("Error starting QR scanner:", err);

            let errorMsg = 'Gagal mengakses kamera.';
            if (err.name === 'NotAllowedError') {
                errorMsg = 'Akses kamera ditolak. Silakan izinkan akses kamera di browser.';
            } else if (err.name === 'NotFoundError') {
                errorMsg = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
            } else if (err.name === 'NotSupportedError') {
                errorMsg = 'Browser tidak mendukung akses kamera.';
            }

            showToast(errorMsg, 'error');
            resetScanButton();
        }
    }

    function stopQRScanner() {
        if (html5QrCode && scannerStarted) {
            html5QrCode.stop().then(() => {
                console.log('QR scanner stopped successfully');
                resetScanButton();
            }).catch(err => {
                console.error("Error stopping scanner:", err);
                resetScanButton();
            });
        }
    }

    function resetScanButton() {
        const readerContainer = document.getElementById('reader-container');
        const startBtn = document.getElementById('startScanBtn');

        if (readerContainer) {
            readerContainer.style.display = 'none';
        }
        if (startBtn) {
            startBtn.style.display = 'block';
        }

        scannerStarted = false;
        html5QrCode = null;
    }

    function detectLocation() {
        const locationBtn = document.getElementById('getLocationBtn');
        const locationStatus = document.getElementById('location-status');
        const locationPreview = document.getElementById('location-preview');

        if (!locationBtn || !locationStatus || !locationPreview) {
            console.error('Location elements not found');
            return;
        }

        locationStatus.innerHTML = '<span class="badge bg-warning rounded-pill px-3 py-2">Meminta izin...</span>';
        locationBtn.disabled = true;

        if (!navigator.geolocation) {
            locationPreview.innerHTML = '<span class="text-danger">Browser tidak mendukung geolocation</span>';
            locationStatus.innerHTML = '<span class="badge bg-danger rounded-pill px-3 py-2">Tidak didukung</span>';
            showToast('Browser tidak mendukung deteksi lokasi', 'error');
            locationBtn.disabled = false;
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 15000, // Increase timeout
            maximumAge: 30000 // Allow cached location up to 30 seconds
        };

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);
                const location = lat + ',' + lng;

                // Set location ke form utama
                const locationInput = document.getElementById("location");
                if (locationInput) {
                    locationInput.value = location;
                }

                // Set location ke form manual juga
                const manualLocationInput = document.getElementById("manual-location");
                if (manualLocationInput) {
                    manualLocationInput.value = location;
                }

                locationPreview.innerHTML = `<strong class="text-success">${lat}, ${lng}</strong>`;
                locationStatus.innerHTML = '<span class="badge bg-success rounded-pill px-3 py-2">Lokasi didapat</span>';

                locationFetched = true;
                updateProgress();
                locationBtn.style.display = 'none';

                showToast('Lokasi berhasil dideteksi!', 'success');
            },
            function(error) {
                let errorMsg = 'Gagal mendapatkan lokasi';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di browser.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Timeout mendapatkan lokasi. Silakan coba lagi.';
                        break;
                }

                locationPreview.innerHTML = `<span class="text-danger">${errorMsg}</span>`;
                locationStatus.innerHTML = '<span class="badge bg-danger rounded-pill px-3 py-2">Gagal</span>';
                locationBtn.disabled = false;

                showToast(errorMsg, 'error');
            },
            options
        );
    }

    function showToast(message, type) {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        // Create new toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed custom-toast alert-dismissible fade show`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Check if HTTPS is being used
    function checkSecureContext() {
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            showToast('Untuk menggunakan kamera dan lokasi, aplikasi harus diakses melalui HTTPS.', 'error');
            return false;
        }
        return true;
    }

    // Handle manual token input
    function handleManualTokenInput() {
        const manualTokenInput = document.getElementById('manual-token');
        if (!manualTokenInput) return;

        manualTokenInput.addEventListener('input', function() {
            const tokenValue = this.value.trim();
            if (tokenValue.length > 0) {
                // Extract token if it's a URL
                const extractedToken = extractTokenFromQRCode(tokenValue);

                // Update main form token
                const mainTokenInput = document.getElementById('token');
                if (mainTokenInput) {
                    mainTokenInput.value = extractedToken;
                }

                // Update display if token is different from input
                if (extractedToken !== tokenValue) {
                    this.value = extractedToken;
                }

                // Update token status
                const tokenPreview = document.getElementById("token-preview");
                const tokenStatus = document.getElementById("token-status");

                if (tokenPreview) {
                    tokenPreview.innerHTML = `<strong class="text-primary">${extractedToken}</strong>`;
                }

                if (tokenStatus) {
                    tokenStatus.innerHTML = '<span class="badge bg-primary rounded-pill px-3 py-2">Input manual</span>';
                }

                tokenScanned = true;
                updateProgress();
            } else {
                // Reset if empty
                const mainTokenInput = document.getElementById('token');
                if (mainTokenInput) {
                    mainTokenInput.value = '';
                }

                const tokenPreview = document.getElementById("token-preview");
                const tokenStatus = document.getElementById("token-status");

                if (tokenPreview) {
                    tokenPreview.innerHTML = '<span class="text-muted">Menunggu scan...</span>';
                }

                if (tokenStatus) {
                    tokenStatus.innerHTML = '<span class="badge bg-secondary rounded-pill px-3 py-2">Belum discan</span>';
                }

                tokenScanned = false;
                updateProgress();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Check secure context
        if (!checkSecureContext()) {
            return;
        }

        // Event listeners
        const startScanBtn = document.getElementById('startScanBtn');
        const stopScanBtn = document.getElementById('stopScanBtn');
        const getLocationBtn = document.getElementById('getLocationBtn');

        if (startScanBtn) {
            startScanBtn.addEventListener('click', startQRScanner);
        }

        if (stopScanBtn) {
            stopScanBtn.addEventListener('click', stopQRScanner);
        }

        if (getLocationBtn) {
            getLocationBtn.addEventListener('click', detectLocation);
        }

        // Handle manual token input
        handleManualTokenInput();

        // Auto detect location on page load
        setTimeout(() => {
            detectLocation();
        }, 1000);

        // Initialize progress
        updateProgress();

        // Handle page visibility change to stop camera when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && scannerStarted) {
                stopQRScanner();
            }
        });

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (scannerStarted) {
            stopQRScanner();
        }
    });
</script>

<script>
    // Enhanced location detection function with multiple accurate providers
function detectLocation() {
    const locationBtn = document.getElementById('getLocationBtn');
    const locationStatus = document.getElementById('location-status');
    const locationPreview = document.getElementById('location-preview');

    if (!locationBtn || !locationStatus || !locationPreview) {
        console.error('Location elements not found');
        return;
    }

    locationStatus.innerHTML = '<span class="badge bg-warning rounded-pill px-3 py-2">Meminta izin...</span>';
    locationPreview.innerHTML = '<span class="text-info">Meminta izin lokasi...</span>';
    locationBtn.disabled = true;

    if (!navigator.geolocation) {
        locationPreview.innerHTML = '<span class="text-danger">Browser tidak mendukung geolocation</span>';
        locationStatus.innerHTML = '<span class="badge bg-danger rounded-pill px-3 py-2">Tidak didukung</span>';
        showToast('Browser tidak mendukung deteksi lokasi', 'error');
        locationBtn.disabled = false;
        return;
    }

    const options = {
        enableHighAccuracy: true,
        timeout: 25000, // Increased timeout for better accuracy
        maximumAge: 10000 // Shorter cache time for more current location
    };

    navigator.geolocation.getCurrentPosition(
        async function(position) {
            const lat = position.coords.latitude.toFixed(7); // Higher precision
            const lng = position.coords.longitude.toFixed(7);
            const location = lat + ',' + lng;
            const accuracy = position.coords.accuracy ? Math.round(position.coords.accuracy) : 'Unknown';

            // Update status to show coordinates are obtained
            locationStatus.innerHTML = '<span class="badge bg-info rounded-pill px-3 py-2">Mendapatkan alamat...</span>';
            locationPreview.innerHTML = `
                <div class="text-info">
                    <strong>Koordinat:</strong> ${lat}, ${lng}<br>
                    <small>Akurasi: ±${accuracy}m | Sedang mendapatkan alamat...</small>
                </div>
            `;

            try {
                // Get address using multiple providers with better accuracy
                const address = await reverseGeocode(lat, lng);

                // Create location data with coordinates and address
                const locationData = {
                    coordinates: location,
                    address: address,
                    accuracy: accuracy,
                    full: `${location}|${address}|${accuracy}m` // Format: "lat,lng|address|accuracy"
                };

                // Set location ke form utama
                const locationInput = document.getElementById("location");
                if (locationInput) {
                    locationInput.value = locationData.full;
                }

                // Set location ke form manual juga
                const manualLocationInput = document.getElementById("manual-location");
                if (manualLocationInput) {
                    manualLocationInput.value = locationData.full;
                }

                // Update preview with both coordinates and address
                locationPreview.innerHTML = `
                    <div class="text-success">
                        <strong>Koordinat:</strong> ${lat}, ${lng}<br>
                        <strong>Alamat:</strong> ${address}<br>
                        <small class="text-muted">Akurasi: ±${accuracy}m</small>
                    </div>
                `;
                locationStatus.innerHTML = '<span class="badge bg-success rounded-pill px-3 py-2">Lokasi & alamat didapat</span>';

                locationFetched = true;
                updateProgress();
                locationBtn.style.display = 'none';

                showToast('Lokasi dan alamat berhasil dideteksi!', 'success');

            } catch (error) {
                console.error('Error getting address:', error);

                // Fallback: still use coordinates even if address fails
                const locationInput = document.getElementById("location");
                if (locationInput) {
                    locationInput.value = `${location}||${accuracy}m`;
                }

                const manualLocationInput = document.getElementById("manual-location");
                if (manualLocationInput) {
                    manualLocationInput.value = `${location}||${accuracy}m`;
                }

                locationPreview.innerHTML = `
                    <div class="text-warning">
                        <strong>Koordinat:</strong> ${lat}, ${lng}<br>
                        <small class="text-muted">Akurasi: ±${accuracy}m | Alamat tidak dapat dideteksi</small>
                    </div>
                `;
                locationStatus.innerHTML = '<span class="badge bg-warning rounded-pill px-3 py-2">Koordinat saja</span>';

                locationFetched = true;
                updateProgress();
                locationBtn.style.display = 'none';

                showToast('Koordinat berhasil dideteksi, tetapi alamat tidak dapat ditemukan.', 'warning');
            }
        },
        function(error) {
            let errorMsg = 'Gagal mendapatkan lokasi';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di browser.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg = 'Lokasi tidak tersedia. Pastikan GPS aktif dan Anda berada di area dengan sinyal yang baik.';
                    break;
                case error.TIMEOUT:
                    errorMsg = 'Timeout mendapatkan lokasi. Silakan coba lagi atau pindah ke area dengan sinyal GPS yang lebih baik.';
                    break;
            }

            locationPreview.innerHTML = `<span class="text-danger">${errorMsg}</span>`;
            locationStatus.innerHTML = '<span class="badge bg-danger rounded-pill px-3 py-2">Gagal</span>';
            locationBtn.disabled = false;

            showToast(errorMsg, 'error');
        },
        options
    );
}

// Enhanced reverse geocoding function with better Indonesian coverage
async function reverseGeocode(lat, lng) {
    const providers = [
        {
            name: 'OpenCage',
            url: `https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=YOUR_OPENCAGE_KEY&language=id&no_annotations=1`,
            parser: parseOpenCageResponse,
            requiresKey: true
        },
        {
            name: 'Nominatim-ID',
            url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=id,en&namedetails=1`,
            parser: parseNominatimResponse,
            requiresKey: false
        },
        {
            name: 'Photon',
            url: `https://photon.komoot.io/reverse?lat=${lat}&lon=${lng}&lang=id`,
            parser: parsePhotonResponse,
            requiresKey: false
        },
        {
            name: 'BigDataCloud',
            url: `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=id`,
            parser: parseBigDataCloudResponse,
            requiresKey: false
        }
    ];

    // Filter out providers that require API keys if not available
    const availableProviders = providers.filter(provider =>
        !provider.requiresKey || (provider.requiresKey && provider.url.includes('YOUR_') === false)
    );

    for (const provider of availableProviders) {
        try {
            console.log(`Trying ${provider.name} for reverse geocoding...`);

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout

            const response = await fetch(provider.url, {
                method: 'GET',
                headers: {
                    'User-Agent': 'AttendanceApp/1.0 (Educational Purpose)',
                    'Accept': 'application/json',
                    'Accept-Language': 'id,en'
                },
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const address = provider.parser(data);

            if (address && address.length > 15 && !address.toLowerCase().includes('unknown')) {
                console.log(`Successfully got address from ${provider.name}:`, address);
                return address;
            } else {
                console.log(`${provider.name} returned insufficient address data:`, address);
            }
        } catch (error) {
            console.error(`Error with ${provider.name}:`, error.message);
            continue; // Try next provider
        }
    }

    // If all providers fail, return fallback
    throw new Error('Semua layanan alamat tidak dapat diakses atau tidak memiliki data untuk lokasi ini');
}

// Parser for OpenCage response (most accurate for Indonesia)
function parseOpenCageResponse(data) {
    if (!data || !data.results || data.results.length === 0) {
        throw new Error('Invalid OpenCage response');
    }

    const result = data.results[0];
    if (result.formatted) {
        return result.formatted;
    }

    // Build from components if formatted not available
    const comp = result.components;
    const parts = [];

    if (comp.house_number) parts.push(comp.house_number);
    if (comp.road) parts.push(comp.road);
    if (comp.neighbourhood) parts.push(comp.neighbourhood);
    if (comp.suburb) parts.push(comp.suburb);
    if (comp.village) parts.push(comp.village);
    if (comp.town) parts.push(comp.town);
    if (comp.city) parts.push(comp.city);
    if (comp.county) parts.push(comp.county);
    if (comp.state) parts.push(comp.state);
    if (comp.country) parts.push(comp.country);

    return parts.length > 0 ? parts.join(', ') : result.formatted || 'Alamat tidak diketahui';
}

// Enhanced parser for Nominatim response
function parseNominatimResponse(data) {
    if (!data || (!data.address && !data.display_name)) {
        throw new Error('Invalid Nominatim response');
    }

    // Use display_name as primary source for better accuracy
    if (data.display_name) {
        // Clean up display_name for Indonesian addresses
        let address = data.display_name;

        // Remove postal codes that might be incorrect
        address = address.replace(/\b\d{5}\b/g, '').replace(/,\s*,/g, ',').trim();

        // If it's too generic or contains coordinates, try building from components
        if (address.length < 20 || address.includes('°')) {
            return buildAddressFromComponents(data.address);
        }

        return address;
    }

    return buildAddressFromComponents(data.address);
}

function buildAddressFromComponents(addr) {
    if (!addr) {
        throw new Error('No address components available');
    }

    const parts = [];

    // Indonesian address hierarchy
    if (addr.house_number) parts.push(addr.house_number);
    if (addr.road) parts.push(addr.road);
    if (addr.hamlet) parts.push(addr.hamlet);
    if (addr.village) parts.push(addr.village);
    if (addr.neighbourhood) parts.push(addr.neighbourhood);
    if (addr.suburb) parts.push(addr.suburb);
    if (addr.city_district) parts.push(addr.city_district);
    if (addr.town) parts.push(addr.town);
    if (addr.city) parts.push(addr.city);
    if (addr.county) parts.push(addr.county);
    if (addr.state) parts.push(addr.state);
    if (addr.country) parts.push(addr.country);

    return parts.length > 0 ? parts.join(', ') : 'Alamat tidak lengkap';
}

// Parser for Photon response
function parsePhotonResponse(data) {
    if (!data || !data.features || data.features.length === 0) {
        throw new Error('Invalid Photon response');
    }

    const feature = data.features[0];
    const props = feature.properties;

    if (!props) {
        throw new Error('No properties in Photon response');
    }

    const parts = [];

    if (props.housenumber) parts.push(props.housenumber);
    if (props.street) parts.push(props.street);
    if (props.district) parts.push(props.district);
    if (props.city) parts.push(props.city);
    if (props.county) parts.push(props.county);
    if (props.state) parts.push(props.state);
    if (props.country) parts.push(props.country);

    return parts.length > 0 ? parts.join(', ') : props.name || 'Alamat tidak diketahui';
}

// Enhanced parser for BigDataCloud response
function parseBigDataCloudResponse(data) {
    if (!data) {
        throw new Error('Invalid BigDataCloud response');
    }

    // Try to get the most complete address
    if (data.locality) {
        const parts = [data.locality];

        if (data.localityInfo && data.localityInfo.administrative) {
            // Sort by admin level descending to get more specific first
            const adminAreas = data.localityInfo.administrative
                .filter(admin => admin.name && admin.adminLevel >= 4)
                .sort((a, b) => b.adminLevel - a.adminLevel);

            adminAreas.forEach(admin => {
                if (!parts.includes(admin.name)) {
                    parts.push(admin.name);
                }
            });
        }

        if (data.principalSubdivision && !parts.includes(data.principalSubdivision)) {
            parts.push(data.principalSubdivision);
        }
        if (data.countryName && !parts.includes(data.countryName)) {
            parts.push(data.countryName);
        }

        return parts.join(', ');
    }

    if (data.city) {
        return `${data.city}, ${data.principalSubdivision || ''}, ${data.countryName || ''}`.replace(/,\s*,/g, ',').trim();
    }

    throw new Error('No useful address data from BigDataCloud');
}

// Enhanced manual location input handler
function handleManualLocationInput() {
    const manualLocationInput = document.getElementById('manual-location');
    if (!manualLocationInput) return;

    // Add input event listener for manual location
    manualLocationInput.addEventListener('input', function() {
        const locationValue = this.value.trim();
        const mainLocationInput = document.getElementById('location');

        if (mainLocationInput) {
            mainLocationInput.value = locationValue;
        }

        // Update location status based on input
        const locationPreview = document.getElementById("location-preview");
        const locationStatus = document.getElementById("location-status");

        if (locationValue.length > 0) {
            if (locationPreview) {
                locationPreview.innerHTML = `<div class="text-primary"><strong>Manual Input:</strong><br>${locationValue}</div>`;
            }
            if (locationStatus) {
                locationStatus.innerHTML = '<span class="badge bg-primary rounded-pill px-3 py-2">Input manual</span>';
            }
            locationFetched = true;
        } else {
            if (locationPreview) {
                locationPreview.innerHTML = '<span class="text-muted">Mendeteksi lokasi...</span>';
            }
            if (locationStatus) {
                locationStatus.innerHTML = '<span class="badge bg-secondary rounded-pill px-3 py-2">Mendeteksi...</span>';
            }
            locationFetched = false;
        }

        updateProgress();
    });
}

// Enhanced showToast function with warning type
function showToast(message, type) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());

    // Create new toast
    let alertClass = 'alert-info';
    let iconClass = 'fa-info-circle';

    switch(type) {
        case 'success':
            alertClass = 'alert-success';
            iconClass = 'fa-check-circle';
            break;
        case 'error':
            alertClass = 'alert-danger';
            iconClass = 'fa-exclamation-circle';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            iconClass = 'fa-exclamation-triangle';
            break;
    }

    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} position-fixed custom-toast alert-dismissible fade show`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
    toast.innerHTML = `
        <i class="fas ${iconClass} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 6 seconds (increased for longer messages)
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 6000);
}

// Helper function to validate coordinates
function isValidCoordinate(lat, lng) {
    const latitude = parseFloat(lat);
    const longitude = parseFloat(lng);

    return !isNaN(latitude) && !isNaN(longitude) &&
           latitude >= -90 && latitude <= 90 &&
           longitude >= -180 && longitude <= 180;
}
</script>

@endsection
