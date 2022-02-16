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

    <div class="row mb-4">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white">Totais da Promoção</h4>
                </div>
                <div class="card-body bg-primary text-white">
                    Meta: <b>2.160</b>
                    <br>
                    Atual: <b>{{ $headAlert['totais']['confirmados'] }} ({{ number_format($headAlert['totais']['confirmados'] / 2160 * 100, 2) }}%)</b>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="card-title text-dark">Totais da Equipe</h4>
                </div>
                <div class="card-body bg-warning">
                    Meta: <b>196,36</b>
                    <br>
                    Atual: <b>{{ $headAlert['totais']['equipe'] }} ({{ number_format($headAlert['totais']['equipe'] / 196.36 * 100, 2) }}%)</b>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-info">
                    <h4 class="card-title text-white">Meta da {{ $headAlert['metas']['adm']['title'] }}</h4>
                </div>
                <div class="card-body bg-info text-dark">
                    Período: <b>10/02 a 21/02</b>
                    <br>
                    Meta: <b>{{ $headAlert['metas']['adm']['billets_goal'] }}</b>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-danger">
                    <h4 class="card-title text-white">Vendas da Semana</h4>
                </div>
                <div class="card-body bg-danger text-white">
                    Meta: <b>{{ $headAlert['metas']['team']['billets_goal'] }}</b>
                    <br>
                    Atual: <b>{{ $headAlert['metas']['team']['billets_actual'] }}</b>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row mt-4">
        <div class="col-sm-12">
            <div class="card text-center">
                <div class="card-header">
                    Comparativo com a semana passada
                </div>
                <div class="card-body">
                    <canvas id="chBar"></canvas>
                </div>
                <div class="card-footer text-muted">
                    houve uma melhora nas vendas \o/
                </div>
            </div>
        </div>
    </div> -->

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