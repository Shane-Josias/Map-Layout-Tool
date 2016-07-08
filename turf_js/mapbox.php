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
                                    // map.featureLayer._geojson.features = undefined;
                                    // features = undefined;

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
                                            console.log(poly);
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
                                                // ptsArr = []
                                                val = 0;
                                                
                                                var index;
                                                var theta_deg;
                                                var intersection_pts_array = [];
                                                var minimum_index = 0;
                                                var minimum_distance = Number.MAX_VALUE;
                                                for (index=0; index*30 < 360; index++) {

                                                
                                                    // console.log(index);
                                                    // theta_deg = 30*index - 360;
                                                    
                                                    theta_deg = 30*index;
                                                    

                                                    // console.log(bearing_np);

                                                    var theta_rad = (Math.PI*theta_deg)/180;
                                                    var la1 = (Math.PI*yc)/180;
                                                    var lo1 = (Math.PI*xc)/180;
                                                    var d = 0;
                                                    var R = 6371;
                                                    var Ad = d/R;
                                                    
                                                    var la2 = Math.asin(Math.sin(la1)*Math.cos(Ad) + Math.cos(la1)*Math.sin(Ad)*Math.cos(theta_rad));
                                                    var lo2 = lo1 + Math.atan2(Math.sin(theta_rad)*Math.sin(Ad)*Math.cos(la1), Math.cos(Ad)-Math.sin(la1)*Math.sin(la2));
                                                    var lat2 = la2 * (180/Math.PI);
                                                    var lon2 = lo2 * (180/Math.PI);

                                                    // console.log([lon2,lat2]);

                                                    var test_line = turf.linestring([[yc,xc],[lon2,lat2]]);
                                                    // console.log(test_line);
                                                    var intersect = turf.intersect(test_line,poly);
                                                    // console.log(intersect);
                                                    var point_intersection = turf.point(intersect.geometry.coordinates[1]);
                                                    var dist = turf.distance(center, point_intersection);
                                                    intersection_pts_array.push(intersect);

                                                    var point1 = intersection_pts_array[index].geometry.coordinates[0];
                                                    var point2 = intersection_pts_array[index].geometry.coordinates[1];
                                                    console.log(test_line.geometry.coordinates[1]);
                                                     line_points = [
                                                        [test_line.geometry.coordinates[0]]
                                                        // [test_line.geometry.coordinates[1]]
                                                        // [lat2, lon2]
                                                        // [xc-4*relative, yc]
                                                    ];

                                                    polyline = L.polygon(line_points, polyline_options);
                                                    map.addLayer(polyline);

                                                    if (dist < minimum_distance) {
                                                        minimum_index = index;
                                                        minimum_distance = dist;
                                                        // console.log(minimum_distance);
                                                    }
                                                }

                                                // console.log(intersection_pts_array);

                                                // console.log(minimum_index);


                                                 // var test_line = turf.linestring([line_points2[0], line_points2[1], line_points2[0]]);
                                                 // console.log(test_line);
                                                 // var intersect = turf.intersect(test_line,poly);
                                                 // console.log(intersect);
                                                 // console.log('----------');
                                                // bearing calculations

                                               

                                                // var bearing_np_radians = (Math.PI*bearing_np)/180;
                                                // var la1 = (Math.PI*p1.geometry.coordinates[1])/180;
                                                // var lo1 = (Math.PI*p1.geometry.coordinates[0])/180;
                                                // var d = frontage_distance;
                                                // var R = 6371;
                                                // var Ad = d/R;
                                                
                                                // var la2 = Math.asin(Math.sin(la1)*Math.cos(Ad) + Math.cos(la1)*Math.sin(Ad)*Math.cos(bearing_np_radians));
                                                // var lo2 = lo1 + Math.atan2(Math.sin(bearing_np_radians)*Math.sin(Ad)*Math.cos(la1), Math.cos(Ad)-Math.sin(la1)*Math.sin(la2));
                                                // var lat2 = la2 * (180/Math.PI);
                                                // var lon2 = lo2 * (180/Math.PI);

                                                // console.log(intersection_pts_array);


                                                var point1 = intersection_pts_array[minimum_index].geometry.coordinates[0];
                                                var point2 = intersection_pts_array[minimum_index].geometry.coordinates[1];

                                                // console.log(point1);

                                                // line_points = [
                                                //     [point1[1],point1[0] ], 
                                                //     [point2[1],point2[0]],
                                                //     // [lat2, lon2]
                                                //     // [xc-4*relative, yc]
                                                // ];
                                                // line_points2 = [
                                                //     [point.geometry.coordinates[0], point.geometry.coordinates[1]  ], 
                                                //     // [p2.geometry.coordinates[0],p2.geometry.coordinates[1] ],
                                                //     // [lon2, lat2]
                                                //     // [xc-4*relative, yc]
                                                // ];
                                                // line_points = [
                                                //     [xc, yc-2*relative],
                                                //     [xc, yc + 2*relative]
                                                //     // [xc + 4*relative, yc+4*relative]
                                                //     // [xc-4*relative, yc]
                                                // ];
                                                // line_points2 = [
                                                //     [yc-2*relative, xc],
                                                //     [yc+2*relative, xc ]
                                                //     // [yc + 4*relative, xc + 4*relative]
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