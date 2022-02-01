@extends('coordenador_layout')

@section('title', 'Vendas')
@section('page_title', 'Registro de venda')

<script type="text/javascript" src="/assets/js/html-to-image.js"></script>
<script type="text/javascript" src="/assets/js/html-to-image.js.map"></script>

<script type="text/javascript" src="/assets/js/billet.js"></script>

<script>
    const myzapUrl = "{{ env('MYZAP_URL') }}";
    const myzapSessionKey = "{{ env('MYZAP_SESSION_KEY') }}";
    const myzapToken = "{{ env('MYZAP_TOKEN') }}";
    const myzapSession = "{{ $session }}";
    const apiUrl = "{{ env('APP_URL') }}";
</script>

@section('content')

<div class="col-sm-12 d-flex justify-content-center m-2">
    <button class="btn btn-lg btn-primary" type="button" onclick="initMyzap_()">Enviar Ticket(s)</button>
</div>

    @for ($i = 0; $i < sizeof($sales); $i++) 
    <div class="card col-sm-3 p-0">
        @if(empty($sales[$i]['billet_file']))
        <div class="bilhete-box-item card-img-top" id="ticket-{{ $sales[$i]['id'] }}" data-hasfile="false">
            <div class="d-flex align-items-end" style="height: 100%;">
                <label class="ticket-number">{{ $sales[$i]['ticket_number'] }}</label>
            </div>
        </div>
        @else
        <img src="/assets/img/billets/{{$sales[$i]['billet_file']}}" class="card-img-top" id="ticket-{{ $sales[$i]['id'] }}" data-hasfile="true">
        @endif
    </div>
    @endfor

    <!-- Modal -->
    <div class="modal fade" id="myzapModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Whatsapp Web</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center" id="myzap-box">
                        <img src="/assets/img/loading.gif" class="img-responsive" id="myzap-qrcode">
                    </div>

                    <div class="d-none" id="tickets-box">
                        <p>
                            Quais tickets quer enviar para <b>{{ $sales[0]['seller'] }}</b>
                        </p>
                        <p>
                            <ul class="list-group">
                                @foreach($sales as $sale)
                                <li class="list-group-item m-0 p-0 ">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" checked name="checkBoxTicket" value="{{$sale['id']}}" class="check-myzap">
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" value="{{$sale['ticket_number']}}" disabled>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </p>

                        <div class="alert alert-danger d-none" id="myzap-alert">
                            <label id="lblMyzapAlert"></label>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Voltar</button>
                    <button type="button" class="btn btn-primary" id="btnSendTicket" onclick="sendBillets()">Enviar Bilhetes</button>
                </div>
            </div>
        </div>
    </div>

    @endsection