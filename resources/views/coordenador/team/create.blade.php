@extends('layouts.head')

@section('title', 'Painel do Coordenador')
@section('page_title', 'Cadastro de membro')

@section('content')

<form action="/teams" method="post">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label for="name">Nome</label>
                    <input class="form-control" type="text" name="name" id="name">
                </div>
                <div class="col-sm-6 form-group">
                    <label for="phone">Telefone</label>
                    <input class="form-control" type="text" name="phone" id="phone" onkeypress="mask(this, mphone);" onblur="mask(this, mphone);">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary float-right">Salvar</button>
        </div>
    </div>
</form>
@endsection