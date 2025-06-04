<?php
if (!isset($_GET['blood_group']) || empty($_GET['blood_group'])) {
    echo "<script>alert('No blood group selected!'); window.history.back();</script>";
    exit;
}

$blood_group = $_GET['blood_group'];

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "person_db";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donors based on the selected blood group
$sql = "SELECT fullname, latitude, longitude FROM donors WHERE blood_group = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $blood_group);
$stmt->execute();
$result = $stmt->get_result();

$donors = [];
while ($row = $result->fetch_assoc()) {
    $donors[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Locations</title>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        #map { height: 500px; width: 100%; margin: 10px auto; }
        .button-container { margin-top: 20px; }
        button { padding: 12px 20px; font-size: 16px; cursor: pointer; background-color: #d9534f; color: white; border: none; border-radius: 5px; }
        button:hover { background-color: #c9302c; }
    </style>
</head>
<body>

<h2>Donor Locations for Blood Group: <?php echo htmlspecialchars($blood_group); ?></h2>

<div id="map"></div>

<div class="button-container">
    <button onclick="goBack()">Go Back</button>
</div>

<script>
    var donors = <?php echo json_encode($donors); ?>;
    var map = L.map('map').setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    donors.forEach(donor => {
        if (donor.latitude && donor.longitude) {
            L.marker([donor.latitude, donor.longitude])
                .addTo(map)
                .bindTooltip(donor.fullname, { permanent: true, direction: "top" });
        }
    });

    function goBack() {
        window.history.back();
    }
</script>

</body>
</html>
