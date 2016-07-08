<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>A simple map</title>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
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

                                    map.on('mousemove', function(e) {

                                        var xc = e.latlng.lat;
                                        var yc = e.latlng.lng;
                                        var center = turf.point([yc,xc]);
                                        // console.log(e );
                                        var poly;
                                        var line_points;
                                        var line_points2;

                                        var pts;
                                        for (var i = 0; i < features.length; i++) {

                                            poly = turf.polygon(features[i].geometry.coordinates);
                                            if (turf.inside(center, poly)) {

                                                var ptsArr = []
                                                var val = 0;
                                                for (var j = 0; j < poly.geometry.coordinates[0].length; j++) {
                                                    var one = poly.geometry.coordinates[0][j][0];
                                                    var two = poly.geometry.coordinates[0][j][1];
                                                    // console.log(one);
                                                    ptsArr.push(turf.point([one, two], { id: val}));
                                                    val+=1;
                                                }

                                                pts  = turf.featurecollection(ptsArr);
                                                var nearest1 = turf.nearest(center, pts);

                                                var before;
                                                var after;
                                                ptsArr = []
                                                val = 0;
                                                for (var j = 0; j < poly.geometry.coordinates[0].length; j++) {
                                                    // Potential out of bounds here!!!!!
                                                    if (val == nearest1.properties.id) {
                                                         console.log(val);

                                                        if (val == 0) {
                                                            before = turf.point(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length - 1]);
                                                        } else {
                                                           before = turf.point(poly.geometry.coordinates[0][val-1]); 
                                                        }
                                                        
                                                        if (val == poly.geometry.coordinates[0].length - 1) {
                                                             after = turf.point(poly.geometry.coordinates[0][0]);
                                                        } else {
                                                             after = turf.point(poly.geometry.coordinates[0][val+1])
                                                        }
                                                        
                                                        break;
                                                    }
                                                    val+=1;
                                                }

                                                // consorf.featurecollection(ptsArr);

                                                var d1 = turf.distance(nearest1, before);
                                                var d2 = turf.distance(nearest1, after);
                                                var nearest2;
                                                // bearing calculations
                                                var bearing_nearest = turf.bearing(center, nearest1);
                                                var bearing_before = turf.bearing(center, before);
                                                var bearing_after = turf.bearing(center, after);
                                                var bearing_np = turf.bearing(center, turf.point(line_points2[1]));

                                                // top line
                                                if ((bearing_nearest >= -180) && (bearing_nearest <=0) && (bearing_after <= 180) && (bearing_after >= 0)) {
                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(after.geometry.coordinates[0],after.geometry.coordinates[1])]);
                                                        nearest2 = after;
                                                        bearing_np += 90;

                                                } else if ((bearing_nearest >= 0) && (bearing_nearest <= 180) && (bearing_before > -180) && (bearing_before <0)) {
                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(before.geometry.coordinates[0],before.geometry.coordinates[1])]);
                                                        nearest2 = before;
                                                        bearing_np += 90;

                                                }

                                                // bottom line
                                                if ((bearing_nearest >= 0) && (bearing_nearest <=180) && (bearing_after <= 0) && (bearing_after >= -180)) {
                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(after.geometry.coordinates[0],after.geometry.coordinates[1])]);
                                                        nearest2 = after;
                                                        bearing_np -= 90;


                                                } else if ((bearing_nearest <= 0) && (bearing_nearest >= -180) && (bearing_before > 0) && (bearing_before <180)) {
                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(before.geometry.coordinates[0],before.geometry.coordinates[1])]);
                                                        nearest2 = before;
                                                        bearing_np -= 90;

                                                }

                                                // if its to the right corresponds to all bearings negative HERE can be null pointer exceptions too
                                                if (bearing_nearest < 0 && bearing_before < 0 && bearing_after < 0) {
                                                    if (bearing_nearest > -90 ) {
                                                        nearest1 = after;
                                                        //we actually changed after
                                                        if ( val == poly.geometry.coordinates[0].length - 1) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][1]);
                                                        } else if ( val == poly.geometry.coordinates[0].length - 2) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][0]);
                                                        } else {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][val + 2]);
                                                        }
                                                    } else {
                                                        nearest1 = before;
                                                        // we actually changed before
                                                        if (val == 0) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length - 2]);
                                                        } else if (val == 1) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length - 1]);
                                                        } else {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][val - 2]);

                                                        }
                                                    }
                                                    
                                                    bearing_np += 90;

                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(nearest2.geometry.coordinates[0],nearest2.geometry.coordinates[1])]);

                                                } else if (bearing_nearest > 0 && bearing_before > 0 && bearing_after > 0) {

                                                    if (bearing_nearest > 90 ) {
                                                        nearest1 = before;
                                                        // we actually changed before
                                                        if (val == 0) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length - 2]);
                                                        } else if (val == 1) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][poly.geometry.coordinates[0].length - 1]);
                                                        } else {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][val - 2]);
                                                        }

                                                    } else {
                                                        nearest1 = after;
                                                        // we actually changed after
                                                        if ( val == poly.geometry.coordinates[0].length - 1) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][1]);
                                                        } else if ( val == poly.geometry.coordinates[0].length - 2) {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][0]);
                                                        } else {
                                                            nearest2 = turf.point(poly.geometry.coordinates[0][val + 2]);
                                                        }
                                                    }
                                                    bearing_np -= 90;

                                                    line_snappee = turf.linestring([new Array(nearest1.geometry.coordinates[0],nearest1.geometry.coordinates[1]), new Array(nearest2.geometry.coordinates[0],nearest2.geometry.coordinates[1])]);

                                                }


                                               

                                                var a = turf.distance(nearest1, center);
                                                var b = turf.distance(nearest1, nearest2);
                                                var c = turf.distance(center, nearest2);
                                                var frontage_distance = turf.distance(center, turf.point(line_points2[1]));
                                                console.log('a ' + a);
                                                console.log('b ' + b);

                                                console.log('c ' + c);
                                                console.log('frontage ' +frontage_distance);



                                                var numerator = a*a + b*b - c*c;
                                                var cos_theta = numerator/(2*a*b);
                                                var proj_distance = a*cos_theta;
                                                console.log('proj distance ' + proj_distance);
                                                var p1 = turf.along(line_snappee, Math.abs(proj_distance), 'kilometers');
                                                var p2 = turf.along(line_snappee,Math.abs(proj_distance) + Math.abs(frontage_distance), 'kilometers');

                    

                                                

                                               

                                                var bearing_np_radians = (Math.PI*bearing_np)/180;
                                                var la1 = (Math.PI*p1.geometry.coordinates[1])/180;
                                                var lo1 = (Math.PI*p1.geometry.coordinates[0])/180;
                                                var d = frontage_distance;
                                                var R = 6371;
                                                var Ad = d/R;
                                                
                                                var la2 = Math.asin(Math.sin(la1)*Math.cos(Ad) + Math.cos(la1)*Math.sin(Ad)*Math.cos(bearing_np_radians));
                                                var lo2 = lo1 + Math.atan2(Math.sin(bearing_np_radians)*Math.sin(Ad)*Math.cos(la1), Math.cos(Ad)-Math.sin(la1)*Math.sin(la2));
                                                var lat2 = la2 * (180/Math.PI);
                                                var lon2 = lo2 * (180/Math.PI);






                                                line_points = [
                                                    [p1.geometry.coordinates[1],p1.geometry.coordinates[0] ], 
                                                    [p2.geometry.coordinates[1],p2.geometry.coordinates[0]],
                                                    [lat2, lon2]
                                                    // [xc-4*relative, yc]
                                                ];
                                                line_points2 = [
                                                    [p1.geometry.coordinates[0],p1.geometry.coordinates[1] ], 
                                                    [p2.geometry.coordinates[0],p2.geometry.coordinates[1] ],
                                                    [lon2, lat2]
                                                    // [xc-4*relative, yc]
                                                ];


                                                
                                                break;
                                            } else {
                                                relative = 0.0005;
                                                
                                                line_points = [
                                                    [xc, yc],
                                                    [xc, yc + 4*relative]
                                                    // [xc + 4*relative, yc+4*relative]
                                                    // [xc-4*relative, yc]
                                                ];
                                                line_points2 = [
                                                    [yc, xc],
                                                    [yc + 4*relative, xc ]
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
                                            polyline = L.polygon(line_points, polyline_options);
                                            map.addLayer(polyline);
                                            first = 0;
                                        } else {
                                            map.removeLayer(polyline);
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