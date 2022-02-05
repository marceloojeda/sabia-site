@extends('layouts.adm')

@section('title', 'Planejamento')
@section('page_title', 'Calendário da promoção')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <a href="/calendars/create" class="btn btn-primary float-right mb-2">Novo Evento</a>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="text-center">Tipo</th>
            <th class="text-center">Inicio</th>
            <th class="text-center">Fim</th>
            <th>Título</th>
            <th>Público Alvo</th>
            <th class="text-right">Meta</th>
            <th class="text-right">Vendas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($calendars as $event)
        <tr onclick="redirectTo('/calendars/' + {{$event['id']}} + '/edit')" style="cursor: pointer;">
            <td class="text-center">{{ $event['type'] }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($event['begin_at'])) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($event['finish_at'])) }}</td>
            <td>{{ $event['title'] }}</td>
            <td>{{ $event['audience'] }}</td>
            <td class="text-right">{{ $event['billets_goal'] }}</td>
            <td class="text-right">{{ $event['billets_actual'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-secondary text-white">
            <td colspan="6" class="text-right font-weight-bold">{{ array_sum(array_column($calendars, 'billets_goal')) }}</td>
            <td class="text-right font-weight-bold"></td>
        </tr>
    </tfoot>
</table>

@endsection