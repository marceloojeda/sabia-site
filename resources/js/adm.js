const { getJSON } = require("jquery");

window.redirectTo = function (url) {
    window.location.href = url;
}

window.getTeamPerformanceData = async function () {
    const urlApp = document.getElementById('urlApp').value + '/adm/get-teams-performance';

    let performanceData = $.get(urlApp, function (result) {
        performanceData = result;
    });

    return performanceData;
}

window.renderGraficoDesempenhoTimes = async function (chBar) {
    getTeamPerformanceData()
        .then((data) => {
            let sellers = data.map(function (member) {
                return member.head;
            });

            let sales = data.map(function (member) {
                return member.vendas;
            });

            let metas = data.map(function (member) {
                return member.meta;
            });

            renderGraficoTimes(sellers, sales, metas, chBar);

        })
        .then(() => {
            getTeamRanking();
        });
}

window.renderGraficoTimes = function (sellers, sales, metas, chBar) {
    // chart colors
    var colors = ['#007bff', '#28a745', '#333333', '#c3e6cb', '#dc3545', '#6c757d'];

    var chartData = {
        labels: sellers,
        datasets: [{
            type: 'bar',
            data: sales,
            fill: false,
            backgroundColor: colors[1],
            label: 'Equipe',
            yAxisID: 'y-axis-1'
        }
            // {
            //     type: 'line',
            //     data: metas,
            //     fill: false,
            //     label: 'Meta Semana',
            //     borderColor: colors[0],
            //     backgroundColor: colors[0],
            //     pointBorderColor: colors[0],
            //     pointBackgroundColor: colors[0],
            //     pointHoverBackgroundColor: colors[0],
            //     pointHoverBorderColor: colors[0],
            //     yAxisID: 'y-axis-2'
            // }
        ]
    };

    window.myBar = new Chart(chBar, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            tooltips: {
                mode: 'label'
            },
            elements: {
                line: {
                    fill: false
                }
            },
            scales: {
                yAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    labels: {
                        show: true,

                    }
                }]
            }
        }
    });
}

window.sellerDelete = function (seller) {
    if (confirm('Confirma exclusão desse membro da sua equipe?')) {
        window.location.href = "/teams/" + seller + '/remove';
    }
}

window.getTeamRanking = function () {
    const urlRequest = document.getElementById('urlApp').value + '/adm/get-teams-ranking';

    $.get(urlRequest, function (data, status) {
        if (status != 'success') {
            alert('Opss.. não consegui gerar os graficos de ranking das equipes');
            console.log(status);
            return;
        }
        
        const rankingBox = document.getElementById('rankingBox');
        rankingBox.innerHTML = data.view;

        const canvasRanking = [...document.getElementsByName('canvas-ranking')];
        canvasRanking.forEach((canvas) => {
            let canvasId = canvas.getAttribute('data-id');
            let canvasData = canvas.getAttribute('data-canvas');

            setWeekRankingData(canvasId, JSON.parse(canvasData));
        });
    });
}

window.setWeekRankingData = function(canvasId, rankingData) {
    const labels = rankingData.map((e) => { return e.head; });
    const sales = rankingData.map((e) => { return e.sales; });

    const chartData = {
        labels: labels,
        datasets: [{
            type: 'bar',
            data: sales,
            fill: false,
            backgroundColor: '#007bff',
            label: 'Coordenador',
            yAxisID: 'y-axis-1'
        }]
    };

    renderRankingChart(canvasId, chartData);
}


window.renderRankingChart = function(canvasId, canvasData) {
    window.rankingChart = new Chart(canvasId, {
        type: 'bar',
        data: canvasData,
        options: {
            responsive: true,
            tooltips: {
                mode: 'label'
            },
            elements: {
                line: {
                    fill: false
                }
            },
            scales: {
                yAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    labels: {
                        show: true,
    
                    }
                }]
            }
        }
    });
}

window.getDuplicateSales = function() {
    const head = document.getElementById('head');
    redirectTo('/adm/duplicate-sales?head=' + head.value);
}