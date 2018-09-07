/*
 * @author John Snook
 * @date Aug 30, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of hourlyVisitsLineChart
 */
var chartData = $.ajax({
    url: "/exploration/visits",
    dataType: "json",
    success: console.log("Visitors geojson data successfully loaded."),
    error: function (xhr) {
        alert(xhr.statusText);
    }
});

$.when(chartData).done(function () {
    var chart = c3.generate({
        bindto: '#dailyChart',
        data: {
            x: 'x',
            xFormat: '%Y-%m-%d',
            columns: Object.values(chartData.responseJSON),
            onclick: function (d, element) {
                console.log(d);
                showDayChart(d.x.toISOString().split('T')[0]);
            },
            type: 'area-spline',
            types: {
                'New Visitors': 'bar',
            },

        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%Y-%m-%d'
                }
            },
        },
        zoom: {
            enabled: true
        },
        subchart: {
            show: true
        },
        title: {
            text: 'All visits & new visitors by date'
        }

    });
});

function showDayChart(day) {
    var chartData = $.ajax({
        url: "/exploration/visits?day=" + day,
        dataType: "json",
        success: console.log("Visitors geojson data successfully loaded."),
        error: function (xhr) {
            alert(xhr.statusText);
        }
    });
    $.when(chartData).done(function () {

        var chart = c3.generate({
            bindto: '#hourlyChart',
            data: {
                x: 'x',
                xFormat: '%Y-%m-%d %H:%M:%S',
                columns: Object.values(chartData.responseJSON),
                type: 'area-spline',
                types: {
                    'New Visitors': 'bar',
                },
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: '%H:%M:%S'
                    }
                }
            },
            zoom: {
                enabled: true
            },
            title: {
                text: 'All visits & new visitors for ' + day
            }

        });
    });

}