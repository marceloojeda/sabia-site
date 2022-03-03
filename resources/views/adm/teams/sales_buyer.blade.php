@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Checagem de Vendas')

@section('content')
<form action="{{ route('adm.buyers') }}" method="post">
    @csrf
    <div class="card">
        <div class="card-header">
            Filtro
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 form-group">
                    <label for="buyer">Comprador</label>
                    <input type="text" name="buyer" id="buyer" class="form-control" value="{{ $filter['buyer'] ?? '' }}">
                </div>
                <div class="col-6 form-group">
                    <label for="buyer">Vendedor</label>
                    <input type="text" name="seller" id="buyer" class="form-control" value="{{ $filter['seller'] ?? '' }}">
                </div>

                <div class="col-6 form-group">
                    <label for="buyer">Data Inicial</label>
                    <input type="text" name="data_inicio" id="buyer" class="form-control" onkeypress="mask(this, mdate);" onblur="mask(this, mdate);" value="{{ $filter['data_inicio'] ?? '' }}">
                </div>
                <div class="col-6 form-group">
                    <label for="buyer">Data Final</label>
                    <input type="text" name="data_fim" id="buyer" class="form-control" onkeypress="mask(this, mdate);" onblur="mask(this, mdate);" value="{{ $filter['data_fim'] ?? '' }}">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary float-right">Aplicar Filtro</button>
        </div>
    </div>
</form>

@if(empty($sales))
<div class="alert alert-info text-center mt-4">
    <h4 class="text-muted">Favor aplicar algum filtro</h4>
</div>
@else
<table class="table table-striped mt-4">
    <thead>
        <tr>
            <th>Vendedor</th>
            <th>Comprador</th>
            <th class="text-center">Data</th>
            <th class="text-center">Forma Pgto</th>
            <th class="text-right">Bilhete</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale['seller'] }}</td>
            <td>{{ $sale['buyer'] }}</td>
            <td class="text-center">{{ $sale['created_at'] }}</td>
            <td class="text-center">{{ $sale['payment_method'] }}</td>
            <td class="text-right">{{ $sale['ticket_number'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection