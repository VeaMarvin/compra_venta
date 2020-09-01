<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema Ventas Laravel Vue Js- IncanatoIT">
    <meta name="author" content="Incanatoit.com">
    <meta name="keyword" content="Sistema ventas Laravel Vue Js, Sistema compras Laravel Vue Js">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id_persona : '' }}">
    <title>Sistema Compras y Ventas</title>
    <!-- Icons -->
    <link href="css/plantilla_style.css" rel="stylesheet">
    <!-- Main styles for this application -->
    <!-- Sweet alert 
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>-->
    <script src="js/Chart.min.js"></script>
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <ul class="nav navbar-nav ml-auto" style="margin-right:2%">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="d-md-down-none"> {{ Auth::user()->usuario }} </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>
                        <a class="dropdown-item" href="/logout" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-lock"></i> Cerrar sesión</a>
                        
                            <form action="/logout" id="logout-form" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>

                    </div>
                </li>
            </ul>
            
        </header>

        <div class="app-body">

            @if(Auth::check())
                @if(Auth::user()->id_rol == 1)
                    @include( 'plantilla.sidebar_administrador' )    
                @elseif(Auth::user()->id_rol == 2)
                    @include( 'plantilla.sidebar_vendedor' )    
                @elseif(Auth::user()->id_rol == 3)
                    @include( 'plantilla.sidebar_almacenador' )       
                @endif
            @endif

            @yield( 'contenido' )
            
        </div>
    </div>
    
    <footer class="app-footer">
        <span>&copy; 2019</span>
    </footer>
    
    <script src="js/app_vue.js"></script>
    <script src="js/plantilla_script.js"></script>
</body>
</html>