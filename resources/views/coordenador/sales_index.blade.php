@extends('layouts.head')

@section('title', 'Vendas')
@section('page_title', 'Listagem de vendas')

@section('content')

<div class="col-sm-12">
    <input type="hidden" id="urlApp" value="{{ env('URL_APP ') }}">
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
            <th>Ticket</th>
            <th>Comprador</th>
            <th>Telefone</th>
            <th>Forma pagto</th>
            <th>Vendedor</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        @if (!isset($sale->show) || $sale->show !== false)
        <tr>
            <td>
                <input type="checkbox" data-sale="{{ $sale->id }}" aria-label="Selecione para enviar esse bilhete" class="form-control check-myzap">
            </td>
            <td class="align-middle">{{ strval($sale->ticket_number) }}</td>
            <td class="align-middle">{{ $sale->buyer }}</td>
            <td class="align-middle">{{ $sale->buyer_phone }}</td>
            <td class="align-middle">{{ $sale->payment_method }}</td>
            <td class="align-middle">{{ $sale->seller }}</td>
            <td class="align-middle">
                <a class="text-muted" href="/sales/{{$sale->id}}/edit">alterar venda</a>
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
                <button class="btn btn-primary" onclick="sendTicketsBatch()">Enviar Bilhetes</button>
            </td>
        </tr>
    </tfoot>
</table>

@if ($filter['hasPages'])
<div class="col-sm-12">
    <div class="d-flex justify-content-center">
        {!! $sales->links() !!}
    </div>
</div>
@endif

@endsection