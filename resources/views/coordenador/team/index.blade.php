@extends('layouts.head')

@section('title', 'Painel do Coordenador')
@section('page_title', 'Minha equipe')

@section('content')

<div class="col-sm-12">
    <a href="/teams/create" class="btn btn-primary float-right mb-2">Novo Membro</a>
</div>

<div class="col-sm-12">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th class="text-right">Bilhetes vendidos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($team as $member)
            <tr>
                <td>{{ $member['name'] }}</td>
                <td>{{ $member['phone'] }}</td>
                <td class="text-right">{{ sizeof($member['sales']) }}</td>
                <td class="text-right"><a href="/teams/{{ $member['id'] }}/edit">alterar cadastro</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection