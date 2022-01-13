<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Páscoa do Sabiá - @yield('title')</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>
    @section('sidebar')
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg static-top">
        <div class="container d-block">
            <div class="row">
                <div class="col-sm-4 text-center">
                    <a class="navbar-brand" href="#">
                        <img src="/assets/img/logomarca.png" alt="..." height="36">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="col-sm-4 text-center pt-2">
                    <h4 class="card-title">Painél do Coordenador</h4>
                </div>
                <div class="col-sm-4">
                    <div class="collapse navbar-collapse float-right pt-2" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="/coordenador">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/sales/create">Nova Venda</a>
                            </li>
                            <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Dropdown
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li> -->
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

        <div class="row">
            @yield('content')
        </div>
    </div>

    <script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>


</body>

</html>
