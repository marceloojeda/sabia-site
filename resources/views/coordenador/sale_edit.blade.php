@extends('layouts.head')

@section('title', 'Vendas')
@section('page_title', 'Registro de venda')

@section('content')

<div class="col-sm-12">
    <form action="/sales/{{ $sale->id }}" method="post">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Registro de vendas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label for="buyer">Nome comprador</label>
                        <input class="form-control" type="text" name="buyer" id="buyer" value="{{ $sale->buyer }}">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="buyer_email">Email comprador</label>
                        <input class="form-control" type="email" name="buyer_email" id="buyer_email" value="{{ $sale->buyer_email }}">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="buyer_phone">Telefone comprador</label>
                        <input class="form-control" type="text" name="buyer_phone" id="buyer_phone" value="{{ $sale->buyer_phone }}" onkeypress="mask(this, mphone);" onblur="mask(this, mphone);">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="amount_paid">Valor pago</label>
                        <input class="form-control" type="text" name="amount_paid" id="amount_paid" value="{{ $sale->amount_paid }}" readonly>                        
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="payment_method">Forma pagto</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            @foreach($formasPagamento as $formaPgto)
                            <option {{ $formaPgto == $sale->payment_method ? 'selected' : '' }} value="{{ $formaPgto }}">{{ $formaPgto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="user_id">Vendedor</label>
                        <select class="form-control" name="user_id" id="user_id">
                            @foreach($vendedores as $vendedor)
                            <option {{ $vendedor->id == $sale->user_id ? 'selected' : '' }} value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-4">
                        <a href="/sales" class="btn btn-secondary">Voltar</a>
                    </div>
                    <div class="col-sm-4 text-center">
                        @if(!empty($sale->ticket_number))
                        <!-- <a href="/sales/{{ $sale->id }}" class="btn btn-warning">Ticket</a> -->
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary float-right">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
