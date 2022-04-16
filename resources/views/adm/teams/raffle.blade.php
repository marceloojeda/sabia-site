@extends('layouts.adm')

@section('title', 'Painel do Administrador')
@section('page_title', 'Sorteio')

@section('content')
<form action="{{ route('adm.raffle') }}" method="post">
    @csrf
    <div class="card">
        <div class="card-header">
            Confere ganhador
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 form-group">
                    <label for="numero">Número sorteado</label>
                    <input type="text" name="numero" id="numero" class="form-control" value="{{ $filter['numero'] ?? '' }}">
                </div>
                <div class="col-6 form-group">
                    <label for="btn" class="text-white">button</label>
                    <button type="submit" class="btn btn-primary btn-block">Ver Ganhador</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if(!empty($winner))

<div class="card text-white mt-3">
    <div class="card-header bg-success pt-4">
        <h4 class="card-title">Ganhador encontrado!</h4>
    </div>
    <div class="card-body">
        <div class="row text-dark">
            <div class="col-3 form-group">
                <label for="billet">Bilhete</label>
                <input type="text" class="form-control" disabled id="billet" name="billet" value="{{ $winner['billet'] }}">
            </div>

            <div class="col-6 form-group">
                <label for="buyer">Nome</label>
                <input type="text" class="form-control text-lg" disabled id="buyer" name="buyer" value="{{ $winner['buyer'] }}">
            </div>
            <div class="col-3 form-group">
                <label for="buyerPhone">Telefone</label>
                <input type="text" class="form-control" disabled id="buyerPhone" name="buyerPhone" value="{{ $winner['buyerPhone'] }}">
            </div>

            <div class="col-3 form-group">
                <label for="saleDate">Data venda</label>
                <input type="text" class="form-control" disabled id="saleDate" name="saleDate" value="{{ $winner['saleDate'] }}">
            </div>
            <div class="col-6 form-group">
                <label for="seller">Vendedor</label>
                <input type="text" class="form-control text-lg" disabled id="seller" name="seller" value="{{ $winner['seller'] }}">
            </div>
        </div>
    </div>
</div>
@elseif (empty($winner) && !empty($filter))
<div class="alert alert-warning text-center pt-4 mt-4">
    <h4>Bilhete não encontrado</h4>
</div>
@endif
@endsection