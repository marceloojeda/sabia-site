window.initMyzap = function () {
    document.getElementById('btnSendTicket').setAttribute('disabled', true);
    document.getElementById('myzap-box').classList.add('d-flex');
    document.getElementById('myzap-box').classList.remove('d-none');
    document.getElementById('tickets-box').classList.add('d-none');

    $('#myzapModal').modal('show');

    startMyzap();
}

window.startMyzap = function () {
    let isConnected = false;
    $.get(apiUrl + "/myzap/start", function(data) {
        if (data.state == 'QRCODE' && data.qrcode != undefined) {
            document.getElementById('myzap-qrcode').setAttribute('src', data.qrcode);
        } 
        
        if (data.state == 'CONNECTED' && data.status == 'inChat') {
            document.getElementById('myzap-box').classList.remove('d-flex');
            document.getElementById('myzap-box').classList.add('d-none');
            document.getElementById('tickets-box').classList.remove('d-none');

            setMyzapAlert('');
        }

        let checkMyzapTimer = setInterval(() => {
            isConnected = checkMyzapSession();
            setTimeout(() => {
                if (isConnected) {
                    clearInterval(checkMyzapTimer);
                }
            }, 2000)
        }, 3000);

        // Encerra tentativas após 1 min
        setTimeout(() => {
            clearInterval(checkMyzapTimer);
        }, 1000 * 60)
    }).catch(function(err) {
        setMyzapAlert(err.responseText);
    });
}

window.checkMyzapSession = function() {
    $.get(apiUrl + "/myzap/check-state/" + myzapSession, function(data) {
        if(data.qrcode && data.qrcode != '' && data.qrcode != undefined) {
            document.getElementById('myzap-qrcode').setAttribute('src', data.qrcode);
        }
        
        if (data.state == 'CONNECTED' && data.status == 'inChat') {
            document.getElementById('myzap-box').classList.remove('d-flex');
            document.getElementById('myzap-box').classList.add('d-none');
            document.getElementById('tickets-box').classList.remove('d-none');

            setMyzapAlert('');

            return true;
        }

        return false;
    }).catch(function(err) {
        setMyzapAlert(err.responseText);
        return true;
    });
}

window.sendBillets = function () {
    let hasDefaultText = true;
    const billets = [...document.querySelectorAll('.check-myzap:checked')].map((e, i, rows) => {
        document.getElementById('btnSendTicket').setAttribute('disabled', true);
        const ticket = document.getElementById('ticket-' + e.value);
        if (ticket.getAttribute('data-hasfile') == 'false') {

            // intervalo entre as requisições
            setTimeout(() => {
                exportBillet(e.value, hasDefaultText, i + 1 === rows.length);
                hasDefaultText = false;
            }, 2000);

        } else {

            // intervalo entre as requisições
            setTimeout(() => {
                $.get(apiUrl + "/myzap/send-ticket/" + e.value + '?session=' + myzapSession + '&hasText=' + hasDefaultText, function(data) {
                    if (i + 1 === rows.length) {
                        setMyzapAlert('');
                        alert('Bilhetes enviados!');
                        $('#myzapModal').modal('hide');
                        $.get(apiUrl + '/myzap/close/' + myzapSession);
                    }
                }).catch(function(err) {
                    setMyzapAlert(err.responseText);
                    return;
                });
            }, 2000);

        }
        hasDefaultText = false;
    });
}

window.exportBillet = function (saleId, hasDefaultText, lastItem) {
    htmlToImage.toPng(document.getElementById('ticket-' + saleId))
        .then(function (dataUrl) {
            $.post(apiUrl + '/myzap/store-billet', { img: dataUrl, saleId: saleId }).done((result) => {
                $.get(apiUrl + "/myzap/send-ticket/" + saleId + '?session=' + myzapSession + '&hasText=' + hasDefaultText, function(data) {
                    if (lastItem) {
                        setMyzapAlert('');
                        alert('Bilhetes enviados!');
                        $('#myzapModal').modal('hide');
                        $.get(apiUrl + '/myzap/close/' + myzapSession);
                    }
                }).catch(function(err) {
                    setMyzapAlert(jqXHR.responseText);
                    return;
                });
            }).fail((err) => {
                // console.log(err)
            })
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
    let urlApp = document.getElementById('urlApp');
    if (!urlApp) {
        return;
    }

    urlApp = document.getElementById('urlApp').value + '/team/send-ticket-batch';
    const sales = [...document.querySelectorAll('.check-myzap:checked')].map(e => {
        return e.getAttribute('data-sale');
    });

    if(sales.length == 0) {
        alert('Nenhum bilhete foi selecionado');
    } else {
        window.location.href = urlApp + '?sales=' + sales.join('|');
    }
}