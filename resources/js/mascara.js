window.mask = function (o, f) {
    setTimeout(function () {
        var v = f(o.value);
        if (v != o.value) {
            o.value = v;
        }
    }, 1);
}

window.mphone = function (v) {
    var r = v.replace(/\D/g, "");
    r = r.replace(/^0/, "");
    if (r.length > 10) {
        r = r.replace(/^(\d\d)(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (r.length > 5) {
        r = r.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (r.length > 2) {
        r = r.replace(/^(\d\d)(\d{0,5})/, "($1) $2");
    } else {
        r = r.replace(/^(\d*)/, "($1");
    }
    return r;
}

window.mdate = function (v) {
    if (v.length == 2 || v.length == 5) {
        return v + '/';
    }
    return date;
}

window.setBilhetes = function (soma = true) {
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