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
            @foreach($teams as $team)
            <tr class="bg-secondary font-weight-bold text-white">
                <td>{{ $team['head'] }}</td>
                <td></td>
                <td class="text-right">{{ $team['billets'] }}</td>
            </tr>
                @foreach($team['team'] as $seller)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{{ $seller['name'] }}</td>
                    <td>{{ $seller['phone'] }}</td>
                    <td class="text-right">
                        @if($seller['billets'] <= 0)
                        {{ $seller['billets'] }}
                        @else
                        {{ $seller['billets'] }}
                        <a href="/adm/teams/sales-seller/{{ $seller['id'] }}"> [ver]</a>
                        @endif
                    </td>
                </tr>
                @endforeach
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