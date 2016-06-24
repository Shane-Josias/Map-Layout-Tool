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
                    echo '<li id = '.$num.'>'.$c[0].'</li>';
                    $num = $num+1;
                }
                echo '</ul>';
                // echo '<script type= "text/javascript">$("#menu").menu();</script>';
            ?>
        </div>
        <div id='map' class='map pad2'>Map</div>
    <script type= "text/javascript">


        L.mapbox.accessToken = 'pk.eyJ1Ijoic2hhbmVqb3NpYXMiLCJhIjoiY2lwczZwYzVkMDAxN2h0bTJ4M3Fpa3JzZyJ9.oJQvdNDebSyjfrhPOE2xAw';
        var map = L.mapbox.map('map', 'shanejosias.0fm4hn5h');
        
        $("#menu").menu({
            select: function(event, ui) {
                $("#3").addClass("ui-state-enabled");

            }
        });

        // $("#menu-5" ).menu({
        //     create: function( event, ui ) {
        //        var result = $( "#result" );
        //        result.append( "Create event<br>" );
        //     },
        //     blur: function( event, ui ) {
        //        var result = $( "#result" );
        //        result.append( "Blur event<br>" );
        //     },
        //     focus: function( event, ui ) {
        //        var result = $( "#result" );
        //        result.append( "focus event<br>" );
        //     }
        //  });
    
        
    
    </script>
    <?php
    
    ?>
    </body>
</html>