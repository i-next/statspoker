$(document).ready(function () {
    function colorize(opaque) {
        return (ctx) => {
            const v = ctx.parsed.y;
            const c = v < 0.5 ? '#CF0909' : '#3860BB';
            return c;
        };
    }

    let all_range = new Chart($('#tournament_range'), {
        type: 'bar',
        data: {
            datasets: [{
                label: 'All range',
                data: [],
            }]
        }
    });
    let tournoi_win_per_day = new Chart($('#tournament_win_per_day'), {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Win per day',
                data: [],
            }]
        }
    });
    let tournoi_win_per_month = new Chart($('#tournament_win_per_month'), {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Win per month',
                data: [],
            }]
        }
    });

    let tournois_gains = new Chart($('#tournament_gains'), {
        type: 'line',
        data: {
            showXAxisLabel: false,
            datasets: [{
                label: 'Gains Tournois',
                data: [],
                fill: false,
                borderColor: '#3860BB',
                borderWidth: 1,
                tension: 0,
                pointRadius: 0,
            }]
        },
        options: {
            scales: {
                x: {  // <-- object '{' now, instead of array '[{' before in v2.x.x
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });

    const tournois_win_party = new Chart($('#tournament_win_party'), {
        type: 'line',
        data: {
            showXAxisLabel: false,
            datasets: [{
                label: 'Tournois win',
                data: [],
                fill: false,
                borderColor: '#3860BB',
                borderWidth: 1,
                tension: 0,
                pointRadius: 0,
            }]
        },
        options: {
            scales: {
                x: {  // <-- object '{' now, instead of array '[{' before in v2.x.x
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });
    ajax_chart(all_range, $('#tournament_range').data('url'));
    ajax_chart(tournoi_win_per_month, $('#tournament_win_per_month').data('url'));
    ajax_chart(tournoi_win_per_day, $('#tournament_win_per_day').data('url'));
    ajax_chart(tournois_gains, $('#tournament_gains').data('url'));
    ajax_chart(tournois_win_party, $('#tournament_win_party').data('url'));

// function to update our chart
    function ajax_chart(chart, url, data) {
        var data = data || {};

        $.getJSON(url, data).done(function(response) {
            const obj = JSON.parse(response);
            chart.data.labels = obj.labels;
            chart.data.datasets[0].data = obj.result; // or you can iterate for multiple datasets
            if(chart.config.type == 'bar'){
                chart.options.elements.bar.backgroundColor = colorize(false);
                chart.options.elements.bar.borderColor = colorize(false);
            }
            chart.update(); // finally update our chart
        });
    }
});

/*
{{ ranges|json_encode|raw }}

{{ tournoi_win_per_day|json_encode|raw }}
{{ tournoi_win_per_month|json_encode|raw }}
{{ tournois_gains|json_encode|raw }}
{{ tournament_win_party|json_encode|raw }}*/