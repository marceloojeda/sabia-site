@extends('coordenador_layout')

@section('title', 'Vendas')
@section('page_title', 'Registro de venda')

@section('content')

<div class="col-sm-12">
    <form action="/sales" method="post">
        @csrf
        <input type="hidden" name="amount" value="{{ $saleData['amount'] }}">
        <input type="hidden" name="is_ecommerce" value="{{ !empty($saleData['is_ecommerce']) ? 'true' : 'false' }}">
        <input type="hidden" name="payment_date" value="{{ $saleData['payment_date'] }}">
        <input type="hidden" name="payment_status" value="{{ $saleData['payment_status'] }}">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Registro de vendas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label for="buyer">Nome comprador</label>
                        <input class="form-control" type="text" name="buyer" id="buyer">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="buyer_email">Email comprador</label>
                        <input class="form-control" type="email" name="buyer_email" id="buyer_email">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="buyer_phone">Telefone comprador</label>
                        <input class="form-control" type="text" name="buyer_phone" id="buyer_phone"  onkeypress="mask(this, mphone);" onblur="mask(this, mphone);">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="amount_paid">Bilhetes</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary" type="button" id="button-addon1" onclick="setBilhetes(false);">-</button>
                            </div>
                            <input class="form-control text-center" type="text" name="amount_paid" id="amount_paid" value="1" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="button-addon1" onclick="setBilhetes();">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="payment_method">Forma pagto</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            @for($i = 0; $i < sizeof($formasPagamento); $i++) <option value="{{ $formasPagamento[$i] }}">{{ $formasPagamento[$i] }}</option>
                                @endfor
                        </select>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label for="user_id">Vendedor</label>
                        <select class="form-control" name="user_id" id="user_id">
                            @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="/sales" class="btn btn-secondary">Voltar</a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary float-right">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection