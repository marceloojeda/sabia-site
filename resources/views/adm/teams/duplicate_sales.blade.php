@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Vendas Duplicadas - Check')

@section('content')

<form action="adm.sales.revision" method="POST">
    @csrf
    <div class="card card-warning">
        <div class="card-header">
            <div class="row">
                <div class="form-group col-4">
                    <label for="head">Coordenador</label>
                    <select name="heade" id="head" class="form-control">
                        <option value="">Selecione um coordenador</option>
                        @foreach ($heads as $head)
                        <option value="{{ $head['id'] }}" {{ $head['id'] == $headId ? 'selected' : '' }}>{{ $head['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-info btn-lg btn-block" style="height: 100%;" onclick="getDuplicateSales()">
                        Listar Vendas
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Vendedor</th>
                        <th>Bilhete</th>
                        <th>Comprador</th>
                        <th>Data</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr class="{{ !empty($sale->suspect) ? 'bg-danger' : '' }}">
                        <td class="text-center">
                            <input type="checkbox" name="sale">
                        </td>
                        <td>{{ $sale->seller }}</td>
                        <td>{{ $sale->ticket_number }}</td>
                        <td>{{ $sale->buyer }}</td>
                        <td>{{ $sale->sale_date }}</td>
                        <td>{{ $sale->sale_hour }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>


@endsection