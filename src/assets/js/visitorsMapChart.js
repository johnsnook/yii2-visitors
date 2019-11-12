/*
 * @author John Snook
 * @date Aug 30, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Displays a ploty.js map of visitor info
 */

Plotly.d3.json("/exploration/visitor-map", function (err, rows) {

    function unpack(rows, key) {
        return rows.map(function (row) {
            return row[key];
        });
    }

    var figure = {
        "frames": [],
        "layout": {
            "autosize": true,
            "title": "Visitors by ISP",
            "showlegend": false,
            "breakpoints": [],
            "shapes": [],
            "mapbox": {
                "bearing": 0,
                "style": "outdoors",
                "pitch": 0,
                "center": {
                    "lat": 37.0294878,
                    "lon": -98.2801771
                },
                "zoom": 3
            },
            "hovermode": "closest"
        },
        "data": [
            {
                "type": "scattermapbox",
                "autobiny": true,
                "autobinx": true,
                "name": "visitors",
                "text": unpack(rows, 'who-where'),
                "lon": unpack(rows, 'longitude'),
                "lat": unpack(rows, 'latitude'),
                "transforms": [
                    {
                        "style": [],
                        "enabled": true,
                        "groups": unpack(rows, 'who-where'),
                        "groupbyId": "e4b317",
                        //"groupssrc": "johnsnook:4:a32280",
                        "type": "groupby"
                    }
                ],
                //"textsrc": "johnsnook:4:a32280",
                "mode": "markers",
                "hoverinfo": "text-visits",
                "marker": {
                    "sizesrc": "johnsnook:4:440de4",
                    "sizemode": "area",
                    "sizeref": 0.12375,
                    "size": unpack(rows, 'visits'),
                },
                //"latsrc": "johnsnook:4:ff662d",
                //"lonsrc": "johnsnook:4:9dc1c9",
            }
        ]
    };

    window.PLOTLYENV = {'BASE_URL': 'https://plot.ly'};

    var gd = document.getElementById('graphContainer');
    var resizeDebounce = null;

    function resizePlot() {
        var bb = gd.getBoundingClientRect();
        Plotly.relayout(gd, {
            width: bb.width,
            height: bb.height
        });
    }

    window.addEventListener('resize', function () {
        if (resizeDebounce) {
            window.clearTimeout(resizeDebounce);
        }
        resizeDebounce = window.setTimeout(resizePlot, 100);
    });

    Plotly.plot(gd, {
        data: figure.data,
        layout: figure.layout,
        frames: figure.frames,
        config: {"mapboxAccessToken": "pk.eyJ1IjoiY2hyaWRkeXAiLCJhIjoiY2lxMnVvdm5iMDA4dnhsbTQ5aHJzcGs0MyJ9.X9o_rzNLNesDxdra4neC_A", "linkText": "Export to plot.ly", "showLink": true}
    });

});


