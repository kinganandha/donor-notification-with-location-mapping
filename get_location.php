<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "person_db";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$blood_group_filter = "";
$donors = [];

if (isset($_GET['blood_group']) && !empty($_GET['blood_group'])) {
    $blood_group_filter = $_GET['blood_group'];
    $sql = "SELECT fullname, latitude, longitude, blood_group FROM donors WHERE blood_group = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $blood_group_filter);
} else {
    $sql = "SELECT fullname, latitude, longitude, blood_group FROM donors";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (!empty($row['latitude']) && !empty($row['longitude'])) {
        $row['landmark'] = getNearestLandmark($row['latitude'], $row['longitude']);
        $donors[] = $row;
    }
}

$conn->close();

function getNearestLandmark($lat, $lon) {
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";
    $context = stream_context_create(["http" => ["header" => "User-Agent: BloodDonorApp"]]);
    $response = file_get_contents($url, false, $context);
    if ($response) {
        $data = json_decode($response, true);
        return $data['display_name'] ?? 'Unknown Landmark';
    }
    return 'Unknown Landmark';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        #map { height: 500px; }
        .search-container { text-align: center; margin: 10px; }
        input { padding: 8px; margin: 5px; width: 200px; }
        table { width: 70%; margin: 20px auto; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #d9534f; color: white; }
    </style>
</head>
<body>

<h2>Search for a Place or Donor</h2>
<div class="search-container">
    <input type="text" id="placeSearch" placeholder="Enter place name">
    <button onclick="searchPlace()">Find Place</button>
    <br>
    <input type="text" id="donorSearch" placeholder="Enter donor name">
    <button onclick="searchDonor()">Find Donor</button>
</div>

<div id="map"></div>

<h2>Distance Table</h2>
<table id="distanceTable">
    <tr>
        <th>Donor Name</th>
        <th>Blood Group</th>
        <th>Landmark</th>
        <th>Distance (km)</th>
    </tr>
</table>

<script>
    var map = L.map('map').setView([10.0, 78.0], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var donorMarkers = {};
    var selectedLocation = null;
    var routeLayers = [];
    var donorLocations = <?php echo json_encode($donors); ?>;

    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c).toFixed(2);
    }

    function loadDonors() {
        let tableBody = document.getElementById("distanceTable");

        donorLocations.forEach(donor => {
            let marker = L.marker([donor.latitude, donor.longitude]).addTo(map);
            
            marker.bindTooltip(`${donor.fullname}`, { permanent: true, direction: "top" });
            marker.bindPopup(`<b>${donor.fullname}</b><br>Blood Group: ${donor.blood_group}<br>Landmark: ${donor.landmark}`);

            donorMarkers[donor.fullname.toLowerCase()] = marker;

            tableBody.innerHTML += `<tr>
                <td>${donor.fullname}</td>
                <td>${donor.blood_group}</td>
                <td>${donor.landmark}</td>
                <td>N/A</td>
            </tr>`;
        });
    }

    loadDonors();

    function searchDonor() {
        let query = document.getElementById("donorSearch").value.toLowerCase();
        if (!query) return alert("Enter donor name");

        let marker = donorMarkers[query];
        if (marker) {
            map.setView(marker.getLatLng(), 14);
            marker.openPopup();
        } else {
            alert("Donor not found");
        }
    }

    async function searchPlace() {
        let query = document.getElementById("placeSearch").value;
        if (!query) return alert("Enter a place name");

        let url = `https://nominatim.openstreetmap.org/search?format=json&q=${query}`;
        let response = await fetch(url);
        let results = await response.json();

        if (results.length === 0) {
            alert("Place not found");
            return;
        }

        let place = results[0];
        map.setView([place.lat, place.lon], 14);
    }

    function clearRoutes() {
        routeLayers.forEach(layer => map.removeLayer(layer));
        routeLayers = [];
    }

    map.on('click', function (e) {
        if (selectedLocation) {
            map.removeLayer(selectedLocation);
        }
        clearRoutes();

        selectedLocation = L.marker(e.latlng, { draggable: true })
            .addTo(map)
            .bindPopup("Target Location")
            .openPopup();

        updateDistanceTable(e.latlng);
    });

    function updateDistanceTable(targetLatLng) {
        let tableBody = document.getElementById("distanceTable");
        tableBody.innerHTML = "<tr><th>Donor Name</th><th>Blood Group</th><th>Landmark</th><th>Distance (km)</th></tr>";

        donorLocations.forEach(donor => {
            var distance = calculateDistance(donor.latitude, donor.longitude, targetLatLng.lat, targetLatLng.lng);

            tableBody.innerHTML += `<tr><td>${donor.fullname}</td><td>${donor.blood_group}</td><td>${donor.landmark}</td><td>${distance}</td></tr>`;
        });
    }
</script>

</body>
</html>
