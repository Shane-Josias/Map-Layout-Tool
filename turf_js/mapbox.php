<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>A simple map</title>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.4/mapbox.js'></script>
        <script src="jquery-1.12.1.min.js"></script>
        <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.4/mapbox.css' rel='stylesheet' />
        <link href="http://code.jquery.com/ui/1.9.0/themes/cupertino/jquery-ui.css" rel="stylesheet" />
        <script src="./papaparse.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.20.1/mapbox-gl.js'></script>
        <script src="turf.min.js"></script>
        <script src="numeric-1.2.6.min.js"></script>

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
                            var result = $( "#result" ).empty();
                            $( ".ui-selected", this ).each(function() {
                                    var index = $( "#menu li" ).index( this );


                                    // This obtains the relevent properties
                                    var el = document.querySelector('.ui-selected');
                                    // alert(el.dataset.frontage);

                                    var polyline;
                                    var first = 1;
                                    var relative = 0.0002;
                                    var features = map.featureLayer._geojson.features;

                                    map.on('mousemove', function(e) {

                                        var xc = e.latlng.lat;
                                        var yc = e.latlng.lng;
                                        var center = turf.point([yc,xc]);
                                        // console.log(point);
                                        var poly;
                                        var line_points;
                                        for (var i = 0; i < features.length; i++) {

                                            poly = turf.polygon(features[i].geometry.coordinates);

                                            if (turf.inside(center, poly)) {

                                                // console.log(poly);
                                                var ptsArr = []
                                                var val = 0;
                                                for (var j = 0; j < poly.geometry.coordinates[0].length; j++) {
                                                    var one = poly.geometry.coordinates[0][j][0];
                                                    var two = poly.geometry.coordinates[0][j][1];
                                                    // console.log(one);
                                                    ptsArr.push(turf.point([one, two], { id: val}));
                                                    val+=1;
                                                }

                                                // console.log(against);
                                                // var nearest = turf.nearest(point, against);
                                                // console.log(nearest);
                                                pts  = turf.featurecollection(ptsArr);
                                                console.log(pts);
                                                var nearest1 = turf.nearest(center, pts);
                                                console.log(nearest1);
                                                // turf.remove(pts,"id", nearest1.properties.id);
                                                // var nearest2 = turf.nearest(center, pts);
                                                var a = turf.distance(center, nearest1);
                                                var b = turf.distance(center, turf.point(line_points[1]));
                                                var c = turf.distance(nearest1, turf.point(line_points[1]));

                                                var sum = a*a + b*b - c*c;
                                                var frac = sum/(2*a*b);
                                                var theta;
                                                if (center.geometry.coordinates[0] > nearest1.geometry.coordinates[0]) {
                                                    theta = 180 -  Math.acos(frac);
                                                } else {
                                                    theta = Math.acos(frac);
                                                }

                                                // var hyp = turf.distance(center, nearest);

                                                // var r_point = turf.point([nearest.geometry.coordinates[0],center.geometry.coordinates[1]]);
                                                // var adj = turf.distance(center, r_point);
                                                // var theta = Math.acos(adj/hyp);
                                                var diag = Math.cos(theta);
                                                var n_diag = Math.sin(theta);
                                                var rot_mat = [
                                                        [diag, -n_diag],
                                                        [n_diag, diag]
                                                ];
                                                var vec1 = [-4*relative,2*relative];
                                                var vec2 = [0,2*relative];

                                                var vec3 = [0,-2*relative];
                                                var vec4 = [-4*relative,-2*relative];


                                                var prod1 = numeric.dot(rot_mat, vec1);
                                                var prod2 = numeric.dot(rot_mat, vec2);

                                                var prod3 = numeric.dot(rot_mat, vec3);
                                                var prod4 = numeric.dot(rot_mat, vec4);


                                                

                                                console.log("Peter");

                                                // line_points = [
                                                // [xc-4*relative, yc+2*relative],
                                                // [xc, yc+2*relative],
                                                // [xc, yc-2*relative],
                                                // [xc-4*relative, yc-2*relative]
                                                // ];

                                                line_points = [
                                                        [xc+prod1[0], yc+prod1[1]],
                                                        [xc+prod2[0], yc+prod2[1]],
                                                        [xc+prod3[0], yc+prod3[1]],
                                                        [xc+prod4[0], yc+prod4[1]]
                                                ];



                                                break;
                                            } else {
                                                relative = 0.00005;
                                                line_points = [
                                                [xc-4*relative, yc+2*relative],
                                                [xc, yc+2*relative],
                                                [xc, yc-2*relative],
                                                [xc-4*relative, yc-2*relative]
                                                ];
                                            }
                                        }



                                        var polyline_options = {
                                            color: '#404040'
                                        };



                                        if (first == 1) {
                                            polyline = L.polygon(line_points, polyline_options);
                                            map.addLayer(polyline);
                                            first = 0;
                                        } else {
                                            map.removeLayer(polyline)
                                            polyline = L.polygon(line_points, polyline_options);
                                            map.addLayer(polyline);
                                        }   
                                    });

                            });


                        }
            });

            $( "#menu" ).on("selectableselected", function( event, ui ) {} );

        </script>
    </body>
</html>