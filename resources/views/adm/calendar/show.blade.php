@extends('layouts.adm')

@section('title', 'Planejamento')
@section('page_title', 'Cadastro de evento')

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3 form-group">
                <label for="title">Tipo</label>
                <select name="type" id="type" class="form-control">
                    <option value="Meta" {{ $event['type'] == 'Meta' ? 'selected' : '' }}>Meta</option>
                    <option value="Ação" {{ $event['type'] == 'Ação' ? 'selected' : '' }}>Ação</option>
                </select>
            </div>
            <div class="col-sm-3 form-group">
                <label for="title">Título</label>
                <input class="form-control" type="text" name="title" id="title" value="{{ $event['title'] }}">
            </div>
            <div class="col-sm-6 form-group">
                <label for="description">Descrição</label>
                <input class="form-control" type="text" name="description" id="description" value="{{ $event['description'] }}">
            </div>

            <div class="col-sm-3 form-group">
                <label for="begin_at">Inicio</label>
                <input class="form-control" maxlength="10" type="text" name="begin_at" id="begin_at" onkeypress="mask(this, mdate);" onblur="mask(this, mdate);" value="{{ $event['begin_at'] }}">
            </div>
            <div class="col-sm-3 form-group">
                <label for="finish_at">Fim</label>
                <input class="form-control" maxlength="10" type="text" name="finish_at" id="finish_at" onkeypress="mask(this, mdate);" onblur="mask(this, mdate);" value="{{ $event['finish_at'] }}">
            </div>

            <div class="col-sm-3 form-group">
                <label for="title">Público Alvo</label>
                <select name="audience" id="audience" class="form-control">
                    <option value="Geral" {{ $event['audience'] == 'Geral' ? 'selected' : '' }}>Geral</option>
                    <option value="Vendedores" {{ $event['audience'] == 'Vendedores' ? 'selected' : '' }}>Vendedores</option>
                    <option value="Coordenadores" {{ $event['audience'] == 'Coordenadores' ? 'selected' : '' }}>Coordenadores</option>
                    <option value="Administradores" {{ $event['audience'] == 'Administradores' ? 'selected' : '' }}>Administradores</option>
                </select>
            </div>
            <div class="col-sm-3 form-group">
                <label for="billets_goal">Meta</label>
                <input class="form-control" maxlength="4" type="text" name="billets_goal" id="billets_goal" value="{{ $event['billets_goal'] }}">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary float-right">Salvar</button>
    </div>
</div>
@endsection