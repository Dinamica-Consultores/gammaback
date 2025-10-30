<!DOCTYPE html>
<html lang="en" data-bs-theme-mode="dark">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">  
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
   <!-- Daterange picker -->
   <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
   @stack('third_party_stylesheets')

    @stack('page_css') 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css">

    <style>
        .choices__input {
            background:transparent;
        }
        .choices{
            
            background:transparent;
        }
        .choices__inner{
            background:transparent;
        }
        .is-open .choices__list--dropdown {
            background:#343a40 !important;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted{
            background:#343a40 !important;

        }
        .dark-mode input:-webkit-autofill, .dark-mode input:-webkit-autofill:hover, .dark-mode input:-webkit-autofill:focus, .dark-mode textarea:-webkit-autofill, .dark-mode textarea:-webkit-autofill:hover, .dark-mode textarea:-webkit-autofill:focus, .dark-mode select:-webkit-autofill, .dark-mode select:-webkit-autofill:hover, .dark-mode select:-webkit-autofill:focus{
        -webkit-text-fill-color: rgb(0,0,0) !important;
        font-weight:bolder;
}
.nav-sidebar .menu-open > .nav-treeview {
    padding: 15px;
}
    </style>
</head>
@if(!isset(Auth::user()->name ))
<script>
window.location.href = "{{route('login')}}"
</script>
@else
<body class="hold-transition sidebar-mini layout-fixed dark-mode" data-bs-theme="dark">
<div class="wrapper" >
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand ">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        @if(Auth::user()->getControlStudioCantidades()>0)
<p class="text-align-center m-auto " style="font-size:20px;">
        <span class="badge badge-danger badge-counter"> Revise su perfil y dirijase a control de cuentas</span>

</p>

        @endif

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <span class="badge badge-danger badge-counter"> {{Auth::user()->getControlStudioCantidades()}}</span>
                    <img src="{{asset('icono_GE.svg')}}"
                         class="user-image img-circle elevation-2" alt="User Image">
                         
                    <span class="d-none d-md-inline">
@if(isset(Auth::user()->name ))
                    {{ Auth::user()->name }}
@endif</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <img src="{{asset('icono_GE.svg')}}"
                             class="img-circle elevation-2"
                             alt="User Image">
                        <p>
                        @if(isset(Auth::user()->name ))
                    {{ Auth::user()->name }}
@endif
                                 </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="{{route('controlcuentas.index')}}" class="btn btn-default btn-flat">
                    <span class="badge badge-danger badge-counter">{{Auth::user()->getControlStudioCantidades()}}</span> Control Cuentas</a>
                        <a href="#" class="btn btn-default btn-flat float-right"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Left side column. contains the logo and sidebar -->
@include('layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.1
        </div>
        <strong>Copyright &copy; 2024 Gamma Gestion Empresarial.</strong> 
    </footer>
</div>

@endif
<!-- AdminLTE App -->
<!--script src="dist/js/adminlte.js"></script-->
<script src="{{ mix('js/app.js') }}"></script>
<!-- ChartJS -->
<script src="/plugins/chart.js/Chart.min.js"></script>
<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>   
<!-- jQuery UI 1.11.4 -->
<script src="/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('third_party_scripts')
@stack('page_scripts')
</body>
</html>
