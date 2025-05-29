<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ config('app.company') }} </title>

    <!-- Scripts -->
    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



    <!-- Styles -->
    <style>
       /* Override Bootstrap 4 Primary Colors to Success Green */

/* Button Primary */
.btn-primary {
    background-color: #28a745 !important; /* Bootstrap success color */
    border-color: #28a745 !important;
    color: #fff !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary:active:focus,
.btn-primary:focus-visible {
    background-color: #218838 !important; /* Darker success for hover */
    border-color: #1e7e34 !important;
    color: #fff !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
}

.btn-primary:disabled,
.btn-primary.disabled {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    opacity: 0.65;
}

/* Button Outline Primary */
.btn-outline-primary {
    color: #28a745 !important;
    border-color: #28a745 !important;
}

.btn-outline-primary:hover,
.btn-outline-primary:focus,
.btn-outline-primary:active {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: #fff !important;
}

/* Primary Text Color */
.text-primary {
    color: #28a745 !important;
}

/* Primary Background */
.bg-primary {
    background-color: #28a745 !important;
}

/* Primary Border */
.border-primary {
    border-color: #28a745 !important;
}

/* Links */
a.text-primary:hover,
a.text-primary:focus {
    color: #218838 !important;
}

/* Form Controls with Primary Focus */
.form-control:focus {
    border-color: #80d4a0 !important; /* Light success */
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
}

/* Custom Controls */
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
}

/* Progress Bar Primary */
.progress-bar {
    background-color: #28a745 !important;
}

/* Nav Pills Primary */
.nav-pills .nav-link.active,
.nav-pills .show > .nav-link {
    background-color: #28a745 !important;
}

/* Badge Primary */
.badge-primary {
    background-color: #28a745 !important;
}

/* Alert Primary (jika ada) */
.alert-primary {
    color: #155724 !important;
    background-color: #d4edda !important;
    border-color: #c3e6cb !important;
}

/* Pagination Primary */
.page-item.active .page-link {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.page-link {
    color: #28a745 !important;
}

.page-link:hover {
    color: #218838 !important;
}
    </style>



</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/home">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">{{config('app.name')}}</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="/home">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                MENU
            </div>
            @php
            $role = auth()->user()->role;
        @endphp
            <!-- Nav Item - Pages Collapse Menu -->
            @if ($role === 'admin')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwotr"
                    aria-expanded="true" aria-controls="collapseTwotr">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Hak Akses</span>

                </a>
                <div id="collapseTwotr" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Hak Akses</h6>
                        <a class="collapse-item" href="/user">Data Karyawan</a>
                    </div>
                </div>
            </li>
            @endif

            {{-- @if ($role === 'user') --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Data Master</span>

                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item" href="/profile">Profile Karyawan</a>


                    </div>
                </div>
            </li>
            


            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Transaksi Gaji</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Data Gaji</h6>
                        <a class="collapse-item" href="/tunjangan">Data Tunjangan</a>
                        <a class="collapse-item" href="/potongan">Data Potongan</a>

                    </div>
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Transaksi</h6>
                        <a class="collapse-item" href="/gaji">Penggajian</a>

                    </div>
                </div>
            </li>
            {{-- @endif --}}
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseUtilitiesLaporan" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-comment-dollar"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseUtilitiesLaporan" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    {{-- <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Laporan Jurnal</h6>
                        <a class="collapse-item" href="/jurnal">Entri Jurnal</a>
                        <a class="collapse-item" href="/jurnal-laporan">Cetak Laporan Jurnal</a>

                    </div> --}}
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Laporan Penggajian</h6>
                        <a class="collapse-item" href="/gaji-laporan">Laporan Gaji</a>

                    </div>
                </div>
            </li>



            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <b>{{ config('app.company') }}</b>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block">


                        </div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->name }}</span>


                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Keluar
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <main class="py-4">
                    @yield('content')
                </main>


                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Informasi</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                        onclick="event.preventDefault();
                                  document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="/js/demo/chart-area-demo.js"></script>
    <script src="/js/demo/chart-pie-demo.js"></script>
    <script src="/js/success.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>



</body>

</html>
