let myzapSession = '';
let myzapState = '';
let myzapStatus = '';
let myzapQrcode = false;
let checkState = 'stop';

const apiUrl = 'http://52.91.174.190';

function setBilhetes(soma = true) {
    atual = parseInt($('#amount_paid').val());
    const input = document.getElementById('amount_paid');
    if (soma) {
        atual = atual + 1;
        input.value = atual;
        return;
    }

    if (atual <= 1) {
        atual = 1;
    } else {
        atual = atual - 1;
    }

    input.value = atual;
}

async function sendTicket(saleId) {
    const file = await exportBillet(saleId);
    document.getElementById('btnSendTicket').setAttribute('disabled', true);
    $.get(apiUrl + "/myzap/send-ticket/" + saleId + '?session=' + myzapSession, function (data, status) {
        document.getElementById('btnSendTicket').removeAttribute('disabled');
    });
}

function exportBillet(saleId) {
    let file = '';
    htmlToImage.toPng(document.getElementById(saleId))
        .then(function (dataUrl) {
            $.post(apiUrl + '/myzap/store-billet', {img: dataUrl, saleId: saleId})
            .done((result) => {
                file = result.file;
            }).fail((err) => {
                alert(err.responseText)
            })
        });

    return file;
}

async function initMyzap(event) {
    event.preventDefault();

    startMyzap();

    let timerMyzapCheck = setInterval(() => {
        if (checkStatus()) {
            clearInterval(timerMyzapCheck);
        }
    }, 1000);
}

async function startMyzapAsync() {
    let startResult = null;
    await $.get(apiUrl + "/myzap/start/", function (data, status) {
        startResult = data;
    });

    return startResult;
}

function startMyzap() {
    const hiddenState = document.getElementById('myzap-state');
    const hiddenStatus = document.getElementById('myzap-status');
    const hiddenSession = document.getElementById('myzap-session');

    checkState = 'running';
    startMyzapAsync()
        .then((startResult) => {
            hiddenState.value = startResult.state;
            hiddenStatus.value = startResult.status;
            hiddenSession.value = startResult.session;

            myzapSession = hiddenSession.value;
            myzapState = hiddenState.value;
            myzapStatus = hiddenStatus.value;
            
            checkState = 'stop';
        })
        .then(() => {
            if ((hiddenState.value == 'DISCONNECTED' || hiddenState.value == 'QRCODE') &&
                (hiddenStatus.value == 'qrReadError' || hiddenStatus.value == 'notLogged')
            ) {
                if (hiddenState.value == 'QRCODE' && hiddenStatus.value == 'notLogged') {
                    showQrcode();
                }
            }
        });
}

async function qrcodeMyzapAsync(session) {
    let qrcodeResult = null;
    await $.get(apiUrl + "/myzap/qrcode/" + session, function (data, status) {
        qrcodeResult = data;
    });

    return qrcodeResult;
}

function qrcodeMyzap(session) {
    const hiddenState = document.getElementById('myzap-state');
    const hiddenStatus = document.getElementById('myzap-status');

    if (hiddenState.value == 'QRCODE' && hiddenStatus.value == 'notLogged') {
        return;
    }

    // tenta carregar o QR Code
    let timerQrcodeMyzap = setInterval(() => {
        if(checkState == 'stop') {
            checkState = 'running';
            qrcodeMyzapAsync(session).then((qrcodeResult) => {
                hiddenState.value = qrcodeResult.state;
                hiddenStatus.value = qrcodeResult.status;
    
                if (qrcodeResult.result == 'success' && qrcodeResult.state == 'QRCODE' && qrcodeResult.status == 'notLogged') {
                    clearInterval(timerQrcodeMyzap);
    
                    showQrcode(startResult);
                }

                checkState = 'stop';
            });
        }
    }, 2000);

    // after 10 seconds stop
    setTimeout(() => {
        clearInterval(timerQrcodeMyzap);
    }, 20000);
}

function showQrcode() {
    // const hiddenState = document.getElementById('myzap-state');
    // const hiddenStatus = document.getElementById('myzap-status');
    const imgQrcode = document.getElementById('myzap-qrcode');

    qrcodeMyzapAsync(myzapSession).then((url) => {
        imgQrcode.setAttribute('src', url);
        myzapQrcode = true;
        // hiddenState.value = qrcodeResult.state;
        // hiddenStatus.value = qrcodeResult.status;
    })
}

function checkStatus() {
    if (myzapState == '' || checkState == 'running') {
        return false;
    }

    if (myzapState == 'CONNECTED' && myzapStatus == 'inChat') {
        const myzapBox = document.getElementById('myzap-box');
        const ticketBox = document.getElementById('ticket-box');

        myzapBox.classList.add('d-none');
        ticketBox.classList.remove('d-none');
        ticketBox.classList.add('d-flex');

        return true;
    }

    if (!myzapQrcode && myzapState == 'QRCODE' && myzapStatus == 'notLogged') {
        showQrcode(myzapSession);
    } else if (myzapQrcode && myzapState == 'QRCODE' && myzapStatus == 'notLogged') {
        startMyzap();
    }

    return false;
}
