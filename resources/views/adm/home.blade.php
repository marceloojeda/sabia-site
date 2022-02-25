@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Painel do Administrador')

@section('content')

<div class="row mb-4">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">Meta da Promoção</h4>
            </div>
            <div class="card-body bg-primary text-white">
                Meta: <b>2.160</b>
                <br>
                Realizado: <b>{{ $dashData['totalSales'] }} ({{ number_format($dashData['totalSales']/2160*100,2) }}%)</b>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="card-title text-dark">Meta da Semana</h4>
            </div>
            <div class="card-body bg-warning">
                Meta: <b>{{ $dashData['calendar']['billets_goal'] }}</b>
                <br>
                Realizado: <b>{{ $dashData['totalSalesWeek'] }} ({{ number_format($dashData['percSalesWeek'],2) }}%)</b>
            </div>
        </div>
    </div>

    <!-- <div class="col-sm-4">
        <div class="card">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white">Pior/Melhor Equipe</h4>
            </div>
            <div class="card-body bg-danger text-white">
                Pior: <b>KKKK</b>
                <br>
                Melhor: <b>JJJJJ</b>
            </div>
        </div>
    </div> -->

</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Desempenho das Equipes</h4>
    </div>
    <div class="card-body">
        <canvas id="grafico-desempenho-times"></canvas>
    </div>
    <div class="card-footer text-center">
        Meta Acumulada: {{ $dashData['metas']['accumulated']['meta'] }}
    </div>
</div>

<div class="row mt-4" id="rankingBox">
    
</div>




@endsection