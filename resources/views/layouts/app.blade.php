<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title', 'Dashboard')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    @vite('resources/js/app.js')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ url('/') }}" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto"> 
                <!-- User Account Menu -->
                @auth
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            @php
                                $initials = collect(explode(' ', Auth::user()->name))
                                    ->map(fn($segment) => strtoupper(substr($segment, 0, 1)))
                                    ->take(2)
                                    ->join('');
                            @endphp
                            <div class="user-image img-circle elevation-2 d-inline-flex justify-content-center align-items-center bg-primary text-white" 
                                style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">
                                {{ $initials }}
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- User image -->
                            <li class="user-header bg-primary">
                                <div class="img-circle elevation-2 d-inline-flex justify-content-center align-items-center bg-white text-primary mx-auto mb-2" 
                                    style="width: 90px; height: 90px; font-size: 36px; font-weight: bold;">
                                    {{ $initials }}
                                </div>
                                <p>
                                    {{ Auth::user()->name }} -
                                    {{ Auth::user()->role->name === 'Super Admin' ? 'Admin' : Auth::user()->role->name ?? 'No Role' }}
                                    <small>Member since {{ Auth::user()->created_at->format('M Y') }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="{{ route('profile.edit') }}" class="btn btn-default btn-flat">Profile</a>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="btn btn-default btn-flat float-right">Sign out</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="brand-link">
                <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

                        @auth
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            @can('view_users')
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Manage Users</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_roles')
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}"
                                    class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>Manage Roles</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_wilayahs')
                            <li class="nav-item">
                                <a href="{{ route('wilayahs.index') }}"
                                    class="nav-link {{ request()->routeIs('wilayahs.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-map"></i>
                                    <p>Manage Wilayah</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_areas')
                            <li class="nav-item">
                                <a href="{{ route('areas.index') }}"
                                    class="nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-map-marker-alt"></i>
                                    <p>Manage Areas</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_cms')
                            <li class="nav-item">
                                <a href="{{ route('cms.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-shipping-fast"></i>
                                    <p>CM Data</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_coins')
                            <li class="nav-item">
                                <a href="{{ route('coins.index') }}"
                                    class="nav-link {{ request()->routeIs('coins.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-coins"></i>
                                    <p>Coin Data</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_monitoring')
                            <li class="nav-item">
                                <a href="{{ route('monitoring.index') }}"
                                    class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Monitoring</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_monitoring_so')
                            <li class="nav-item">
                                <a href="{{ route('monitoring-so.index') }}"
                                    class="nav-link {{ request()->routeIs('monitoring-so.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>Monitoring SO</p>
                                </a>
                            </li>
                            @endcan

                            @can('view_activity_logs')
                            <li class="nav-item">
                                <a href="{{ route('activity-logs.index') }}"
                                    class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>Activity Logs</p>
                                </a>
                            </li>
                            @endcan

                             <!-- Reports Menu -->
                            <li class="nav-item {{ request()->routeIs('reports.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>
                                        Laporan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports.monitoring-so.index') }}" class="nav-link {{ request()->routeIs('reports.monitoring-so.*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laporan Monitoring SO</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endauth

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Options</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; {{ date('Y') }} <a
                    href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>.</strong> All rights
            reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{ asset('adminlte') }}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte') }}/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte') }}/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- ChartJS -->
    <script src="{{ asset('adminlte') }}/plugins/chart.js/Chart.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script>
        // Add any custom JavaScript here
        $(function() {


            // Check if Swal is defined
            if (typeof Swal === 'undefined') {

            }

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });

            @if(session('success'))

            Toast.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!}
            });
            @endif

            @if(session('error'))

            Toast.fire({
                icon: 'error',
                title: {!! json_encode(session('error')) !!},
                timer: false,
                showCloseButton: true
            });
            @endif
            
            @if(session('warning'))

            Toast.fire({
                icon: 'warning',
                title: {!! json_encode(session('warning')) !!}
            });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
