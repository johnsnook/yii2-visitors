/*
 * @author John Snook
 * @date Aug 30, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Displays a ploty.js map of visitor info
 */


var visitors = $.ajax({
    url: "/exploration/geojson",
    dataType: "json",
    success: console.log("Visitors geojson data successfully loaded."),
    error: function (xhr) {
        alert(xhr.statusText);
    }
});

// Specify that this code should run once the county data request is complete
$.when(visitors).done(function () {
    var map = L.map('map').setView([37.0294878, -98.2801771], 3);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.outdoors',
        accessToken: 'pk.eyJ1IjoianNub29rIiwiYSI6ImNqYXRyN3JobzU3YXAzM25xemk5MjR1bWkifQ.k2z2shOImL7klX5g5sqn7Q'
    }).addTo(map);

    L.geoJSON(visitors.responseJSON, {
        style: function (feature) {
            return feature.properties && feature.properties.style;
        },
        onEachFeature: onEachFeature,
        pointToLayer: function (feature, latlng) {
            let mark = L.circleMarker(latlng, {
                radius: (feature.properties.visits * 0.013) + 7,
                fillColor: getRandomColor(), //"#ff7800",
                color: "#000",
                weight: 1,
                opacity: 1,
                info: feature.properties.whoAndWhere + '<br>'
                        + 'Visits: ' + feature.properties.visits,
                fillOpacity: 0.4
            });
            mark.bindTooltip(mark.options.info);
            mark.on('click', function (e) {
                $('#pumpme').html(e.target.options.info);
            });
            return mark;
        }
    }).addTo(map);

});


function onEachFeature(feature, layer) {
//    var popupContent = feature.properties.whoAndWhere + '<br>'
//            + 'Visits: ' + feature.properties.visits;
//    layer.bindPopup(popupContent);
}


function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

