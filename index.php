<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Mapbox Map</title>
        <link rel="stylesheet" href="screen.css" />
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
    </head>
    <body>
        <h1>Mapbox</h1>
        <div id='map'></div>

        <script>
            var map = L.mapbox.map('map', 'obrit.j3l6pe74')
                .setView([33.22, -97.12], 13);

            <?php
            $json_string = file_get_contents("weight_log.json");
            $json_string = str_replace('ISODate', 'Date', $json_string);
            $json_string = preg_replace('/(ObjectId\()("[0-9A-Za-z]*")(\))/', '$2', $json_string);
            ?>
            var json_data = <?php echo str_replace(array("\n", "\t", " "), '', $json_string); ?>;

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
                        //coordinates: [-97.746014 + Math.random() * 2, 32.052802 + Math.random() * 2]
                        coordinates: json_data[i]['geometry']['coordinates']
                    }
                });
                // for (var key in json_data[i]) {
                //     alert(key)
                // }
            }

            // GeoJSON data: see http://geojson.org/ for the full description of this format.
            //
            // In these lines, we create some random points. This, of course, you can change:
            // instead, your data can be hardcoded as a Javascript object, or pulled in
            // from an external file with AJAX, or loaded from Mapbox automatically.
            // var geoJsonData = {
            //     type: "FeatureCollection",
            //     features: []
            // };
            // for (var i = 0; i < 30; i++) {
            //     geoJsonData.features.push({
            //         type: 'Feature',
            //         properties: {
            //             // The important part is here: that each feature has some property
            //             // that we refer to later on, in `pointToLayer`, that determines
            //             // the size of the scaled circle.
            //             count: Math.random() * 20
            //         },
            //         geometry: {
            //             type: 'Point',
            //             coordinates: [-97.746014 + Math.random() * 2, 32.052802 + Math.random() * 2]
            //         }
            //     });
            // }

            var geoJson = L.geoJson(geoJsonData, {
                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, {
                        // Here we use the `count` property in GeoJSON verbatim: if it's
                        // to small or two large, we can use basic math in Javascript to
                        // adjust it so that it fits the map better.
                        radius: feature.properties.count
                    })
                }
            }).addTo(map);
        </script>
    </body>
</html>
