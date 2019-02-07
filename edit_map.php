<!DOCTYPE html>
<html>
<head>
<?php
    require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
?>

    <title><?php echo get_bloginfo('name'); ?> &#8211; Karteneintrag anpassen</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../cmap-resources/leaflet/leaflet.css" />
    <script src="../cmap-resources/leaflet/leaflet.js"></script> 
    <script src="../cmap-resources/jquery/jquery-3.3.1.min.js"></script>

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		#map {
			width: 100%;
			height: 100%;
		}
	</style>	
</head>
<body>
    <div id='map'></div>

    <script>
        var curLocation = [<? echo $_GET['lat']; ?>,<? echo $_GET['lon']; ?>];

        var map = L.map('map').setView(curLocation, 18);
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

        var marker = new L.marker(curLocation, {
            draggable: 'true'
        }).addTo(map);

        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            marker.setLatLng(position, {
                draggable: 'true'
            }).update();
            window.top.postMessage(position, '*');
        });
    </script>

</body>
</html>
