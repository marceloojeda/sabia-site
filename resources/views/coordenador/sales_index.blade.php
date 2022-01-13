@extends('coordenador_layout')

@section('title', 'Vendas')
@section('page_title', 'Listagem de vendas')

@section('content')

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
