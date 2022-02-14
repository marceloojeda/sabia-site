@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Painel do Administrador')

@section('content')

<!-- <div class="row mb-4">
    <div class="col-sm-3">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">Total da Promoção</h4>
            </div>
            <div class="card-body bg-primary">
                <p class="text-white">
                    Pendentes: <b>{{$headAlert['sales']['pendentes']}}</b>
                    <br>
                    Confirmados: <b>{{$headAlert['sales']['confirmados']}}</b>
                    <br>
                    Geral: <b>{{$headAlert['sales']['geral']}}</b>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="card-title text-dark">Total da equipe</h4>
            </div>
            <div class="card-body bg-warning text-center">
                <h3 class="text-dark text-decoration-bold">125 <small>5,8%</small></h3>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="card">
            <div class="card-header bg-info">
                <h4 class="card-title text-white">Meta da semana</h4>
            </div>
            <div class="card-body bg-info text-center">
                <h3 class="text-white text-decoration-bold">200 <small>9,2%</small></h3>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="card">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white">Total da semana</h4>
            </div>
            <div class="card-body bg-danger text-center">
                <h3 class="text-white text-decoration-bold">225 <small>112,5%</small></h3>
            </div>
        </div>
    </div>
</div> -->

<!-- <div class="card">
    <div class="card-header">
        <h3 class="card-title">Desempenho das Equipes</h3>
    </div>
    <div class="card-body">
        <canvas id="grafico-desempenho"></canvas>
    </div>
</div> -->

@endsection