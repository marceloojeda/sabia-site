<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Páscoa do Sabiá - @yield('title')</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>

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

        <form action="/auth/login" method="post">
            @csrf

            <div class="row">
                <div class="card mb-3 col-md-6 offset-md-3">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <img src="/assets/img/logomarca.png" height="62" class="mt-3">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">CPF</h5>
                                <div class="form-group">
                                    <input type="text" name="cpf" id="cpf" class="form-control">
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-sm btn-primary" type="submit">Entrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="/assets/js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/js/panel.js"></script>

</body>

</html>