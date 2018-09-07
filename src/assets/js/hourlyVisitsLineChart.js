/*
 * @author John Snook
 * @date Aug 30, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of hourlyVisitsLineChart
 */


//$.getJSON('/dashboard/hourly-visits-json', function (data) {
//
//Plotly.newPlot('graphContainer', [data]);
/***************************************************************************/
//Plotly.d3.csv("https://raw.githubusercontent.com/plotly/datasets/master/finance-charts-apple.csv", function (err, rows) {
Plotly.d3.json("/exploration/hourly", function (err, rows) {

    var lastMonth = new Date((new Date()).setMonth((new Date()).getMonth() - 1)).toISOString().slice(0, 10);
    var today = new Date().toISOString().slice(0, 10);
    var minDate = rows[0].x;

    function unpack(rows, key) {
        return rows.map(function (row) {
            return row[key];
        });
    }
    var visitors = {
        type: "scatter",
        mode: "markers",
        name: 'New Visitors',
        x: unpack(rows, 'x'),
        y: unpack(rows, 'visitors'),
        line: {color: '#17BE17'}
    }

    var visits = {
        type: "scatter",
        mode: "lines",
        name: 'Visits',
        x: unpack(rows, 'x'),
        y: unpack(rows, 'visits'),
        line: {color: '#4F4FBF'}
    }


    var data = [visitors, visits];
    var layout = {
        title: 'Visits & Visitors',
        xaxis: {
            autorange: true,
            range: [minDate, today],
            rangeselector: {buttons: [
                    {
                        count: 1,
                        label: '1d',
                        step: 'day',
                        stepmode: 'backward'
                    },
                    {
                        count: 1,
                        label: '1w',
                        step: 'week',
                        stepmode: 'backward'
                    },
                    {
                        count: 1,
                        label: '1m',
                        step: 'month',
                        stepmode: 'backward'
                    },
                    {
                        count: 3,
                        label: '3m',
                        step: 'month',
                        stepmode: 'backward'
                    },
                    {
                        count: 6,
                        label: '6m',
                        step: 'month',
                        stepmode: 'backward'
                    },
                    {step: 'all'}
                ]},
            rangeslider: {range: [lastMonth, today]},
            type: 'date'
        },
        yaxis: {
            autorange: true,
            range: [0, 300],
            type: 'linear'
        }
    };
    Plotly.newPlot('graphContainer', data, layout);
})

//                });