<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
    @section('sidebar')
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg static-top">
        <div class="container d-block">
            <div class="row">
                <div class="col-sm-4 text-center">
                    <a class="navbar-brand" href="#">
                        <img src="/assets/img/logomarca.png" alt="..." height="100">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="col-sm-4 text-center pt-2">
                </div>
                <div class="col-sm-4">
                    <div class="collapse navbar-collapse float-right pt-2" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-4">
                            <!-- Right Side Of Navbar -->
                            <!-- Authentication Links -->
                            @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                            @endif
                            @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @show

    <div class="container mt-4">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="container">
            <div class="alert alert-secondary">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>@yield('page_title')</h2>
                        <input type="hidden" id="urlApp" value="{{ env('APP_URL') }}">
                        <input type="hidden" id="panelName" value="ADMINISTRADOR">
                    </div>
                    <div class="col-sm-6 text-right mt-2">
                        <a href="/home" class="mr-2">Home</a>
                        <a href="/calendars" class="ml-2 mr-2">Planejamento</a>
                        <a href="/adm/teams" class="ml-2">Equipes e Vendas</a>
                    </div>
                </div>
            </div>

            @yield('content')
            
        </div>
    </div>

    <!-- <script type="text/javascript" src="/assets/js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/js/mascara.js"></script>
    <script type="text/javascript" src="/assets/js/adm.js"></script> -->

    <script type="text/javascript" src="/js/app.js"></script>

</body>

</html>