{{-- resources/views/karyawan/presensi/scan.blade.php --}}
@extends('layouts.app')

@section('title', 'Scan QR Presensi')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-camera me-2"></i>
                            Scan QR Code Presensi
                        </h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- QR Scanner Container --}}
                        <div class="text-center mb-4">
                            <div id="qr-reader"
                                style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 10px; overflow: hidden;">
                                <!-- QR Scanner will be initialized here -->
                            </div>

                            {{-- Scanner Status --}}
                            <div id="scanner-status" class="mt-2">
                                <p class="text-muted mb-0">
                                    <i class="fas fa-camera me-2"></i>
                                    <span id="status-text">Klik tombol Start untuk memulai scan</span>
                                </p>
                            </div>

                            {{-- Scan Result --}}
                            <div id="scan-result" class="alert" style="display: none; margin-top: 15px;">
                                <i class="fas fa-qrcode me-2"></i>
                                <span id="result-text"></span>
                            </div>
                        </div>

                        {{-- Scanner Controls --}}
                        <div class="text-center mb-3">
                            <button type="button" id="start-scan" class="btn btn-success me-2">
                                <i class="fas fa-play me-2"></i>Start Scan
                            </button>
                            <button type="button" id="stop-scan" class="btn btn-danger me-2" style="display: none;">
                                <i class="fas fa-stop me-2"></i>Stop Scan
                            </button>
                            <button type="button" id="switch-camera" class="btn btn-outline-info btn-sm me-2"
                                style="display: none;">
                                <i class="fas fa-sync-alt me-2"></i>Switch Camera
                            </button>
                            <button type="button" id="test-qr" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-vial me-2"></i>Test QR
                            </button>
                        </div>

                        {{-- Camera Selection --}}
                        <div id="camera-selection" class="text-center mb-3" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <label for="camera-select" class="form-label">Pilih Kamera:</label>
                                    <select id="camera-select" class="form-select">
                                        <option value="">Memuat daftar kamera...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Manual Input Alternative --}}
                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                data-bs-target="#manualInput">
                                <i class="fas fa-keyboard me-2"></i>
                                Input Manual Token
                            </button>
                        </div>

                        <div class="collapse" id="manualInput">
                            <div class="card card-body bg-light">
                                <form action="{{ route('presensi.store') }}" method="POST" id="manual-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="token" class="form-label">Token QR Code</label>
                                        <input type="text" class="form-control" id="token" name="token"
                                            placeholder="Masukkan token dari QR Code">
                                    </div>
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Lokasi</label>
                                        <input type="text" class="form-control" id="location" name="location"
                                            placeholder="Lokasi akan terdeteksi otomatis" readonly>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>
                                        Submit Presensi
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Auto Submit Form (Hidden) --}}
                        <form action="{{ route('presensi.store') }}" method="POST" id="auto-form" style="display: none;">
                            @csrf
                            <input type="hidden" id="auto-token" name="token">
                            <input type="hidden" id="auto-location" name="location">
                            <input type="hidden" id="auto-time" name="jam_presensi"> {{-- Pastikan name sesuai di controller --}}
                        </form>


                        {{-- Current Status --}}
                        <div class="mt-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-user me-2"></i>{{ Auth::user()->name ?? 'User' }}</h6>
                                    <p class="mb-0"><i class="fas fa-calendar me-2"></i>{{ date('d F Y') }}</p>
                                    <p class="mb-0"><i class="fas fa-clock me-2"></i><span
                                            id="current-time">{{ date('H:i:s') }}</span></p>
                                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><span
                                            id="current-location">Mendeteksi lokasi...</span></p>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <div class="text-center mt-4">
                            <a href="{{ route('presensi.riwayat') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-2"></i>
                                Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include html5-qrcode library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        let html5QrCode = null;
        let currentLocation = 'Lokasi tidak terdeteksi';
        let isScanning = false;
        let cameras = [];
        let currentCameraId = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing HTML5 QR Code Scanner...');
            initializeScanner();
            updateTime();
            setInterval(updateTime, 1000);
            getCurrentLocation();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Start scan button
            document.getElementById('start-scan').addEventListener('click', startScanning);

            // Stop scan button
            document.getElementById('stop-scan').addEventListener('click', stopScanning);

            // Switch camera button
            document.getElementById('switch-camera').addEventListener('click', switchCamera);

            // Test QR button
            document.getElementById('test-qr').addEventListener('click', function() {
                const testToken = 'test-token-' + Date.now();
                console.log('Testing with token:', testToken);
                handleQRCodeSuccess(testToken);
            });

            // Camera selection change
            document.getElementById('camera-select').addEventListener('change', function() {
                if (this.value && isScanning) {
                    stopScanning();
                    setTimeout(() => {
                        currentCameraId = this.value;
                        startScanning();
                    }, 500);
                }
            });

            // Manual form submission
            document.getElementById('manual-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const token = document.getElementById('token').value.trim();

                if (!token) {
                    alert('Silakan masukkan token QR Code');
                    return;
                }

                if (token.length < 10) {
                    alert('Token QR Code terlalu pendek');
                    return;
                }

                // Update location before submit
                document.getElementById('location').value = currentLocation;
                this.submit();
            });
        }

        // Initialize QR Scanner
        function initializeScanner() {
            try {
                // Create Html5Qrcode instance
                html5QrCode = new Html5Qrcode("qr-reader");

                // Get available cameras
                Html5Qrcode.getCameras().then(devices => {
                    console.log('Available cameras:', devices);
                    cameras = devices;

                    if (devices && devices.length) {
                        populateCameraSelect(devices);

                        // Set default camera (prefer back camera)
                        const backCamera = devices.find(device =>
                            device.label.toLowerCase().includes('back') ||
                            device.label.toLowerCase().includes('rear') ||
                            device.label.toLowerCase().includes('environment')
                        );

                        currentCameraId = backCamera ? backCamera.id : devices[0].id;

                        updateStatus('Scanner siap. Klik Start untuk memulai.', 'success');
                    } else {
                        updateStatus('Tidak ada kamera yang tersedia', 'danger');
                    }
                }).catch(err => {
                    console.error('Error getting cameras:', err);
                    updateStatus('Error mengakses kamera: ' + err.message, 'danger');
                });

            } catch (error) {
                console.error('Error initializing scanner:', error);
                updateStatus('Error inisialisasi scanner: ' + error.message, 'danger');
            }
        }

        // Populate camera selection dropdown
        function populateCameraSelect(devices) {
            const select = document.getElementById('camera-select');
            select.innerHTML = '';

            devices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.id;
                option.textContent = device.label || `Camera ${index + 1}`;

                if (device.id === currentCameraId) {
                    option.selected = true;
                }

                select.appendChild(option);
            });

            if (devices.length > 1) {
                document.getElementById('camera-selection').style.display = 'block';
            }
        }

        // Start scanning
        function startScanning() {
            if (!html5QrCode || !currentCameraId) {
                updateStatus('Scanner belum siap atau kamera tidak tersedia', 'danger');
                return;
            }

            const config = {
                fps: 10, // Frame per second for scanning
                qrbox: { // QR code scanning box
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0,
                disableFlip: false,
                videoConstraints: {
                    facingMode: "environment" // Prefer back camera
                }
            };

            updateStatus('Memulai kamera...', 'info');

            html5QrCode.start(
                currentCameraId,
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                console.log('QR Code scanner started successfully');
                isScanning = true;
                updateStatus('Arahkan kamera ke QR Code', 'success');

                // Update UI
                document.getElementById('start-scan').style.display = 'none';
                document.getElementById('stop-scan').style.display = 'inline-block';
                document.getElementById('switch-camera').style.display = cameras.length > 1 ? 'inline-block' :
                    'none';

            }).catch(err => {
                console.error('Error starting scanner:', err);
                updateStatus('Gagal memulai scanner: ' + err, 'danger');
                isScanning = false;
            });
        }

        // Stop scanning
        function stopScanning() {
            if (!html5QrCode || !isScanning) {
                return;
            }

            html5QrCode.stop().then(() => {
                console.log('QR Code scanner stopped');
                isScanning = false;
                updateStatus('Scanner dihentikan', 'secondary');

                // Update UI
                document.getElementById('start-scan').style.display = 'inline-block';
                document.getElementById('stop-scan').style.display = 'none';
                document.getElementById('switch-camera').style.display = 'none';

            }).catch(err => {
                console.error('Error stopping scanner:', err);
                updateStatus('Error menghentikan scanner: ' + err, 'warning');
            });
        }

        // Switch to next available camera
        function switchCamera() {
            if (cameras.length <= 1) return;

            const currentIndex = cameras.findIndex(camera => camera.id === currentCameraId);
            const nextIndex = (currentIndex + 1) % cameras.length;
            const nextCamera = cameras[nextIndex];

            stopScanning();

            setTimeout(() => {
                currentCameraId = nextCamera.id;
                document.getElementById('camera-select').value = currentCameraId;
                startScanning();
            }, 500);
        }

        // Handle successful QR code scan
        function onScanSuccess(decodedText, decodedResult) {
            console.log('QR Code detected:', decodedText);

            // Stop scanning immediately to prevent multiple scans
            stopScanning();

            // Process the QR code
            handleQRCodeSuccess(decodedText);
        }

        // Handle scan failure (this is called continuously, so we don't show errors)
        function onScanFailure(error) {
            // This is called when no QR code is found in frame
            // We don't need to show these errors as they're normal
        }

        // Process successful QR code detection
        function handleQRCodeSuccess(qrData) {
            updateStatus('QR Code terdeteksi! Memproses...', 'success');

            // Show scan result
            showScanResult('QR Code berhasil dibaca: ' + qrData.substring(0, 50) + (qrData.length > 50 ? '...' : ''),
                'success');

            // Extract token from QR data
            let token = '';

            try {
                // Method 1: If QR contains URL with token parameter
                if (qrData.includes('token=')) {
                    try {
                        const url = new URL(qrData);
                        token = url.searchParams.get('token');
                    } catch (e) {
                        // If URL parsing fails, try regex
                        const match = qrData.match(/token=([^&\s]+)/);
                        if (match) token = match[1];
                    }
                }
                // Method 2: If QR contains route with token
                else if (qrData.includes('/presensi/scan')) {
                    const match = qrData.match(/token=([^&\s]+)/);
                    if (match) token = match[1];
                }
                // Method 3: If QR contains JSON
                else if (qrData.startsWith('{') && qrData.endsWith('}')) {
                    try {
                        const data = JSON.parse(qrData);
                        token = data.token || data.code || data.id;
                    } catch (e) {
                        console.log('Not valid JSON');
                    }
                }
                // Method 4: If QR contains just the token (assume it's valid if long enough)
                else if (qrData.length >= 10 && !qrData.includes(' ') && !qrData.includes('\n')) {
                    token = qrData.trim();
                }

                console.log('Extracted token:', token);

                if (token && token.length >= 10) {
                    submitPresensi(token, currentLocation);
                } else {
                    throw new Error('Token tidak valid atau tidak ditemukan dalam QR Code');
                }
            } catch (error) {
                console.error('Error processing QR code:', error);
                showScanResult('QR Code tidak valid: ' + error.message, 'danger');

                // Restart scanning after 3 seconds
                setTimeout(() => {
                    hideScanResult();
                    startScanning();
                }, 3000);
            }
        }

        function submitPresensi(token, location) {
            showScanResult('Menyimpan presensi...', 'info');
            const now = new Date();
            const jamPresensi = now.toLocaleTimeString('id-ID', {
                hour12: false
            });

            document.getElementById('auto-token').value = token;
            document.getElementById('auto-location').value = location;
            document.getElementById('auto-time').value = jamPresensi;
            setTimeout(() => {
                document.getElementById('auto-form').submit();
            }, 1000);
        }


        // Update status text
        function updateStatus(message, type = 'info') {
            const statusText = document.getElementById('status-text');
            statusText.textContent = message;

            // Remove existing classes
            statusText.className = '';

            // Add appropriate class based on type
            switch (type) {
                case 'success':
                    statusText.className = 'text-success';
                    break;
                case 'danger':
                    statusText.className = 'text-danger';
                    break;
                case 'warning':
                    statusText.className = 'text-warning';
                    break;
                case 'info':
                    statusText.className = 'text-info';
                    break;
                default:
                    statusText.className = 'text-muted';
            }
        }

        // Show scan result
        function showScanResult(message, type = 'info') {
            const result = document.getElementById('scan-result');
            const resultText = document.getElementById('result-text');

            resultText.textContent = message;
            result.className = `alert alert-${type}`;
            result.style.display = 'block';
        }

        // Hide scan result
        function hideScanResult() {
            document.getElementById('scan-result').style.display = 'none';
        }

        // Get current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        currentLocation = `${lat}, ${lng}`;

                        // Update location displays
                        document.getElementById('location').value = currentLocation;
                        document.getElementById('current-location').textContent =
                            `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                        console.log('Location detected:', currentLocation);
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        currentLocation = 'Lokasi tidak terdeteksi';
                        document.getElementById('location').value = currentLocation;
                        document.getElementById('current-location').textContent = currentLocation;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000
                    }
                );
            } else {
                currentLocation = 'Geolocation tidak didukung';
                document.getElementById('location').value = currentLocation;
                document.getElementById('current-location').textContent = currentLocation;
            }
        }

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toTimeString().split(' ')[0];
            document.getElementById('current-time').textContent = timeString;
        }

        // Cleanup when page unloads
        window.addEventListener('beforeunload', function() {
            if (html5QrCode && isScanning) {
                html5QrCode.stop();
            }
        });

        // Add this to your existing JavaScript in scan.blade.php

        // Check current attendance status when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing HTML5 QR Code Scanner...');
            initializeScanner();
            updateTime();
            setInterval(updateTime, 1000);
            getCurrentLocation();
            setupEventListeners();
            checkAttendanceStatus(); // Add this line
        });

        // Function to check current attendance status
        function checkAttendanceStatus() {
            fetch('/presensi/check-status')
                .then(response => response.json())
                .then(data => {
                    updateAttendanceStatusDisplay(data);
                })
                .catch(error => {
                    console.error('Error checking attendance status:', error);
                });
        }

        // Update the attendance status display
        function updateAttendanceStatusDisplay(status) {
            const statusCard = document.querySelector('.card.bg-info');
            let statusHtml = '';

            if (status.has_checked_in && status.has_checked_out) {
                // Both check-in and check-out completed
                statusHtml = `
            <div class="card-body text-center">
                <h6><i class="fas fa-user me-2"></i>${document.querySelector('.card.bg-info h6').textContent}</h6>
                <p class="mb-0"><i class="fas fa-calendar me-2"></i>${status.date}</p>
                <p class="mb-0"><i class="fas fa-clock me-2"></i><span id="current-time">${new Date().toLocaleTimeString()}</span></p>
                <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><span id="current-location">Mendeteksi lokasi...</span></p>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <small>Check-in</small><br>
                        <strong class="text-success">${status.check_in_time}</strong>
                    </div>
                    <div class="col-6">
                        <small>Check-out</small><br>
                        <strong class="text-danger">${status.check_out_time}</strong>
                    </div>
                </div>
                <div class="alert alert-success mt-2 mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Presensi hari ini sudah lengkap
                </div>
            </div>
        `;

                // Disable scanning since attendance is complete
                disableScanning('Presensi hari ini sudah lengkap');

            } else if (status.has_checked_in && !status.has_checked_out) {
                // Only check-in completed, waiting for check-out
                statusHtml = `
            <div class="card-body text-center">
                <h6><i class="fas fa-user me-2"></i>${document.querySelector('.card.bg-info h6').textContent}</h6>
                <p class="mb-0"><i class="fas fa-calendar me-2"></i>${status.date}</p>
                <p class="mb-0"><i class="fas fa-clock me-2"></i><span id="current-time">${new Date().toLocaleTimeString()}</span></p>
                <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><span id="current-location">Mendeteksi lokasi...</span></p>
                <hr>
                <div class="text-center">
                    <small>Check-in</small><br>
                    <strong class="text-success">${status.check_in_time}</strong>
                </div>
                <div class="alert alert-warning mt-2 mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Scan QR untuk Check-out
                </div>
            </div>
        `;

                updateStatus('Scan QR Code untuk check-out', 'warning');

            } else {
                // No attendance record yet, need to check-in
                statusHtml = `
            <div class="card-body text-center">
                <h6><i class="fas fa-user me-2"></i>${document.querySelector('.card.bg-info h6').textContent}</h6>
                <p class="mb-0"><i class="fas fa-calendar me-2"></i>${status.date}</p>
                <p class="mb-0"><i class="fas fa-clock me-2"></i><span id="current-time">${new Date().toLocaleTimeString()}</span></p>
                <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><span id="current-location">Mendeteksi lokasi...</span></p>
                <div class="alert alert-info mt-2 mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Scan QR untuk Check-in
                </div>
            </div>
        `;

                updateStatus('Scan QR Code untuk check-in', 'info');
            }

            statusCard.innerHTML = statusHtml;

            // Restart time updates
            updateTime();
        }

        // Function to disable scanning
        function disableScanning(message) {
            // Stop any active scanning
            if (isScanning) {
                stopScanning();
            }

            // Hide scan controls
            document.getElementById('start-scan').style.display = 'none';
            document.getElementById('stop-scan').style.display = 'none';
            document.getElementById('switch-camera').style.display = 'none';
            document.getElementById('test-qr').style.display = 'none';

            // Update status
            updateStatus(message, 'success');

            // Hide QR reader
            document.getElementById('qr-reader').style.display = 'none';
        }

        // Update the handleQRCodeSuccess function to refresh status
        function handleQRCodeSuccess(qrData) {
            updateStatus('QR Code terdeteksi! Memproses...', 'success');

            // Show scan result
            showScanResult('QR Code berhasil dibaca: ' + qrData.substring(0, 50) + (qrData.length > 50 ? '...' : ''),
                'success');

            // Extract token from QR data
            let token = '';

            try {
                // Method 1: If QR contains URL with token parameter
                if (qrData.includes('token=')) {
                    try {
                        const url = new URL(qrData);
                        token = url.searchParams.get('token');
                    } catch (e) {
                        // If URL parsing fails, try regex
                        const match = qrData.match(/token=([^&\s]+)/);
                        if (match) token = match[1];
                    }
                }
                // Method 2: If QR contains route with token
                else if (qrData.includes('/presensi/scan')) {
                    const match = qrData.match(/token=([^&\s]+)/);
                    if (match) token = match[1];
                }
                // Method 3: If QR contains JSON
                else if (qrData.startsWith('{') && qrData.endsWith('}')) {
                    try {
                        const data = JSON.parse(qrData);
                        token = data.token || data.code || data.id;
                    } catch (e) {
                        console.log('Not valid JSON');
                    }
                }
                // Method 4: If QR contains just the token
                else if (qrData.length >= 10 && !qrData.includes(' ') && !qrData.includes('\n')) {
                    token = qrData.trim();
                }

                console.log('Extracted token:', token);

                if (token && token.length >= 10) {
                    submitPresensi(token, currentLocation);
                } else {
                    throw new Error('Token tidak valid atau tidak ditemukan dalam QR Code');
                }
            } catch (error) {
                console.error('Error processing QR code:', error);
                showScanResult('QR Code tidak valid: ' + error.message, 'danger');

                // Restart scanning after 3 seconds
                setTimeout(() => {
                    hideScanResult();
                    startScanning();
                }, 3000);
            }
        }

        // Update submitPresensi to refresh status after submission
        function submitPresensi(token, location) {
            showScanResult('Menyimpan presensi...', 'info');
            
            document.getElementById('auto-token').value = token;
            document.getElementById('auto-location').value = location;

            // Add small delay to show the processing message
            setTimeout(() => {
                document.getElementById('auto-form').submit();
            }, 1000);
        }
    </script>

    <style>
        /* Custom styles for html5-qrcode */
        #qr-reader {
            border: 2px dashed #28a745;
            border-radius: 10px;
            overflow: hidden;
        }

        #qr-reader video {
            border-radius: 8px;
        }

        /* Hide the default file input button from html5-qrcode */
        #qr-reader__dashboard_section {
            display: none !important;
        }

        /* Style the scanner region */
        #qr-reader__scan_region {
            border-radius: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #qr-reader {
                max-width: 100%;
            }
        }
    </style>

@endsection
