@extends('layouts.adm')

@section('title', 'Painel do Responsável')
@section('page_title', 'Equipes')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
            <th>&nbsp;&nbsp;&nbsp;Vendedor</th>
            <th>Celular</th>
            <th class="text-right">Bilhetes vendidos</th>
        </tr>
    </thead>
    <tbody>
        @if(!$teams)
        <tr>
            <td colspan="3" class="text-center">nenhuma equipe cadastrada</td>
        </tr>
        @else
            @php
            $head = '';
            @endphp
            @foreach($teams as $team)
                @if($team['head'] != $head)
                    @php
                    $head = $team['head'];
                    @endphp
                    <tr class="bg-info">
                        <td>{{ $team['name'] }}</td>
                        <td></td>
                        <td class="text-right">{{ $team['billets'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;{{ $team['name'] }}</td>
                        <td>{{ $team['phone'] }}</td>
                        <td class="text-right">{{ $team['billets'] }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr class="bg-secondary">
            <td colspan="3" class="text-right">$team</td>
        </tr>
    </tfoot>
</table>
@endsection