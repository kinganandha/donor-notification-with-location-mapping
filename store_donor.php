<?php
$servername = "localhost";
$username = "root"; // Change if you have a different username
$password = ""; // Change if you have a password set
$database = "person_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$fullname = trim($_POST['fullname']);
$mobile = trim($_POST['mobile']);
$age = (int)$_POST['age'];
$email = trim($_POST['email']);
$gender = $_POST['gender'];
$blood_group = $_POST['blood_group'];
$address = trim($_POST['address']);
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Validate required fields
if (
    empty($fullname) || empty($mobile) || empty($age) || empty($email) ||
    empty($gender) || empty($blood_group) || empty($address) ||
    empty($latitude) || empty($longitude)
) {
    die("Error: All fields are required.");
}

// Format the mobile number by prepending the country code
function formatPhoneNumber($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^\d]/', '', $phone);

    // Check if the phone number starts with the country code, otherwise prepend +91
    if (substr($phone, 0, 1) != '9') {
        // Prepend +91 to the phone number if it is not in the required format
        $phone = '+91' . $phone;
    } else {
        $phone = '+91' . $phone; // Assuming 10 digits are entered without country code
    }

    return $phone;
}

// Format the mobile number before storing it
$mobile = formatPhoneNumber($mobile);

// Validate mobile number format (10-digit number starting with 6-9)
if (!preg_match('/^\+91\d{10}$/', $mobile)) {
    die("Error: Invalid mobile number.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email address.");
}

// Validate age range
if ($age < 18 || $age > 65) {
    die("Error: Age must be between 18 and 65.");
}

// Remove the unique constraint on mobile column to allow duplicates
// Uncomment the following line if you haven't removed the unique index yet
// $conn->query("ALTER TABLE donors DROP INDEX mobile;");

// Insert donor details into database
$stmt = $conn->prepare("INSERT INTO donors (fullname, mobile, age, email, gender, blood_group, address, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssissssdd", $fullname, $mobile, $age, $email, $gender, $blood_group, $address, $latitude, $longitude);

if ($stmt->execute()) {
    echo "Success: Donor details stored successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
