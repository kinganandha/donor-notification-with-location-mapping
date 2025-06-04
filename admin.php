<?php
session_start();
$conn = new mysqli("localhost", "root", "", "person_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Admin Login Credentials
$admin_user = "rabi";
$admin_pass = "vj";

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid username or password!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// If not logged in, show login form
if (!isset($_SESSION['admin_logged_in'])) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
            form { background: white; padding: 20px; border-radius: 10px; display: inline-block; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
            input, button { padding: 10px; margin: 5px; width: 100%; }
            button { background: #d9534f; color: white; border: none; cursor: pointer; }
            button:hover { background: #c9302c; }
        </style>
    </head>
    <body>
        <h2>Admin Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </body>
    </html>
<?php
    exit;
}

// Handle Add Donor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_donor'])) {
    $fullname = $_POST['fullname'];
    $mobile = $_POST['mobile'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $blood_group = $_POST['blood_group'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    if (!empty($latitude) && !empty($longitude)) {
        $stmt = $conn->prepare("INSERT INTO donors (fullname, mobile, age, email, blood_group, address, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssdd", $fullname, $mobile, $age, $email, $blood_group, $address, $latitude, $longitude);
        if ($stmt->execute()) {
            header("Location: admin.php");
            exit;
        }
    }
}

// Handle Delete Donor
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM donors WHERE id = $id");
    header("Location: admin.php");
    exit;
}

// Fetch Donors
$result = $conn->query("SELECT * FROM donors");
$donors = [];
while ($row = $result->fetch_assoc()) {
    $donors[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; text-align: center; }
        .container { width: 90%; margin: 20px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #d9534f; color: white; }
        button { padding: 10px; background: #d9534f; color: white; border: none; cursor: pointer; }
        button:hover { background: #c9302c; }
        #map { height: 400px; width: 100%; margin-top: 20px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Panel - Donor Management</h2>
    <a href="admin.php?logout=true"><button>Logout</button></a>

    <h3>Add Donor</h3>
    <form method="post">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="text" name="mobile" placeholder="Mobile" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="blood_group" required>
            <option value="A+">A+</option>
            <option value="B+">B+</option>
            <option value="O+">O+</option>
            <option value="AB+">AB+</option>
            <option value="A-">A-</option>
        </select>
        <input type="text" name="address" placeholder="Address" required>
        <button type="button" onclick="openLocationPicker()">Set Location</button>
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="submit" name="add_donor">Add Donor</button>
    </form>

    <h3>All Donors</h3>
    <table>
        <tr>
            <th>Full Name</th>
            <th>Mobile</th>
            <th>Age</th>
            <th>Email</th>
            <th>Blood Group</th>
            <th>Address</th>
            <th>Location</th>
            <th>Action</th>
        </tr>
        <?php foreach ($donors as $donor): ?>
            <tr>
                <td><?= htmlspecialchars($donor['fullname']) ?></td>
                <td><?= htmlspecialchars($donor['mobile']) ?></td>
                <td><?= htmlspecialchars($donor['age']) ?></td>
                <td><?= htmlspecialchars($donor['email']) ?></td>
                <td><?= htmlspecialchars($donor['blood_group']) ?></td>
                <td><?= htmlspecialchars($donor['address']) ?></td>
                <td>(<?= $donor['latitude'] ?>, <?= $donor['longitude'] ?>)</td>
                <td><a href="admin.php?delete=<?= $donor['id'] ?>"><button>Delete</button></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Donor Locations</h3>
    <div id="map"></div>
</div>

<script>
    function openLocationPicker() {
        let newWindow = window.open('location_picker.php', 'Set Location', 'width=800,height=600');
        window.addEventListener('message', function(event) {
            document.getElementById('latitude').value = event.data.lat;
            document.getElementById('longitude').value = event.data.lng;
        });
    }

    var map = L.map('map').setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

    <?php foreach ($donors as $donor): ?>
        L.marker([<?= $donor['latitude'] ?>, <?= $donor['longitude'] ?>]).addTo(map).bindPopup("<b><?= $donor['fullname'] ?></b><br>Blood: <?= $donor['blood_group'] ?><br><?= $donor['address'] ?>");
    <?php endforeach; ?>
</script>

</body>
</html>
