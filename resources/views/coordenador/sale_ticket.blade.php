@extends('coordenador_layout')

@section('title', 'Vendas')
@section('page_title', 'Registro de venda')

<script type="text/javascript" src="/assets/js/html-to-image.js"></script>
<script type="text/javascript" src="/assets/js/html-to-image.js.map"></script>

@section('content')

<div class="col-sm-12">
    <h2 class="d-block text-center mt-4">Tickets gerados com sucesso!</h2>
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

    <!-- <div class="d-flex justify-content-center mt-5" id="tickets"> -->
    <div class="d-none justify-content-center mt-5" id="ticket-box">
        @foreach ($sales as $sale)
        <div class="card m-3">
            <!-- <img src="/assets/img/bilhete_2.png" class="card-img-top"> -->
            <div class="bilhete-box-item" id="{{ $sale['id'] }}">
                <div class="d-flex align-items-end" style="height: 100%;">
                    <label class="ticket-number">{{ $sale['ticket_number'] }}</label>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $sale['buyer'] }}</h5>
                <p class="card-text">
                    Fone: {{ $sale['buyer_phone'] }} <br />
                    E-mail: {{ $sale['buyer_email'] }}
                </p>
                <button id="btnSendTicket" type="button" class="btn btn-primary" onclick="sendTicket({{ $sale['id'] }})">Enviar Bilhete</button>
            </div>
        </div>
        @endforeach
    </div>
</div>



<input type="hidden" name="myzap-state" id="myzap-state" value="">
<input type="hidden" name="myzap-status" id="myzap-status" value="">
<input type="hidden" name="myzap-session" id="myzap-session" value="">

@endsection