@extends('coordenador_layout')

@section('title', 'Vendas')
@section('page_title', 'Registro de venda')

<script type="text/javascript" src="/assets/js/html-to-image.js"></script>
<script type="text/javascript" src="/assets/js/html-to-image.js.map"></script>

@section('content')

<div class="col-sm-12">
    <h2 class="d-block text-center mt-4">Tickets gerados com sucesso!</h2>

    <!-- Conexao com o whatsapp -->
    <div class="text-center mt-3" id="myzap-box">
        <span card="card-title">
            Whatsapp Status: <b><label id="myzap-status">Desconectado</label></b>
            <br>
            <a href="#" onclick="initMyzap(event)">[Conectar Whatsapp Web]</a>
        </span>
        <div class="d-block mt-2">
            <img src="" class="" id="myzap-qrcode">
        </div>
    </div>

    <!-- Tickets Boxes -->
    <div class="d-none justify-content-center mt-5" id="ticket-box">
        @foreach ($sales as $sale)
        <div class="card m-3">

            @if(empty($sale['billet_file']))
            <div class="bilhete-box-item card-img-top" id="{{ $sale['id'] }}">
                <div class="d-flex align-items-end" style="height: 100%;">
                    <label class="ticket-number">{{ $sale['ticket_number'] }}</label>
                </div>
            </div>
            @else
            <img src="/assets/img/billets/{{$sale['billet_file']}}" class="card-img-top">
            @endif

            <div class="card-body">
                <h5 class="card-title">{{ $sale['buyer'] }}</h5>
                <p class="card-text">
                    Fone: {{ $sale['buyer_phone'] }} <br />
                    E-mail: {{ $sale['buyer_email'] }}
                </p>
                @if(empty($sale['billet_file']))
                <button id="btnSendTicket" type="button" class="btn btn-primary" onclick="sendTicket({{ $sale['id'] }})">Enviar Bilhete</button>
                @else
                <button id="btnSendTicket" type="button" class="btn btn-primary" onclick="sendTicket({{ $sale['id'] }}, false)">Enviar Bilhete</button>
                @endif
            </div>

            <div class="card-footer d-none" id="myzap-alert">
                <div class="alert alert-danger">
                    <label id="lblMyzapAlert"></label>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<input type="hidden" name="myzap-state" id="myzap-state" value="">
<input type="hidden" name="myzap-status" id="myzap-status" value="">
<input type="hidden" name="myzap-session" id="myzap-session" value="">

@endsection