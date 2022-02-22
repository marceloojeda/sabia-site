window.initMyzap = function () {
    document.getElementById('btnSendTicket').setAttribute('disabled', true);

    $('#myzapModal').modal('show');

    startMyzap();
}

window.startMyzap = function () {
    let close = true;
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
            })
            .fail((error) => {
                isConnecting = false;
                console.log(error.message);
                alert(error.message);
            });
        }
    }, 2000);

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
    const billets = [...document.querySelectorAll('.check-myzap:checked')].map((e,i,rows) => {
        document.getElementById('btnSendTicket').setAttribute('disabled', true);
        const ticket = document.getElementById('ticket-' + e.value);
        if (ticket.getAttribute('data-hasfile') == 'false') {
            exportBillet(e.value, hasDefaultText, i + 1 === rows.length);
        } else {
            $.get(apiUrl + "/myzap/send-ticket/" + e.value + '?session=' + myzapSession + '&hasText=' + hasDefaultText)
                .fail((jqXHR, textStatus, errorThrown) => {
                    setMyzapAlert(jqXHR.responseText);
                    return;
                }).done((data) => {
                    if (i + 1 === rows.length) {
                        setMyzapAlert('');
                        alert('Bilhetes enviados!');
                        $('#myzapModal').modal('hide');
                        $.get(apiUrl + '/myzap/close');
                    }
                });
        }
        if (i === 0) {
            hasDefaultText = false
        }
    });
}

window.exportBillet = function (saleId, hasDefaultText, lastItem) {
    htmlToImage.toPng(document.getElementById('ticket-' + saleId))
        .then(function (dataUrl) {
            $.post(apiUrl + '/myzap/store-billet', { img: dataUrl, saleId: saleId }).done((result) => {
                $.get(apiUrl + "/myzap/send-ticket/" + saleId + '?session=' + myzapSession + '&hasText=' + hasDefaultText)
                    .fail((jqXHR, textStatus, errorThrown) => {
                        setMyzapAlert(jqXHR.responseText);
                        return;
                    }).done((data) => {
                        if (lastItem) {
                            setMyzapAlert('');
                            alert('Bilhetes enviados!');
                            $('#myzapModal').modal('hide');
                            $.get(apiUrl + '/myzap/close');
                        }
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