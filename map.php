<!DOCTYPE html>
<html>
<head>
<?php
    require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
    require_once( "./helpers.php" );
?>
    <title><?php echo get_bloginfo('name'); ?> &#8211; Karte</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../cmap-resources/leaflet/leaflet.css" />
    <link rel="stylesheet" href="../cmap-resources/fontawesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../cmap-resources/leaflet.awesome-markers/leaflet.awesome-markers.css" />
    <link rel="stylesheet" href="../cmap-resources/leaflet.EasyButton/easy-button.css" />
    <script src="../cmap-resources/jquery/jquery.min.js"></script>
    <script src="../cmap-resources/leaflet/leaflet.js"></script>
    <script src="../cmap-resources/leaflet.awesome-markers/leaflet.awesome-markers.min.js"></script>
    <script src="../cmap-resources/leaflet.EasyButton/easy-button.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        #map {
            width: 100%;
            height: 100%;
        }
<?php
if (user_is_moderator()) {
?>
        .plain { margin: 0; padding: 0; color: black; }
        .old { margin: 0; padding: 0; color: red; }
        .new { margin: 0; padding: 0; color: green; }
<?php
}
?>
    </style>
</head>
<body>
    <div id='map'></div>

    <script>
        var map = L.map('map');
        map.attributionControl.setPrefix(false);

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                subdomains: 'abc',
                maxZoom: 18,
                attribution: '<a target="_blank" href="https://leafletjs.com">Leaflet</a> | ' +
                             'Kartendaten &copy; <a target="_blank" href="https://www.openstreetmap.org/">OpenStreetMap</a> Beitragende, ' +
                             '<a target="_blank" href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
            }
        ).addTo(map);

        $.getJSON("<?php echo get_bloginfo('wpurl'); ?>/cmap/data.json",function(data){
            var datalayer = L.geoJson(data ,{
                onEachFeature: function(feature, featureLayer) {
                    var text = '<b>'+feature.properties.title+'</b>';
<?php
    if( !user_is_moderator()) {
?>
                    text += '<br/>'+feature.properties.address;
<?php
    } else {
?>
                    text += '<br/>'+feature.properties.delta;

                    text += '<a href="<?php echo get_bloginfo('wpurl'); ?>/cmap/edit?id='+feature.properties.id+'">bearbeiten</a>';
                    if (feature.properties.status == 'pending') {
                        text += '<br><a href="<?php echo get_bloginfo('wpurl'); ?>/cmap/functions?action=publish&id='+feature.properties.id+'">bestätigen</a>';
                    }
<?php
    }
?>
                    featureLayer.bindPopup(text);
                },

                pointToLayer: function(feature, latlng) {
<?php
    if (user_is_moderator()) {
?>
                    switch(feature.properties.status) {
                        case 'unconfirmed':
                            return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: 'envelope', markerColor: 'red', prefix: 'fa', iconColor: 'black'}) });
                            break;
                        case 'pending':
                            return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: 'edit', markerColor: 'orange', prefix: 'fa', iconColor: 'black'}) });
                            break;
                        case 'publish':
                            if( feature.properties.active == 'active' ) {
                                return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'blue', prefix: 'fa', iconColor: 'black'}) });
                            } else {
                                return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: 'ban', markerColor: 'lightgray', prefix: 'fa', iconColor: 'black'}) });
                            }
                            break;
                        default:
                            return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'pink', prefix: 'fa', iconColor: 'black'}) });
                    }
<?php
    } else {
?>
                    return L.marker(latlng, {icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'blue', prefix: 'fa', iconColor: 'black'}) });
<?php
    }
?>
                }
            }).addTo(map);
            map.fitBounds(datalayer.getBounds());
        });

<?php
    if ( user_is_moderator()) {
?>
        L.easyButton( 'fa-list-ul', function(){
            document.location.href = '<?php echo get_bloginfo('wpurl'); ?>/cmap/list'
        }).addTo(map);
<?php
    } else {
?>
        L.easyButton( 'fa-pencil', function(){
            document.location.href = '<?php echo get_bloginfo('wpurl'); ?>/cmap/edit'
        }).addTo(map);
        L.easyButton( 'fa-plus-square', function(){
            document.location.href = '<?php echo get_bloginfo('wpurl'); ?>/cmap/edit?action=new'
        }).addTo(map);
<?php
    }
?>
    </script>
</body>
</html>
