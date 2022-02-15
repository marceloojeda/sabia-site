@extends('layouts.head')

@section('title', 'Painel do Coordenador')
@section('page_title', 'Cadastro de membro')

@section('content')

<form action="/teams/{{$user->id}}" method="post">
    @csrf
    @method('put')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label for="name">Nome</label>
                    <input class="form-control" type="text" name="name" id="name" value="{{$user->name}}">
                </div>
                <div class="col-sm-4 form-group">
                    <label for="phone">Telefone</label>
                    <input class="form-control" type="text" name="phone" id="phone" onkeypress="mask(this, mphone);" onblur="mask(this, mphone);" value="{{$user->phone}}">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-danger" onclick="sellerDelete({{ $user->id }})">Excluir da Equipe</button>
            <button type="submit" class="btn btn-primary float-right">Salvar</button>
        </div>
    </div>
</form>
@endsection