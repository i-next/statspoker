$(document).ready(function () {

    $("#gainsfilter").on('change', function(){
        console.log($(this).val());
        ajax_chart(tournois_gains, $(this).data('url'),{'data': $(this).val()}, '#tournament_gains');
    });

    let all_range_param = {
        type: 'bar',
        data: {
            datasets: [{
                label: 'All range',
                data: [],
            }]
        }
    };
    let all_range = new Chart($('#tournament_range'), all_range_param);

    let tournoi_win_per_day_param = {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Win per day',
                data: [],
            }]
        }
    }
    let tournoi_win_per_day = new Chart($('#tournament_win_per_day'),tournoi_win_per_day_param );

    let tournoi_win_per_month_param = {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Win per month',
                data: [],
            }]
        }
    };
    let tournoi_win_per_month = new Chart($('#tournament_win_per_month'), tournoi_win_per_month_param);

    let tournois_gains_param = {
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
    }
    let tournois_gains = new Chart($('#tournament_gains'), tournois_gains_param);

    let tournois_win_party_param = {
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
    };
    let tournois_win_party = new Chart($('#tournament_win_party'), tournois_win_party_param);

    ajax_chart(all_range, $('#tournament_range').data('url'),{},'#tournament_range',all_range_param);
    ajax_chart(tournoi_win_per_month, $('#tournament_win_per_month').data('url'),{},'#tournament_win_per_month', tournoi_win_per_month_param);
    ajax_chart(tournoi_win_per_day, $('#tournament_win_per_day').data('url'),{},'#tournament_win_per_day',tournoi_win_per_day_param);
    ajax_chart(tournois_gains, $('#tournament_gains').data('url'),{},'#tournament_gains',tournois_gains_param);
    ajax_chart(tournois_win_party, $('#tournament_win_party').data('url'),{},'#tournament_win_party',tournois_win_party_param);

// function to update our chart
    function ajax_chart(chart, url, data, dest, param) {
        var data = data || {};

        console.log($(dest));
        $.getJSON(url, data).done(function(response) {
            /*chart.destroy();
            chart.clear();*/
            console.log(chart);
           /* let chart2 = new Chart($(dest), param)*/
            const obj = JSON.parse(response);
            chart.data.labels = obj.labels;
            console.log(obj.labels);
            console.log(chart.config.type);
            chart.data.datasets[0].data = obj.result; // or you can iterate for multiple datasets
            if(chart.config.type == 'bar'){
                chart.options.elements.bar.backgroundColor = colorize(false);
                chart.options.elements.bar.borderColor = colorize(false);
            }
            console.log(chart.data.datasets[0].data);
            console.log(chart.data.labels);


            chart.update('show'); // finally update our chart
        });
    }

    function colorize(opaque) {
        return (ctx) => {
            const v = ctx.parsed.y;
            const c = v < 0.5 ? '#CF0909' : '#3860BB';
            return c;
        };
    }

});

/*
{{ ranges|json_encode|raw }}

{{ tournoi_win_per_day|json_encode|raw }}
{{ tournoi_win_per_month|json_encode|raw }}
{{ tournois_gains|json_encode|raw }}
{{ tournament_win_party|json_encode|raw }}*/