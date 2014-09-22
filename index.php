<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Compost Denton Map</title>
        <link rel="stylesheet" href="screen.css" />
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
    </head>
    <body>
        <h1>Compost Denton</h1>
        <div id='map'></div>

        <script>
            var map = L.mapbox.map('map', 'obrit.f5bd7c3c')
                .setView([33.2191, -97.1373], 12);

            <?php
            $json_string = file_get_contents("https://compostdenton.com/weight");
            //$json_string = str_replace('ISODate', 'Date', $json_string);
            //$json_string = preg_replace('/(ObjectId\()("[0-9A-Za-z]*")(\))/', '$2', $json_string);
            ?>
            //var json_data = <?php //echo str_replace(array("\n", "\t", " "), '', $json_string); ?>;
            var json_data = <?php echo $json_string; ?>;

            var geoJsonData = {
                type: "FeatureCollection",
                features: []
            };

            for (var i = 0; i < json_data.length; i++) {
                geoJsonData.features.push({
                    type: 'Feature',
                    properties: {
                        count: json_data[i]['totalWeight']
                    },
                    geometry: {
                        type: 'Point',
                        // Can perhaps fudge the exact location in this way?
                        //coordinates: [-97.746014 + Math.random() * 2, 32.052802 + Math.random() * 2]
                        coordinates: json_data[i]['geometry']['coordinates']
                    }
                });
            }

            var geoJson = L.geoJson(geoJsonData, {
                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, {
                        // Here we use the `count` property in GeoJSON verbatim: if it's
                        // to small or two large, we can use basic math in Javascript to
                        // adjust it so that it fits the map better.
                        //radius: feature.properties.count
                    })
                }
            }).addTo(map);


            // TEST GRIDLAYER STUFF
            // L.mapbox.accessToken = 'pk.eyJ1Ijoib2JyaXQiLCJhIjoiMlRUQ0hxQSJ9.--yh59XkySo_ce2g6B7b3g';
            // // Define a map without a Map ID so we
            // // have to add each part of it manually.
            // var map = L.mapbox.map('map', undefined, {
            //     infoControl: true,
            //     attributionControl: false
            // });

            // // The visible tile layer
            // L.mapbox.tileLayer('obrit.f5bd7c3c').addTo(map);

            // // Load interactivity data into the map with a gridLayer
            // var myGridLayer = L.mapbox.gridLayer('obrit.f5bd7c3c').addTo(map);

            // // And use that interactivity to drive a control the user can see.
            // var myGridControl = L.mapbox.gridControl(myGridLayer).addTo(map);

            // // Finally, center the map.
            // map.setView([33.2191, -97.1373], 13);
        </script>
    </body>
</html>
