@extends('layouts.head')

@section('content')
<div class="container">

    <!-- Alertas de vendas pendentes -->
    @if (!empty($headAlert['pending-sales']) && $headAlert['pending-sales']['total'])
    <div class="alert alert-info mb-4" role="alert">
        <strong>Atenção</strong>
        <p class="mt-2">
            Há {{ $headAlert['pending-sales']['total'] }} vendas com o status pendente.
            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#pending-sales-modal">Veja quais são</button>
        </p>
    </div>
    @endif

    <input type="hidden" id="urlApp" value="{{ env('APP_URL') }}">

    <div class="row mb-4">
        <!-- <div class="col-sm-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white">Meta da Promoção</h4>
                </div>
                <div class="card-body bg-primary text-white">
                    Meta: <b>2.160</b>
                    <br>
                    Realizado: <b>{{ $headAlert['totais']['confirmados'] }} ({{ number_format($headAlert['totais']['confirmados'] / 2160 * 100, 2) }}%)</b>
                </div>
            </div>
        </div> -->

        <div class="col-sm-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h4 class="card-title text-dark">Meta da Equipe</h4>
                </div>
                <div class="card-body bg-info">
                    Meta: <b>216</b>
                    <br>
                    Realizado: <b>{{ $headAlert['totais']['equipe'] }} ({{ number_format($headAlert['totais']['equipe'] / 216 * 100, 2) }}%)</b>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="card-title">Acumulado das Semanas</h4>
                </div>
                <div class="card-body bg-warning">
                    Meta: <b>{{ $headAlert['metas']['accumulated']['meta'] }}</b>
                    <br>
                    Realizado: <b>{{ $headAlert['metas']['accumulated']['realizado'] }} ({{ number_format($headAlert['metas']['accumulated']['realizado'] / $headAlert['metas']['accumulated']['meta'] * 100, 2) }}%)</b>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <div class="card-header bg-danger">
                    <h4 class="card-title text-white">Meta Semana Atual</h4>
                </div>
                <div class="card-body bg-danger text-white">
                    Meta: <b>{{ $headAlert['metas']['team']['billets_goal'] }}</b>
                    <br>
                    Realizado: <b>{{ $headAlert['metas']['team']['billets_actual'] }} ({{ number_format($headAlert['metas']['team']['billets_actual'] / $headAlert['metas']['team']['billets_goal'] * 100, 2) }}%)</b>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-12">
            <div class="card text-center">
                <div class="card-header">
                    Desempenho do time
                </div>
                <div class="card-body">
                    <canvas id="chBar"></canvas>
                </div>
                <div class="card-footer text-muted">
                    Meta de vendas por vendedor: {{ $headAlert['metas']['seller']['billets_goal'] }}
                </div>
            </div>
        </div>
    </div>

    @if (!empty($headAlert['pending-sales']) && $headAlert['pending-sales']['total'])
    <div class="row">

        <div class="modal" id="pending-sales-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vendas Pendentes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Comprador</th>
                                    <th>Telefone</th>
                                    <th>Data compra</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($headAlert['pending-sales']['sales'] as $sale)
                                <tr data-id="{{ $sale['id'] }}">
                                    <td>{{ $sale['buyer'] }}</td>
                                    <td>{{ $sale['buyer_phone'] }}</td>
                                    <td>{{ date('d/m/Y H:i:s', strtotime($sale['created_at'])) }}</td>
                                    <td><a href="sales/{{$sale['id']}}/edit">Confirmar vendedor</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>


@endsection
