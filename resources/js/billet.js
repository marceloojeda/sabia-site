window.initMyzap = function () {
    document.getElementById('btnSendTicket').setAttribute('disabled', true);

    $('#myzapModal').modal('show');

    startMyzap();
}

window.startMyzap = function () {
    let close = false;
    let isConnecting = false;

    let startMyzapTimer = setInterval(() => {
        if(!isConnecting) {
            isConnecting = true;

            startMyzapAsync(close).then((data) => {
                isConnecting = false;
                
                if (data.state == 'QRCODE' && data.status == 'notLogged') {
                    document.getElementById('myzap-qrcode').setAttribute('src', data.qrcode);
                }
    
                if (data.state == 'CONNECTED' && data.status == 'inChat') {
                    clearInterval(startMyzapTimer);
                    document.getElementById('myzap-box').classList.remove('d-flex');
                    document.getElementById('myzap-box').classList.add('d-none');
    
                    document.getElementById('tickets-box').classList.remove('d-none');
    
                    setMyzapAlert('');
                }
    
                close = false;
            }).catch((error) => {
                isConnecting = false;
                console.log(error.message);
                alert(error.message);
            });
        }
    }, 5000);

    setTimeout(() => {
        clearInterval(startMyzapTimer);
    }, 1000 * 120)
}

window.startMyzapAsync = async function (close) {
    let startResult = null;
    await $.get(apiUrl + "/myzap/start?close=" + close, function (data, status) {
        startResult = data;
    });

    return startResult;
}

window.sendBillets = function () {
    let hasDefaultText = true;
    setMyzapAlert('Enviando Bilhetes...');

    const billets = [...document.querySelectorAll('.check-myzap:checked')].map((e, i, rows) => {
        document.getElementById('btnSendTicket').setAttribute('disabled', true);
        const ticket = document.getElementById('ticket-' + e.value);
        if (ticket.getAttribute('data-hasfile') == 'false') {
            
            const ticketDiv = document.getElementById('ticket-' + e.value);
            htmlToImage.toPng(ticketDiv)
            .then(function (imgBase64) {
                const sendData = { img: imgBase64, saleId: e.value }
                storeBillet(sendData);
                sendText(e.value, myzapSession, hasDefaultText);
            });
        } else {
            sendText(e.value, myzapSession, hasDefaultText);
        }
        
        hasDefaultText = false;
        if (i + 1 === rows.length) {
            // setMyzapAlert('Enviando Bilhetes...');
            alert('Bilhetes sendo enviados!');
            $('#myzapModal').modal('hide');
            // $.get(apiUrl + '/myzap/close');
        }
    });
}


window.setMyzapAlert = function (message) {
    const label = document.getElementById('lblMyzapAlert');
    const div = document.getElementById('myzap-alert');
    label.innerText = message;

    if (message == '' || message == undefined) {
        div.classList.add('d-none');
        document.getElementById('btnSendTicket').removeAttribute('disabled');
        return;
    }

    div.classList.remove('d-none');
    document.getElementById('btnSendTicket').removeAttribute('disabled');
}

window.sendTicketsBatch = function () {
    const urlApp = document.getElementById('urlApp').value + '/team/send-ticket-batch';
    const sales = [...document.querySelectorAll('.check-myzap:checked')].map(e => {
        return e.getAttribute('data-sale');
    });

    if(sales.length == 0) {
        alert('Nenhum bilhete foi selecionado');
    } else {
        window.location.href = urlApp + '?sales=' + sales.join('|');
    }
}

window.sendText = async function(saleId, session, hasText) {
    const serviceUrl = apiUrl + "/myzap/send-ticket/" + saleId + '?session=' + session + '&hasText=' + hasText;

    $.ajax({
        url: serviceUrl,
        method: 'get'
    }).done(function (data) {
        setMyzapAlert('') ;
    }).fail(function (err) {
        const errJson = err.responseJSON;
        setMyzapAlert(errJson.msg);
    })
}

window.storeBillet = function(sendData) {
    const serviceUrl = apiUrl + '/myzap/store-billet';

    $.ajax({
        url: serviceUrl,
        method: 'post',
        data: sendData
    }).done(function (data) {
        // sendText(saleId, myzapSession, hasDefaultText);
    }).fail(function (err) {
        const errJson = err.responseJSON;
        setMyzapAlert(errJson.msg);
    })
}