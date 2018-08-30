<?php

/**
 * @author John Snook
 * @date Aug 23, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of visits_map
 */
/* @var $this yii\web\View */
/* @var $searchModel \johnsnook\visitors\models\VisitorSearch */

use johnsnook\googlechart\GoogleChart;
use johnsnook\visitors\models\Visits;
use yii\helpers\Json;
use yii\db\Expression;

$this->registerJsFile('https://www.gstatic.com/charts/loader.js', ['position' => \yii\web\View::POS_END]);

//$mapData = $searchModel->mapChartData;
//$query = $searchModel->parselQuery->dbQuery;
/* @var $query \yii\db\ActiveQuery */
$query = Visits::find()
        ->select([
            new Expression('count(*)'),
            'visitDate' => new Expression('created_at::date'),
            'ip'
        ])
        ->groupBy('visitDate, ip')
        ->orderBy('visitDate');
//        echo $this->parselQuery->sql;
//        echo $query->createCommand()->getRawSql();
//        die();

$sumQuery = new yii\db\Query([
    'select' => ['visitDate', 'count' => new Expression('SUM(count)')],
    'from' => ['visitdates' => $query],
    'groupBy' => ['visitDate'],
    'orderBy' => ['visitDate' => SORT_ASC]
        ]
);

#$data = $query->asArray()->all();
$data = $sumQuery->all();
?>
<style>

    .line {
        fill: none;
        stroke: steelblue;
        stroke-width: 1.5px;
    }
    .zoom {
        cursor: move;
        fill: none;
        pointer-events: all;
    }

</style>

<svg width="960" height="500"></svg>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script>
    var data = <?= Json::encode($data) ?>;

    for (let i = 0; i < data.length; i++) {
        let from = data[i].visitDate.split("-");
        data[i].Date = new Date(from[0], from[1] - 1, from[2]);
    }


    var svg = d3.select("svg"),
            margin = {top: 20, right: 20, bottom: 110, left: 40},
            margin2 = {top: 430, right: 20, bottom: 30, left: 40},
            width = +svg.attr("width") - margin.left - margin.right,
            height = +svg.attr("height") - margin.top - margin.bottom,
            height2 = +svg.attr("height") - margin2.top - margin2.bottom;

    var parseDate = d3.timeParse("%m/%d/%Y %H:%M");

    var x = d3.scaleTime().range([0, width]),
            x2 = d3.scaleTime().range([0, width]),
            y = d3.scaleLinear().range([height, 0]),
            y2 = d3.scaleLinear().range([height2, 0]);

    var xAxis = d3.axisBottom(x),
            xAxis2 = d3.axisBottom(x2),
            yAxis = d3.axisLeft(y);

    var brush = d3.brushX()
            .extent([[0, 0], [width, height2]])
            .on("brush end", brushed);

    var zoom = d3.zoom()
            .scaleExtent([1, Infinity])
            .translateExtent([[0, 0], [width, height]])
            .extent([[0, 0], [width, height]])
            .on("zoom", zoomed);

    var line = d3.line()
            .x(function (d) {
                return x(d.Date);
            })
            .y(function (d) {
                return y(d.count);
            });

    var line2 = d3.line()
            .x(function (d) {
                return x2(d.Date);
            })
            .y(function (d) {
                return y2(d.count);
            });

    var clip = svg.append("defs").append("svg:clipPath")
            .attr("id", "clip")
            .append("svg:rect")
            .attr("width", width)
            .attr("height", height)
            .attr("x", 0)
            .attr("y", 0);


    var Line_chart = svg.append("g")
            .attr("class", "focus")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
            .attr("clip-path", "url(#clip)");


    var focus = svg.append("g")
            .attr("class", "focus")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var context = svg.append("g")
            .attr("class", "context")
            .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");


    x.domain(d3.extent(data, function (d) {
        return d.Date;
    }));
    y.domain([0, d3.max(data, function (d) {
            return d.count;
        })]);
    x2.domain(x.domain());
    y2.domain(y.domain());


    focus.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

    focus.append("g")
            .attr("class", "axis axis--y")
            .call(yAxis);

    Line_chart.append("path")
            .datum(data)
            .attr("class", "line")
            .attr("d", line);

    context.append("path")
            .datum(data)
            .attr("class", "line")
            .attr("d", line2);


    context.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height2 + ")")
            .call(xAxis2);

    context.append("g")
            .attr("class", "brush")
            .call(brush)
            .call(brush.move, x.range());

    svg.append("rect")
            .attr("class", "zoom")
            .attr("width", width)
            .attr("height", height)
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
            .call(zoom);


    console.log(data);

    function brushed() {
        if (d3.event.sourceEvent && d3.event.sourceEvent.type === "zoom")
            return; // ignore brush-by-zoom
        var s = d3.event.selection || x2.range();
        x.domain(s.map(x2.invert, x2));
        Line_chart.select(".line").attr("d", line);
        focus.select(".axis--x").call(xAxis);
        svg.select(".zoom").call(zoom.transform, d3.zoomIdentity
                .scale(width / (s[1] - s[0]))
                .translate(-s[0], 0));
    }

    function zoomed() {
        if (d3.event.sourceEvent && d3.event.sourceEvent.type === "brush")
            return; // ignore zoom-by-brush
        var t = d3.event.transform;
        x.domain(t.rescaleX(x2).domain());
        Line_chart.select(".line").attr("d", line);
        focus.select(".axis--x").call(xAxis);
        context.select(".brush").call(brush.move, x.range().map(t.invertX, t));
    }

    function type(d) {
        d.Date = parseDate(d.Date);
        d.count = +d.count;
        return d;
    }





</script>
