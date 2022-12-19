$(document).ready(function () {

    $("#gainsfilter").on('change', function(){

        window.parent.ajax_chart(tournois_gains, $(this).data('url'),{'data': $(this).val()}, '#tournament_gains');
    });

    $("#winpermonthfilter").on('change', function(){

        window.parent.ajax_chart(tournoi_win_per_month, $(this).data('url'),{'data': $(this).val()}, '#tournament_win_per_month');
    });

    $("#winperdayfilter").on('change', function(){

        window.parent.ajax_chart(tournoi_win_per_day, $(this).data('url'),{'data': $(this).val()}, '#tournament_win_per_day');
    });

    $("#winpartyfilter").on('change', function(){
        window.parent.ajax_chart(tournois_win_party, $(this).data('url'),{'data': $(this).val()}, '#tournament_win_party');
    });
    $("#buyinsfilter").on('change', function(){
        window.parent.ajax_chart(all_range, $(this).data('url'),{'data': $(this).val()}, '#tournament_range');
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

    window.parent.ajax_chart(all_range, $('#tournament_range').data('url'),{},'#tournament_range',all_range_param);
    window.parent.ajax_chart(tournoi_win_per_month, $('#tournament_win_per_month').data('url'),{},'#tournament_win_per_month', tournoi_win_per_month_param);
    window.parent.ajax_chart(tournoi_win_per_day, $('#tournament_win_per_day').data('url'),{},'#tournament_win_per_day',tournoi_win_per_day_param);
    window.parent.ajax_chart(tournois_gains, $('#tournament_gains').data('url'),{},'#tournament_gains',tournois_gains_param);
    window.parent.ajax_chart(tournois_win_party, $('#tournament_win_party').data('url'),{},'#tournament_win_party',tournois_win_party_param);



    function colorize(opaque) {
        return (ctx) => {
            const v = ctx.parsed.y;
            const c = v < 0.5 ? '#CF0909' : '#3860BB';
            return c;
        };
    }

});

