<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Picker</title>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        #map { height: 500px; width: 100%; margin: 10px auto; }
        #location-info { margin-top: 10px; font-size: 16px; color: #d9534f; font-weight: bold; }
        .container { display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .button-container { margin-top: 10px; }
        input { width: 80%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 15px; font-size: 14px; cursor: pointer; background-color: #d9534f; color: white; border: none; border-radius: 5px; }
        button:hover { background-color: #c9302c; }
    </style>
</head>
<body>

<h2>Select Your Location</h2>

<div class="container">
    <input type="text" id="search-box" placeholder="Enter a location">
    <button onclick="searchLocation()">Search</button>
    <div id="map"></div>
    <p id="location-info">Click on the map to select a location.</p>
    <div class="button-container">
        <button onclick="saveLocation()">Save Location</button>
    </div>
</div>

<script>
    var map = L.map('map').setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    var marker = L.marker([20.5937, 78.9629], { draggable: true }).addTo(map);
    
    function updateLocation(lat, lng) {
        document.getElementById("location-info").innerText = `Selected Location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng.lat, e.latlng.lng);
    });
    
    marker.on('dragend', function() {
        var position = marker.getLatLng();
        updateLocation(position.lat, position.lng);
    });

    function saveLocation() {
        if (!marker) return alert("Please select a location.");
        let lat = marker.getLatLng().lat;
        let lng = marker.getLatLng().lng;
        window.opener.postMessage({lat: lat, lng: lng}, window.opener.location.origin);
        window.close();
    }

    function searchLocation() {
        var query = document.getElementById("search-box").value;
        if (!query) return alert("Enter a location to search.");
        
        var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
        
        fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                alert("Location not found.");
                return;
            }
            var lat = parseFloat(data[0].lat);
            var lon = parseFloat(data[0].lon);
            
            map.setView([lat, lon], 12);
            marker.setLatLng([lat, lon]);
            updateLocation(lat, lon);
        })
        .catch(error => console.log("Error:", error));
    }
</script>

</body>
</html>
