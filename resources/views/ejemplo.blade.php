<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real</title>
    <!-- Incluye la app.js para inicializar Laravel Echo y Pusher -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDBc6VJNT1FJYfUM0B7EMZhxCy2u4_TXtU&callback=initMap" async defer></script>
</head>

<style>
    #map { height: 400px; width: 100%; }
</style>


<body>
<div id="map"></div>


</body>
</html>


<script>
    let map, userMarker, accuracyCircle;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 0, lng: 0 },
            zoom: 2,
            mapTypeId: 'roadmap'
        });

        if (navigator.geolocation) {
            // More aggressive location tracking
            const watchOptions = {
                enableHighAccuracy: true,  // Most important for accuracy
                timeout: 5000,             // 5 second timeout
                maximumAge: 0              // No cached locations
            };

            navigator.geolocation.watchPosition(updateLocation, handleLocationError, watchOptions);
        } else {
            console.error("Geolocation not supported");
        }
    }

    function updateLocation(position) {
        const { latitude: lat, longitude: lng, accuracy, altitude, altitudeAccuracy } = position.coords;
        const location = new google.maps.LatLng(lat, lng);

        // Remove existing markers
        if (userMarker) userMarker.setMap(null);
        if (accuracyCircle) accuracyCircle.setMap(null);

        // Custom marker with more detail
        userMarker = new google.maps.Marker({
            position: location,
            map: map,
            title: `Accuracy: ±${accuracy.toFixed(2)} m`,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 15,
                fillColor: '#4285F4',
                fillOpacity: 0.9,
                strokeColor: 'white',
                strokeWeight: 3
            }
        });

        // More precise accuracy circle
        accuracyCircle = new google.maps.Circle({
            strokeColor: '#4285F4',
            strokeOpacity: 0.9,
            strokeWeight: 3,
            fillColor: '#4285F4',
            fillOpacity: 0.3,
            map: map,
            center: location,
            radius: accuracy
        });

        // Enhanced console logging
        console.log({
            timestamp: new Date().toISOString(),
            coordinates: {
                latitude: lat,
                longitude: lng,
                accuracy: `±${accuracy.toFixed(2)} meters`
            },
            altitude: altitude ? `${altitude.toFixed(2)} m` : 'N/A',
            altitudeAccuracy: altitudeAccuracy ? `±${altitudeAccuracy.toFixed(2)} m` : 'N/A'
        });

        // Dynamic map adjustment
        map.panTo(location);
        map.setZoom(16);  // Closer zoom for more detail
    }

    function handleLocationError(error) {
        console.error("Location Error:", {
            code: error.code,
            message: error.message,
            details: {
                1: "Permission denied",
                2: "Position unavailable",
                3: "Timeout"
            }[error.code]
        });
    }

</script>
