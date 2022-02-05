@extends('layouts.head')

@section('title', 'Vendas')
@section('page_title', 'Listagem de vendas')

@section('content')

<div class="col-sm-12">
    <form action="/sales/filtered" method="POST">
        <div class="row">

            @csrf
            <div class="col-sm-5 form-group">
                <label for="">Comprador</label>
                <input type="text" class="form-control" name="buyer" id="buyer" value="{{ $filter['buyer'] ?? '' }}">
            </div>
    
            <div class="col-sm-5 form-group">
                <label for="">Vendedor</label>
                <input type="text" class="form-control" name="seller" id="seller" value="{{ $filter['seller'] ?? '' }}">
            </div>

            <div class="col-sm-2 form-group">
                <label for="" class="text-white">Filtro</label>
                <button type="submit" class="btn btn-info form-control">Filtrar</button>
            </div>
        </div>
    </form>
</div>




<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Comprador</th>
            <th>Telefone</th>
            <th>Forma pagto</th>
            <th>Vendedor</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->ticket_number }}</td>
            <td>{{ $sale->buyer }}</td>
            <td>{{ $sale->buyer_phone }}</td>
            <td>{{ $sale->payment_method }}</td>
            <td>{{ $sale->seller }}</td>
            <td>
                <a class="text-muted" href="/sales/{{$sale->id}}/edit">alterar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="col-sm-12">
    <div class="d-flex justify-content-center">
        {!! $sales->links() !!}
    </div>
</div>

@endsection