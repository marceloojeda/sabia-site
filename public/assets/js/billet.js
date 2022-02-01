function initMyzap_() {
    $('#myzapModal').modal('show');

    startMyzap();
}

function startMyzap() {
    let close = true;
    let startMyzapTimer = setInterval(() => {
        startMyzapAsync(close).then((data) => {
            if (data.state == 'QRCODE' && data.status == 'notLogged') {
                document.getElementById('myzap-qrcode').setAttribute('src', data.qrcode);
            }

            if (data.state == 'CONNECTED' && data.status == 'inChat') {
                clearInterval(startMyzapTimer);
                document.getElementById('myzap-box').classList.remove('d-flex');
                document.getElementById('myzap-box').classList.add('d-none');

                document.getElementById('tickets-box').classList.remove('d-none');
            }

            close = false;
        });
    }, 2000);

    setTimeout(() => {
        clearInterval(startMyzapTimer);
    }, 1000 * 120)
}

async function startMyzapAsync(close) {
    let startResult = null;
    await $.get(apiUrl + "/myzap/start?close=" + close, function (data, status) {
        startResult = data;
    });

    return startResult;
}

function sendBillets() {
    const billets = [...document.querySelectorAll('.check-myzap:checked')].map(e => {
        const ticket = document.getElementById('ticket-' + e.value);
        if(ticket.getAttribute('data-hasfile') == 'false') {
            exportBillet(e.value);
        } else {
            $.get(apiUrl + "/myzap/send-ticket/" + e.value + '?session=' + myzapSession)
                .fail((jqXHR, textStatus, errorThrown) => {
                    // setMyzapAlert(jqXHR.responseText);
                }).done((data) => {
                    // setMyzapAlert('');
                    // alert('Bilhete enviado!');
                });
        }
    });

    alert('Bilhetes enviados!');
}

function exportBillet(saleId) {
    htmlToImage.toPng(document.getElementById('ticket-' + saleId))
        .then(function (dataUrl) {
            $.post(apiUrl + '/myzap/store-billet', { img: dataUrl, saleId: saleId }).done((result) => {
                $.get(apiUrl + "/myzap/send-ticket/" + saleId + '?session=' + myzapSession)
                    .fail((jqXHR, textStatus, errorThrown) => {
                        // setMyzapAlert(jqXHR.responseText);
                    }).done((data) => {
                        // setMyzapAlert('');
                        // alert('Bilhete enviado!');
                    });
            }).fail((err) => {
                // console.log(err)
            })
        });
}

function setMyzapAlert(message) {
    const label = document.getElementById('lblMyzapAlert');
    const div = document.getElementById('myzap-alert');
    label.innerText = message;
    
    if(message == '' || message == undefined) {
        div.classList.add('d-none');
        document.getElementById('btnSendTicket').removeAttribute('disabled');
        return;
    }
    
    div.classList.remove('d-none');
    document.getElementById('btnSendTicket').removeAttribute('disabled');
}