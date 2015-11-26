
{{ content() }}

 <div id="map" style="width: 100%; height: 400px;"></div>

<script>

	var map;
	function initMap() {
		var locations = {{ locations }};
		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 10,
			center: new google.maps.LatLng({{ center_location }}),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			streetViewControl:false,
			mapTypeControl:false,

		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;

		for (i = 0; i < locations.length; i++) {
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				map: map
			});

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					  infowindow.setContent(locations[i][0]);
					  infowindow.open(map, marker);
				}
			})(marker, i));
		}
	}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCbpgwGloxKIg7NLRMoTDMqIY-vP2DP_fg&callback=initMap" async defer></script>
