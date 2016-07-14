<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>A simple map</title>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
        <script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
        <script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-image/v0.0.4/leaflet-image.js'></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="http://www.jqueryscript.net/demo/jQuery-Plugin-To-Print-Any-Part-Of-Your-Page-Print/jQuery.print.js"></script>
        <link rel="stylesheet" href="leaflet.print.css"/>
        <script src="leaflet.print.js"></script>
        <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-label/v0.2.1/leaflet.label.js'></script>
        <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-label/v0.2.1/leaflet.label.css' rel='stylesheet' />
        <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-draw/v0.2.3/leaflet.draw.css' rel='stylesheet' />
        <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-draw/v0.2.3/leaflet.draw.js'></script>
        <!-- <script type="text/javascript" src="localhost/turf_js/info.json?var=printConfig"></script> -->
        <!-- <script src="http://apps2.geosmart.co.nz/mapfish-print/pdf/info.json?var=printConfig"></script> -->
            <!-- <link rel="stylesheet" href="dist/easyPrint.css"/> -->
    <!-- <script src="leaflet.easyPrint.js"></script> -->
        <link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
        <!-- <script src="jquery-1.12.1.min.js"></script> -->
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

        <div id='side' class='sidebar pad2'>Campers

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
            <button id="printBtn">Save map</button>
        </div>
        <div id='map' class='map pad2'>Map</div>
        <script type= "text/javascript">


            L.mapbox.accessToken = 'pk.eyJ1Ijoic2hhbmVqb3NpYXMiLCJhIjoiY2lwczZwYzVkMDAxN2h0bTJ4M3Fpa3JzZyJ9.oJQvdNDebSyjfrhPOE2xAw';
            var map = L.mapbox.map('map', 'shanejosias.0fm4hn5h');
            var remove = true;
            var featureLayer = map.featureLayer._geojson;
            var featureGroup = L.featureGroup().addTo(map);
            var line_points;
            var polyline_options;
            var featuresList = [];
            console.log();
            var geo;
            var first2 = 0;
            var first = 1;

            function onMouseMove(e) {
                var xc = e.latlng.lat;
                var yc = e.latlng.lng;
                var center = turf.point([yc,xc]);
                // console.log(yc );
                var features = map.featureLayer._geojson.features;
                var poly;
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
                        // console.log(point2[]);
                        line_points = [];

                        for (var k = 0; k < point2.length; k++ ){
                            line_points.push([point2[k][1], point2[k][0]]);

                        }
                        var line_start = turf.point([point2[0][0], point2[0][1] ]);
                        var line_end =  turf.point([point2[point2.length-1][0], point2[point2.length-1][1] ]);
                        var angle = turf.bearing(line_start,line_end);
                        var angle2 = angle+90;

                        var c2 = turf.destination(line_start,0.03,angle2,'kilometers');
                        var c3 = turf.destination(line_end,0.03,angle2,'kilometers');
                        if (!turf.inside(c2,poly)) {
                            c2 = turf.pointOnLine(bigLine, c2);
                        } 
                        if(!turf.inside(c3,poly)) {
                            c3= turf.pointOnLine(bigLine, c3);

                        }
                        // var featureGroupLayers = featureGroup._layers;
                        // if (!$.isEmptyObject(featureGroupLayers)) {

                        //     for (var e in featureGroupLayers) {
                        //         // console.log(featureGroupLayers[e]._latlngs);
                        //         var array_points = []

                        //         for (var k = 0; k < featureGroupLayers[e]._latlngs.length; k++ ) {
                        //             array_points.push([featureGroupLayers[e]._latlngs[k].lng,featureGroupLayers[e]._latlngs[k].lat ]);
                        //         }
                        //         // console.log(array_points);
                        //         array_points.push([featureGroupLayers[e]._latlngs[0].lng,featureGroupLayers[e]._latlngs[0].lat ]);

                        //         var poly_feat = turf.polygon(array_points);
                        //         var line_feat = turf.linestring(array_points);
                        //         console.log(c2.geometry.coordinates);
                        //         console.log(poly_feat.geometry.coordinates[poly_feat.geometry.coordinates.length-1]);
                        //         console.log('adsljfnsdlfnalsdjfnalkjfnad'); 
                        //             // c2 = turf.pointOnLine(line_feat, c2);
                        //             c3 = turf.pointOnLine(line_feat, c3);

                        //         if (turf.inside(c2,poly_feat)) {
                                     
                        //         } 
                        //         if(turf.inside(c3,poly_feat)) {
                        //             break;

                        //         }

                        //     }

                        // }


                        line_points.push([c3.geometry.coordinates[1], c3.geometry.coordinates[0]]);

                        line_points.push([c2.geometry.coordinates[1], c2.geometry.coordinates[0]]);
                        line_points.push([c2.geometry.coordinates[1], c2.geometry.coordinates[0]]);
                        line_points.push([point2[0][1], point2[0][0]]);










                        // console.log(line_points);
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



                polyline_options = {
                    color: '#404040',
                    opacity : 1
                    // allowIntersection : false
                };



                if (first == 1) {
                    polyline = L.polyline(line_points, polyline_options);
                    map.addLayer(polyline);
                    first = 0;
                } else {

                    if (remove) {
                        map.removeLayer(polyline);
                        polyline = L.polyline(line_points, polyline_options);
                        map.addLayer(polyline);
                    } else {
                        map.removeLayer(polyline);

                        polyline = L.polygon(line_points, polyline_options).bindLabel('nice', { noHide: true }).addTo(featureGroup);
                        // map.showLabel();
                        var divIcon = L.divIcon({ 
                          html: "Name \n Requested frontage"
                        })
                        L.marker(polyline.getBounds().getCenter(), {icon: divIcon }).addTo(featureGroup);

                         

                        // map.showLabel(label);
                        // console.log(featureGroup._layers);
                        $('#menu .ui-selected').removeClass('ui-selected','ui-selecting');
                        $('#menu').trigger('unselected');
                        // map.off('mouseover');
                        // map.off('mouseover', function() {alert('hello')});
                        map.off('mousemove',onMouseMove);
                        first = 1;
                        remove = true;
                        // console.log(map);
                    }
                    
                } 
            }
            $( "#menu" ).selectable({
                selected: function( event, ui ) {
                            $( ".ui-selected", this ).each(function() {
                                    var index = $( "#menu li" ).index( this );


                                    // This obtains the relevent properties
                                    var el = document.querySelector('.ui-selected');
                                    // alert(el.dataset.frontage);

                                    var polyline;
                                    // var first = 1;
                                    var relative = 0.0002;
                                    var features = map.featureLayer._geojson.features;
                                    console.log(map.featureLayer._geojson.features);
                                    // map.featureLayer._geojson.features = undefined;
                                    // features = undefined;
                                    map.on('click', function(e) {
                                        // polyline.addTo(featureGroup);
                                        // el.attrib();
                                        remove = false;
                                        // featureGroup = L.featureGroup([featuresList]).addTo(map);
                                       // console.log(added_features);
                                       console.log('clicked');

                                        


                                    });
                                    map.on('mousemove', onMouseMove);


                            });


                        },
                unselected: function( event, ui ) {alert('here');}
            });

            var drawControl = new L.Control.Draw({
                edit: {
                  featureGroup: featureGroup
                }
            }).addTo(map);
            map.on('draw:created', function(e) {
                featureGroup.addLayer(e.layer);
            });

            // $( "#menu" ).on("selectableselected", function( event, ui ) {} );
            $("#printBtn").click(function(){
              $('#map').print();
            });
            // L.easyPrint({
            //     title: 'My awesome print button',
            //     elementsToHide: '#side, ul, li, #printBtn'
            // }).addTo(map)

            //  var printConfig = {
            //     "scales":[
            //         {"name":"25000"},
            //         {"name":"50000"},
            //         {"name":"100000"}
            //     ],
            //     "dpis":[
            //         {"name":"190"},
            //         {"name":"254"}
            //     ],
            //     "outputFormats":[
            //         {"name":"pdf"},
            //         {"name":"png"}
            //     ],
            //     "layouts":[
            //         {
            //             "name":"A4 portrait",
            //             "map":{
            //                 "width":440,
            //                 "height":483
            //             }
            //         }
            //     ],
            //     "printURL":"http:\/\/localhost\/turf_js\/print.pdf",
            //     "createURL":"http:\/\/localhost\/turf_js\/create.json"
            // }
      
            //  printProvider = L.print.provider({
            //       capabilities: printConfig,
            //       method: 'GET',
            //       dpi: 190,
            //       outputFormat: 'pdf',
            //       customParams: {
            //           mapTitle: 'Print Test',
            //           comment: 'Testing Leaflet printing'
            //       }
            //   });
            // // Create a print control with the configured provider and add to the map
            //   printControl = L.control.print({
            //       provider: printProvider
            //   });
            //   map.addControl(printControl);
            

        </script>
    </body>
</html>