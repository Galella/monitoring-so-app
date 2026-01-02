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
                            <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg"
                                class="user-image img-circle elevation-2" alt="User Image">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- User image -->
                            <li class="user-header bg-primary">
                                <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg"
                                    class="img-circle elevation-2" alt="User Image">
                                <p>
                                    {{ Auth::user()->name }} -
                                    {{ Auth::user()->role->name === 'Super Admin' ? 'Admin' : Auth::user()->role->name ?? 'No Role' }}
                                    <small>Member since {{ Auth::user()->created_at->format('M Y') }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
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
                            @if (Auth::user()->role->name === 'admin' || Auth::user()->role->name === 'Super Admin')
                                <li class="nav-item">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Admin Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}"
                                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Manage Users</p>
                                    </a>
                                </li>
                                    <a href="{{ route('roles.index') }}"
                                        class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user-tag"></i>
                                        <p>Manage Roles</p>
                                    </a>
                                </li>
                                    <a href="{{ route('cms.index') }}"
                                        class="nav-link {{ request()->routeIs('cms.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-shipping-fast"></i>
                                        <p>CM Data</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('coins.index') }}"
                                        class="nav-link {{ request()->routeIs('coins.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-coins"></i>
                                        <p>Coin Data</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('monitoring.index') }}"
                                        class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                            @elseif(Auth::user()->role->name === 'user')
                                <li class="nav-item">
                                    <a href="{{ route('user.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>User Dashboard</p>
                                    </a>
                                </li>
                            @endif
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
                timer: 3000
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
                title: {!! json_encode(session('error')) !!}
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
