@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Vendas realizadas')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
            <th>Vendedor</th>
            <th>Comprador</th>
            <th>Celular</th>
            <th>Data</th>
            <th class="text-right">Bilhete</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale['seller'] }}</td>
            <td>{{ $sale['buyer'] }}</td>
            <td>{{ $sale['buyer_phone'] }}</td>
            <td>{{ $sale['created_at'] }}</td>
            <td class="text-right">{{ $sale['ticket_number'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection