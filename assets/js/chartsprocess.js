
import { calculateEquity } from 'poker-odds'

$(document).ready(function(){

    const hands = [['As', 'Kh'], ['Kd', 'Qs']]
    const board = ['Td', '7s', '8d']
    const iterations = 100000 // optional
    const exhaustive = false // optional

    /*console.log(calculateEquity(hands, board, iterations, exhaustive));*/

    let url = $("#tournament").data('url');
    if(url) {
        function colorize(opaque) {
            return (ctx) => {
                const v = ctx.parsed.y;
                const c = v < 0 ? '#ff0000' : '#0000ff';
                return c;
            };
        }

        function colorize_result(opaque) {
            return (ctx) => {
                const v = ctx.parsed.x;
                const c = v !== 1 ? '#ff0000' : '#0000ff';
                return c;
            };
        }

        $.ajax({
            url: url,
            type: "POST",
            success: function (response) {

                let data = {
                    // labels: labels,
                    datasets: [{
                        label: 'Result Tournament',
                        backgroundColor: colorize(false),
                        borderColor: colorize(false),
                        data: response.win,
                    }]
                };

                let data_month = {
                    // labels: labels,
                    datasets: [{
                        label: 'Result Tournament Monthly',
                        backgroundColor: colorize(false),
                        borderColor: colorize(false),
                        data: response.win_month,
                    }]
                };

                let data2 = {
                    datasets: [{
                        label: 'Money Tournament',
                        backgroundColor: colorize(false),
                        borderColor: colorize(false),
                        data: response.prizepool,
                    }]
                }

                let data2_month = {
                    datasets: [{
                        label: 'Money Tournament Monthly',
                        backgroundColor: colorize(false),
                        borderColor: colorize(false),
                        data: response.prizepool_month,
                    }]
                }
                let positions = [];
                Object.values(response.tournament_positions).forEach(function (position) {
                    positions.push(position);
                });
                let data_config_tournament_position = {
                    label: "Positions finales",
                    labels: Object.keys(response.tournament_positions),
                    borderColor: colorize(false),
                    datasets: [{
                        label: 'Money Tournament Monthly',
                        backgroundColor: [
                            'rgb(0, 0, 204)',
                            'rgb(255, 128, 0)',
                            'rgb(255, 0, 0)',
                            'rgb(0, 0, 0)'
                        ],
                        data: positions,
                    }]
                }

                let data_config_tournament_position_dashboard = {
                    datasets: [{
                        label: "Positions finales",
                        backgroundColor: colorize_result(false),
                        borderColor: colorize_result(false),
                        data: response.tournament_positions
                    }]
                }
                const config = {
                    type: 'bar',
                    data: data,
                    options: {}
                };

                const config2 = {
                    type: 'bar',
                    data: data2,
                    options: {}
                }

                const config_month = {
                    type: 'bar',
                    data: data_month,
                    options: {}
                };

                const config2_month = {
                    type: 'bar',
                    data: data2_month,
                    options: {}
                }

                const config_tournament_position_dashboard = {
                    type: 'bar',
                    data: data_config_tournament_position_dashboard,
                    options: {}
                }

                const config_tournament_position = {
                    type: 'pie',
                    data: data_config_tournament_position,
                    option: {}
                }

                const myChart = new Chart(
                    document.getElementById('tournament'),
                    config
                );
                const myChart2 = new Chart(
                    document.getElementById('cash'),
                    config2
                );
                const myChart_month = new Chart(
                    document.getElementById('tournament_month'),
                    config_month
                );
                const myChart2_month = new Chart(
                    document.getElementById('cash_month'),
                    config2_month
                );
                const myChart_tournament_position = new Chart(
                    document.getElementById('tournament_position'),
                    config_tournament_position
                )
                const myChart_tournament_position_dashboard = new Chart(
                    document.getElementById('tournament_position_dashboard'),
                    config_tournament_position_dashboard
                )
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    }

    let url_ajax_tournoi = $("#alltournois").data('url');
    if(url_ajax_tournoi){
        $.ajax({
            url: url_ajax_tournoi,
            type: "POST",
            success: function (response) {
                console.log(response)
            }
        });
    }
});
