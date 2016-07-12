<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>A simple map</title>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
        <script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-image/v0.0.4/leaflet-image.js'></script>

        <link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
        <script src="jquery-1.12.1.min.js"></script>
        <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <link href="http://code.jquery.com/ui/1.9.0/themes/cupertino/jquery-ui.css" rel="stylesheet" />
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="turf.min.js"></script>
        <script src="numeric-1.2.6.min.js"></script>
        <script src="leaflet.geometryutil.js"></script>

        <style>
            body {
                background:#404040;
                color:#f8f8f8;
                font:500 20px/26px 'Helvetica Neue', Helvetica, Arial, Sans-serif;
                margin:0;
                padding:0;
                -webkit-font-smoothing:antialiased;
            }
            /**
            * The page is split between map and sidebar - the sidebar gets 1/5, map
            * gets 4/5 of the page. You can adjust this to your personal liking.
            */
            .sidebar {
                width:20%;
            }
            .map {
                border-left:1px solid #fff;
                position:absolute;
                left:20%;
                width:80%;
                top:0;
                bottom:0;
            }
            .pad2 {
                padding:20px;
                -webkit-box-sizing:border-box;
                -moz-box-sizing:border-box;
                box-sizing:border-box;
            }
            #menu {
                list-style-type: none;
            }

            #menu .ui-selecting { background: #404040; }
            #menu .ui-selected { background: #0000FF; color: white; }
        </style>
    </head>

    <body>

        <div id class='sidebar pad2'>Campers

            <?php
                $file = fopen("campers.csv","r");
                $campers = array();

                while(!feof($file)) {
                    $row = fgetcsv($file);
                    array_push($campers, $row);
                }

                fclose($file);
                echo '<ul id="menu">';
                $num = 0;
                foreach ($campers as $c) {
                    echo '<li data-frontage = "'.$c[1].'" data-depth="'.$c[2].'">'.$c[0].'</li>';
                    $num = $num+1;
                }
                echo '</ul>';

            ?>
        </div>
        <div id='map' class='map pad2'>Map</div>
        <script type= "text/javascript">


            L.mapbox.accessToken = 'pk.eyJ1Ijoic2hhbmVqb3NpYXMiLCJhIjoiY2lwczZwYzVkMDAxN2h0bTJ4M3Fpa3JzZyJ9.oJQvdNDebSyjfrhPOE2xAw';
            var map = L.mapbox.map('map', 'shanejosias.0fm4hn5h');
            

            $( "#menu" ).selectable({
                selected: function( event, ui ) {
                            $( ".ui-selected", this ).each(function() {
                                    var index = $( "#menu li" ).index( this );


                                    // This obtains the relevent properties
                                    var el = document.querySelector('.ui-selected');
                                    // alert(el.dataset.frontage);

                                    var polyline;
                                    var first = 1;
                                    var relative = 0.0002;
                                    var features = map.featureLayer._geojson.features;
                                    // map.featureLayer._geojson.features = undefined;
                                    // features = undefined;

                                    map.on('mousemove', function(e) {

                                        var xc = e.latlng.lat;
                                        var yc = e.latlng.lng;
                                        var center = turf.point([yc,xc]);
                                        // console.log(yc );
                                        var poly;
                                        var line_points;
                                        var line_points2;

                                        var pts;
                                        for (var i = 0; i < features.length; i++) {

                                            poly = turf.polygon(features[i].geometry.coordinates);
                                            // console.log(poly);
                                            if (turf.inside(center, poly)) {

                                                var points_arr = []
                                                points_arr.push(poly.geometry.coordinates[0][0]);
                                                for (var j = 1; j < poly.geometry.coordinates[0].length-1; j++) {
                                                    one1 = poly.geometry.coordinates[0][j-1][0];
                                                    one2 = poly.geometry.coordinates[0][j-1][1];
                                                    
                                                    

                                                    points_arr.push([one1, one2]);
                                                    
                                                }
                                                points_arr.push(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length-2]);
                                                points_arr.push(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length-1]);


                                                var bigLine = turf.linestring(points_arr);
                                                // console.log(bigLine);

                                                

                                                

                                                var snapped = turf.pointOnLine(bigLine, center);
                                                // var point1 = intersection_pts_array[minimum_index].geometry.coordinates;
                                                var point1 = snapped.geometry.coordinates
                                                // console.log(snapped2);
                                                var bPoint = turf.point(bigLine.geometry.coordinates[0]);
                                                var slice = turf.lineSlice(bPoint, snapped, bigLine);
                                                var first_dist = turf.lineDistance(slice);

                                                var p2 = turf.along(bigLine, (first_dist+0.03), 'kilometers');                                                
                                                // var point2 = p2.geometry.coordinates;
                                                var slice2 = turf.lineSlice(snapped, p2, bigLine);
                                                // console.log(slice2);
                                                var point2 = slice2.geometry.coordinates;
                                                console.log(point2);
                                                line_points = [];
                                                for (var k = 0; k < point2.length; k++ ){
                                                    line_points.push([point2[k][1], point2[k][0]]);

                                                }

                                                console.log(line_points);
                                                // line_points = [
                                                //     [point1[1],point1[0]], 
                                                //     [point2[1],point2[0]]
                                                //     // [lat2, lon2]
                                                //     // [xc-4*relative, yc]
                                                // ];
                                                
                                                
                                                break;
                                            } else {
                                                relative = 0.00005;
                                                
                                                line_points = [
                                                    [xc, yc-2*relative],
                                                    [xc, yc + 2*relative]
                                                    // [xc + 4*relative, yc+4*relative]
                                                    // [xc-4*relative, yc]
                                                ];
                                                line_points2 = [
                                                    [yc-2*relative, xc],
                                                    [yc+2*relative, xc ]
                                                    // [yc + 4*relative, xc + 4*relative]
                                                    // [xc-4*relative, yc]
                                                ];
                                            }
                                        }



                                        var polyline_options = {
                                            color: '#404040',
                                            opacity : 1,
                                            // weight : 20
                                        };



                                        if (first == 1) {
                                            polyline = L.polyline(line_points, polyline_options);
                                            map.addLayer(polyline);
                                            first = 0;
                                        } else {
                                            map.removeLayer(polyline);
                                            polyline = L.polyline(line_points, polyline_options);
                                            map.addLayer(polyline);
                                        }   
                                    });

                            });


                        }
            });

            $( "#menu" ).on("selectableselected", function( event, ui ) {} );
            leafletImage(map, function(err, canvas) {
                // now you have canvas
                // example thing to do with that canvas:
                var img = document.createElement('img');
                var dimensions = map.getSize();
                img.width = dimensions.x;
                img.height = dimensions.y;
                img.src = canvas.toDataURL();
                document.getElementById('images').innerHTML = '';
                document.getElementById('images').appendChild(img);
            });

        </script>
    </body>
</html>