/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');
require('bootstrap');
// create global $ and jQuery variables
global.$ = global.jQuery = $;
import 'bootstrap-table';
import '@fortawesome/fontawesome-free/js/all.js'
// function to update our chart
window.ajax_chart =function (chart, url, data, dest, param) {
    var data = data || {};


    $.getJSON(url, data).done(function(response) {

        const obj = JSON.parse(response);
        chart.data.labels = obj.labels;
        chart.data.datasets[0].data = obj.result; // or you can iterate for multiple datasets
        if(chart.config.type == 'bar'){
            chart.options.elements.bar.backgroundColor = colorize(false);
            chart.options.elements.bar.borderColor = colorize(false);
        }
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