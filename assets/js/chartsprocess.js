import {calculateEquity} from 'poker-odds'

$(document).ready(function () {

    let last_7_days_result_param = {
        type: 'line',
        data: {
            datasets: [{
                label: 'Last 7 Days Result',
                data: [],
                fill: false,
                borderColor: '#3860BB',
                borderWidth: 1,
                tension: 0,
                pointRadius: 0,
            }]
        }
    }

    let last_7_days_money_param = {
        type: 'line',
        data: {
            datasets: [{
                label: 'Last 7 Days maney',
                data: [],
                fill: false,
                borderColor: '#3860BB',
                borderWidth: 1,
                tension: 0,
                pointRadius: 0,
            }]
        }
    }
    let spin_and_go_result_param = {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Spin and Go Result',
                data: [],
            }]
        }
    };

    let spin_and_go_money_param = {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Spin and Go Money',
                data: [],
            }]
        }
    };

    let last_7_days_result = new Chart($('#tournament'), last_7_days_result_param);
    let last_7_days_money = new Chart($('#cash'), last_7_days_money_param);
    let spin_and_go_result = new Chart($('#spinandgoresult'), spin_and_go_result_param);
    let spin_and_go_money = new Chart($('#spinandgomoney'), spin_and_go_money_param);
    window.parent.ajax_chart(last_7_days_result, $('#tournament').data('url'),{},'#tournament',last_7_days_result_param);
    window.parent.ajax_chart(last_7_days_money, $('#cash').data('url'),{},'#cash',last_7_days_money_param);
    window.parent.ajax_chart(spin_and_go_result, $('#spinandgoresult').data('url'),{},'#spinandgoresult',spin_and_go_result_param);
    window.parent.ajax_chart(spin_and_go_money, $('#spinandgomoney').data('url'),{},'#spinandgmoney',spin_and_go_money_param);
});
