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
            <tr>
                @if($team->head != $head)
                    @php
                        $head = $team->head;
                    @endphp
                    <td colspan="3" class="bg-info"><b>Coordenador: {{ $head }}</b></td>
                @else
                    <td>&nbsp;&nbsp;&nbsp;{{ $team->name }}</td>
                    <td>{{ $team->phone }}</td>
                    <td class="text-right">{{ $team->billets }}</td>
                @endif
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
@endsection