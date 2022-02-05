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
                    <h4 class="card-title text-white">Total da promoção</h4>
                </div>
                <div class="card-body bg-primary text-center">
                    <h3 class="text-white text-decoration-bold">600 <small>27,7%</small></h3>
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
    </div>

    <div class="row mt-4">
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

        <!-- <div class="col-sm-6">
            <div class="card text-center">
                <div class="card-header">
                    Meta Promoção
                </div>
                <div class="card-body">
                    <img src="assets/img/meta-promocao-chart.png" class="img-fluid" alt="...">
                </div>
                <div class="card-footer text-muted">
                    alguma observação
                </div>
            </div>
        </div> -->
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