<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Truck Truck | Dashboard</title>
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- Summernote -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- Bootstrap 4.3.1 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Leaflet Routing Machine -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            height: 100vh;
            overflow-x: hidden;
            font-family: 'Source Sans Pro', sans-serif;
            position: relative;
        }

        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('images/background.jpg') }}') no-repeat center center;
            background-size: cover;
            filter: blur(8px);
            z-index: -1;
        }

        .navbar {
            background-color: #6DC5D1;

        }

        .gps-section {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin-bottom: 20px;
            text-align: left;
            /* Menambahkan ini agar konten di dalamnya di tengah */
        }

        .fuel-container {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin-bottom: 20px;
            text-align: center;
            /* Menambahkan ini agar konten di dalamnya di tengah */
        }

        #map {
            width: 100%;
            max-height: 600px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        #gps-data,
        .fuel-content {
            font-size: 16px;
            color: #333;
        }

        .gauge-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .scroll-controls {
            text-align: left;
            margin-bottom: 10px;
        }

        .scroll-controls button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 7px 15px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px;
        }

        .scroll-controls button:hover {
            background-color: #0056b3;
        }

        .dashboard-container {
            margin: 20px;
        }

        .scroll-container {
            height: 80vh;
            padding: 50px;
        }

        .page-container {
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow: visible;
        }

        #map {
            margin-left: 10px;
        }

        .form-border {
            position: relative;
            border: 2px solid #007bff;
            border-radius: 4px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            overflow: hidden;
        }

        .main-sidebar {
            width: 250px;
            transition: width 0.3s ease;
            overflow: hidden;
        }

        .main-sidebar.hidden {
            width: 0;
        }

        .main-sidebar.hidden .nav-link {
            visibility: hidden;
            opacity: 0;
        }

        .main-sidebar:hover {
            width: 250px;
        }

        /* CSS styles for percentage color based on fuel level */
        .low-fuel {
            color: red;
        }

        .medium-fuel {
            color: orange;
        }

        .high-fuel {
            color: green;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('images/trucking.jpg') }}" alt="Track-Truck" height="60"
                width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @if (Auth::check())
                    <li class="nav-item">
                        <span class="welcome-message">Welcome, {{ Auth::user()->name }}!</span>
                    </li>
                @else
                    <li class="nav-item">
                        <span class="welcome-message">Welcome, Guest!</span>
                    </li>
                @endif
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link"
                            style="font-weight: bold; color: white;">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="" class="brand-link">
                <img src="{{ asset('images/trucking.jpg') }}" alt="Track Truck"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Track Truck</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Menu Item 1 -->
                        <li class="nav-item">
                            <a href="{{ route('truk.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Truk</p>
                            </a>
                        </li>
                        <!-- Menu Item 2 -->
                        <li class="nav-item">
                            <a href="{{ route('supir.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Supir</p>
                            </a>
                        </li>
                        <!-- Menu Item 3 -->
                        <li class="nav-item">
                            <a href="{{ route('perjalanan.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Perjalanan</p>
                            </a>
                        </li>
                        <!-- Menu Item 4 -->
                        <li class="nav-item">
                            <a href="{{ route('gudang.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Gudang</p>
                            </a>
                        </li>
                        <!-- Menu Item 5 -->
                        <li class="nav-item">
                            <a href="{{ route('details.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Detail Perjalanan</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('lte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('lte/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('lte/plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('lte/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('lte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- Daterangepicker -->
    <script src="{{ asset('lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('lte/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('lte/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('lte/dist/js/pages/dashboard.js') }}"></script>
</body>

</html>
