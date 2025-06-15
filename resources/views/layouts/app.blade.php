<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ config('app.company') }}</title>

    <!-- Fonts & Styles -->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        /* Override Bootstrap Primary Colors to Success Green */
        .btn-primary {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
            color: #fff !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
        }

        .btn-outline-primary {
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .btn-outline-primary:hover {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
        }

        .text-primary { color: #28a745 !important; }
        .bg-primary { background-color: #28a745 !important; }
        .border-primary { border-color: #28a745 !important; }

        .form-control:focus {
            border-color: #80d4a0 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }

        .page-item.active .page-link {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .page-link { color: #28a745 !important; }
        .page-link:hover { color: #218838 !important; }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            /* Default state: sidebar hidden on mobile */
            #wrapper #accordionSidebar {
                margin-left: -14rem;
                transition: margin-left 0.3s ease-in-out;
            }

            /* When toggled: sidebar visible */
            #wrapper.toggled #accordionSidebar {
                margin-left: 0;
            }

            /* Content wrapper always full width on mobile */
            #wrapper #content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            /* Sidebar positioning for mobile */
            #accordionSidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
            }

            /* Make sure topbar toggle button is visible */
            #sidebarToggleTop {
                display: block !important;
            }

            /* Adjust topbar for mobile */
            .topbar {
                padding: 0.5rem 1rem !important;
            }

            /* Responsive user dropdown */
            .navbar-nav .nav-item .nav-link {
                padding: 0.5rem !important;
            }

            /* Adjust main content padding for mobile */
            main.py-4 {
                padding: 1rem !important;
            }
        }

        /* Responsive Table Styles */
        .table-responsive-mobile {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .table-responsive-mobile table {
            min-width: 100%;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-responsive-mobile {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }

            .table-responsive-mobile table {
                margin-bottom: 0;
                font-size: 0.875rem;
            }

            .table-responsive-mobile th,
            .table-responsive-mobile td {
                padding: 0.5rem !important;
                white-space: nowrap;
            }

            /* Add scrollbar styling for mobile */
            .table-responsive-mobile::-webkit-scrollbar {
                height: 8px;
            }

            .table-responsive-mobile::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .table-responsive-mobile::-webkit-scrollbar-thumb {
                background: #28a745;
                border-radius: 4px;
            }

            .table-responsive-mobile::-webkit-scrollbar-thumb:hover {
                background: #218838;
            }
        }

        /* Card responsive adjustments */
        @media (max-width: 576px) {
            .card {
                margin-bottom: 1rem;
            }

            .card-header {
                padding: 0.75rem 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            /* Responsive buttons */
            .btn {
                margin-bottom: 0.5rem;
            }

            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/home">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">{{ config('app.name') }}</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="/home">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">MENU</div>

            @php
                $role = auth()->user()->role;
            @endphp

            <!-- Data Master -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataMaster"
                    aria-expanded="true" aria-controls="collapseDataMaster">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Data Master</span>
                </a>
                <div id="collapseDataMaster" class="collapse" aria-labelledby="headingDataMaster" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if ($role === 'admin')
                            <h6 class="collapse-header">Manajemen Data</h6>
                            <a class="collapse-item" href="/user">Data Karyawan</a>
                            <a class="collapse-item" href="/allowances">Data Tunjangan</a>
                            <a class="collapse-item" href="/deductions">Data Potongan</a>
                        @endif
                        <h6 class="collapse-header">Profile</h6>
                        <a class="collapse-item" href="/profile">
                            {{ $role === 'admin' ? 'Profile Admin' : 'Profile Karyawan' }}
                        </a>
                    </div>
                </div>
            </li>

            <!-- Presensi & Absensi -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePresensi"
                    aria-expanded="true" aria-controls="collapsePresensi">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Presensi</span>
                </a>
                <div id="collapsePresensi" class="collapse" aria-labelledby="headingPresensi" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if ($role === 'admin')
                            <h6 class="collapse-header">Kelola Presensi</h6>
                            <a class="collapse-item" href="/admin/attendance">Data Presensi</a>
                            <a class="collapse-item" href="/admin/attendance/settings">Pengaturan Presensi</a>
                        @else
                            <h6 class="collapse-header">Absensi Saya</h6>
                            <a class="collapse-item" href="/karyawan/attendance">Data Absensi</a>
                        @endif
                    </div>
                </div>
            </li>

            <!-- Penggajian -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGaji"
                    aria-expanded="true" aria-controls="collapseGaji">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Penggajian</span>
                </a>
                <div id="collapseGaji" class="collapse" aria-labelledby="headingGaji" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Transaksi Gaji</h6>
                        <a class="collapse-item" href="/salaries">
                            {{ $role === 'admin' ? 'Kelola Penggajian' : 'Slip Gaji' }}
                        </a>
                    </div>
                </div>
            </li>

            <!-- Cuti -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCuti"
                    aria-expanded="true" aria-controls="collapseCuti">
                    <i class="fas fa-fw fa-plane"></i>
                    <span>Cuti</span>
                </a>
                <div id="collapseCuti" class="collapse" aria-labelledby="headingCuti" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if ($role === 'admin')
                            <h6 class="collapse-header">Kelola Cuti</h6>
                            <a class="collapse-item" href="/leaves">Data Cuti</a>
                        @else
                            <h6 class="collapse-header">Pengajuan Cuti</h6>
                            <a class="collapse-item" href="/leaves">Pengajuan Cuti</a>
                        @endif
                    </div>
                </div>
            </li>

            <!-- Laporan -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                    aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Cetak Laporan</h6>
                        <a class="collapse-item" href="/attendance/export-filter">Laporan Absensi</a>
                            {{-- <a class="collapse-item" href="/salaries/export-filter">Laporan Gaji</a> --}}
                            <a class="collapse-item" href="/leaves/export-filter">Laporan Cuti</a>

                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <b>{{ config('app.company') }}</b>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        @php
                            $user = Auth::user();
                        @endphp

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle"
                                     src="{{ Storage::url($user->photo) }}"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <span class="ml-2 d-none d-lg-inline text-gray-600 small">
                                    {{ $user->name }}
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Keluar
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Main Content -->
                <main class="py-4">
                    @yield('content')
                </main>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Informasi {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" untuk keluar dari akun ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Kembali</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="/js/sb-admin-2.min.js"></script>
    <script src="/vendor/chart.js/Chart.min.js"></script>
    <script src="/js/demo/chart-area-demo.js"></script>
    <script src="/js/demo/chart-pie-demo.js"></script>
    <script src="/js/success.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            // Mobile responsive behavior
            function handleResponsive() {
                if ($(window).width() <= 768) {
                    // Start with sidebar hidden on mobile
                    $('body').removeClass('sidebar-toggled');
                    $('#accordionSidebar').removeClass('toggled');
                    $('#wrapper').removeClass('toggled');

                    // Toggle sidebar functionality for mobile
                    $('#sidebarToggleTop').off('click').on('click', function(e) {
                        e.preventDefault();
                        $('#wrapper').toggleClass('toggled');
                        $('#sidebarOverlay').toggleClass('show');
                    });

                    // Close sidebar when clicking overlay
                    $('#sidebarOverlay').off('click').on('click', function() {
                        $('#wrapper').removeClass('toggled');
                        $(this).removeClass('show');
                    });

                    // Optional: Close sidebar when clicking menu items (uncomment if needed)
                    /*
                    $('.collapse-item').off('click.mobile').on('click.mobile', function() {
                        $('#wrapper').removeClass('toggled');
                        $('#sidebarOverlay').removeClass('show');
                    });
                    */
                } else {
                    // Desktop behavior - remove mobile event handlers
                    $('#sidebarToggleTop').off('click');
                    $('#sidebarOverlay').off('click');
                    $('.collapse-item').off('click.mobile');
                    $('#sidebarOverlay').removeClass('show');
                }
            }

            // Initialize responsive behavior
            handleResponsive();

            // Handle window resize
            $(window).resize(function() {
                handleResponsive();
            });

            // Initialize responsive tables
            function initResponsiveTables() {
                // Wrap existing tables with responsive div
                $('#dataTable, .table').each(function() {
                    if (!$(this).parent().hasClass('table-responsive-mobile')) {
                        $(this).wrap('<div class="table-responsive-mobile"></div>');
                    }
                });
            }

            // Initialize responsive tables
            initResponsiveTables();

            // Re-initialize after DataTables initialization
            setTimeout(function() {
                initResponsiveTables();
            }, 1000);
        });
    </script>
</body>

</html>
