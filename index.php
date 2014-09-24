<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Compost Denton Map</title>
        <link rel="stylesheet" href="screen.css" />
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
        <!-- MapBox -->
        <script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css"> -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <!-- <div class="row">
                <div class="col-md-12">
                    <h1>Compost Denton</h1>
                </div>
            </div> -->

            <br/>

            <div class="row" style="height: 90%;">
                <div class="col-md-12" style="height: 100%;">
                    <div id='map'>&nbsp;</div>
                </div>
            </div>

            <br/>

            <div id="scale" class="row scale">
                <div class="col-md-2 text-center">
                    <h1 style="margin-top: 0px;">Compost Denton</h1>
                </div>
                <div id="scale2" class="col-md-2 text-center scale">
                    <div class="circle"></div>
                    &lt; <span class="min"></span> lbs
                </div>
                <div id="scale3" class="col-md-2 text-center scale">
                    <div class="circle"></div>
                    <span class="min"></span> - <span class="max"></span> lbs
                </div>
                <div id="scale4" class="col-md-2 text-center scale">
                    <div class="circle"></div>
                    <span class="min"></span> - <span class="max"></span> lbs
                </div>
                <div id="scale5" class="col-md-2 text-center scale">
                    <div class="circle"></div>
                    <span class="min"></span> - <span class="max"></span> lbs
                </div>
                <div id="scale6" class="col-md-2 text-center scale">
                    <div class="circle"></div>
                    &gt; <span class="max"></span> lbs
                </div>
            </div>
        </div>
        <br/>

        <script>
            var map = L.mapbox.map('map', 'obrit.j3l6pe74', {
                maxZoom: 13,
                minZoom: 11
            })
                .setView([33.2191, -97.1373], 11);

            <?php
            //@TODO: Legend/scale
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

            var minCount_A = 1000000000;
            var maxCount_B = 0;
            var sizeMin_a = 2;
            var sizeMax_b = 6;
            for (var i = 0; i < json_data.length; i++) {
                var totalWeight = json_data[i]['totalWeight'];
                if (totalWeight <= 0) {
                    continue;
                }
                if (totalWeight > maxCount_B) {
                    maxCount_B = totalWeight;
                }
                if (totalWeight < minCount_A) {
                    minCount_A = totalWeight;
                }
            }

            var minmax = [[], [], [1000000000, 0], [1000000000, 0], [1000000000, 0], [1000000000, 0], [1000000000, 0]];
            for (var i = 0; i < json_data.length; i++) {
                //a + (x - A)(b - a) / (B - A)
                var totalWeight = json_data[i]['totalWeight'];
                if (totalWeight <= 0) {
                    continue;
                }
                var scaledWeight = Math.round(sizeMin_a + (((totalWeight - minCount_A) * (sizeMax_b - sizeMin_a)) / (maxCount_B - minCount_A)));
                if (totalWeight > minmax[scaledWeight][1]) { minmax[scaledWeight][1] = totalWeight; }
                if (totalWeight < minmax[scaledWeight][0]) { minmax[scaledWeight][0] = totalWeight; }

                geoJsonData.features.push({
                    type: 'Feature',
                    properties: {
                        count: totalWeight,
                        scaled: scaledWeight
                    },
                    geometry: {
                        type: 'Point',
                        // Can perhaps fudge the exact location in this way?
                        //coordinates: [-97.746014 + Math.random() * 2, 32.052802 + Math.random() * 2]
                        coordinates: [json_data[i]['geometry']['coordinates'][0] + Math.random() / 500, json_data[i]['geometry']['coordinates'][1] + Math.random() / 500]
                    }
                });
                //console.log(Math.random() / 1000);
                //console.log(geoJsonData.features[i].geometry.coordinates);
                // console.log('count: ' + geoJsonData.features[i].properties.count);
                // console.log('scale: ' + geoJsonData.features[i].properties.scaled);
                // console.log('\n');
            }
            console.log(minmax);
            for (var i = 2; i < minmax.length; i++) {
                //$('div#scale' + i + ' span.min').text(minmax[i][0]);
                //$('div#scale' + i + ' span.max').text(minmax[i][1]);
                var j = i;
                if (i == 2) { j++; }
                if (i == 6) { j--; }
                $('div#scale' + i + ' span.min').text(Math.floor(minmax[j][0]));
                $('div#scale' + i + ' span.max').text(Math.ceil(minmax[j+1][0]-1));
                //style="width: 72px; height: 72px; border-radius: 36px; -moz-border-radius: 36px; -webkit-border-radius: 36px;"
                var radius = Math.pow(i, 2);
                var diameter = radius * 2;
                $('div#scale' + i + ' div.circle').css('width', diameter + 'px');
                $('div#scale' + i + ' div.circle').css('height', diameter + 'px');
                $('div#scale' + i + ' div.circle').css('border-radius', radius + 'px');
                $('div#scale' + i + ' div.circle').css('-moz-border-radius', radius + 'px');
                $('div#scale' + i + ' div.circle').css('-webkit-border-radius', radius + 'px');
            }

            var geoJson = L.geoJson(geoJsonData, {
                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, {
                        // Here we use the `count` property in GeoJSON verbatim: if it's
                        // too small or too large, we can use basic math in Javascript to
                        // adjust it so that it fits the map better.
                        radius: Math.pow(feature.properties.scaled, 2),
                        //radius: feature.properties.scaled,
                        color: '#4F6F2D',
                        clickable: false
                    })
                        //.bindPopup('Count: ' + feature.properties.count)
                }
            }).addTo(map);
            //console.log(geoJson);
        </script>
    </body>
</html>
