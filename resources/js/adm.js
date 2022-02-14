window.redirectTo = function (url) {
    window.location.href = url;
}

window.renderChatDesempenho = function () {

    var chBar = document.getElementById("grafico-desempenho");
    if (!chBar) {
        return;
    }

    let teams = [];
    let sales = [];
    $.get('http://promocao.test/adm/desempenho', function (result) {
        teams = result.map((el) => { return el.team; });
        sales = result.map((el) => { return el.sales; });

        var chartData = {
            labels: teams,
            datasets: [{
                data: sales,
                label: 'Equipes'
            }]
        };

        new Chart(chBar, {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });
    })


}