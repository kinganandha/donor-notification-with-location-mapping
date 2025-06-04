<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "person_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$donors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['blood_group'])) {
    $blood_group = $_POST['blood_group'];

    $sql = "SELECT * FROM donors WHERE blood_group = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $blood_group);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Need Blood</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #d9534f;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            font-weight: bold;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #d9534f;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #c9302c;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #d9534f;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-container button {
            width: 200px;
        }
    </style>
    <script>
        function sendMessage(donorId, mobileNumber) {
            let messageContent = prompt("Enter your message:");

            if (messageContent === null || messageContent.trim() === "") {
                alert("Message cannot be empty.");
                return;
            }

            $.ajax({
                url: "send_message.php",
                type: "POST",
                data: {
                    donor_id: donorId,
                    mobile: mobileNumber,
                    message: messageContent
                },
                success: function(response) {
                    console.log("Server Response:", response);
                    if (response.includes("Message sent successfully")) {
                        alert("Message sent successfully!");
                    } else {
                        alert("Failed to send message: " + response);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", error);
                    alert("Failed to send message due to an error.");
                }
            });
        }
    </script>
</head>
<body>

<h2>Search Blood Donors</h2>
<form method="POST">
    <label>Blood Group:</label>
    <select name="blood_group" required>
        <option value="">Select</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
    </select>
    <button type="submit">Search</button>
</form>

<?php if (!empty($donors)) { ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Mobile</th>
            <th>Age</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Blood Group</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
        <?php foreach ($donors as $donor) { ?>
        <tr>
            <td><?php echo $donor['fullname']; ?></td>
            <td><?php echo $donor['mobile']; ?></td>
            <td><?php echo $donor['age']; ?></td>
            <td><?php echo $donor['email']; ?></td>
            <td><?php echo $donor['gender']; ?></td>
            <td><?php echo $donor['blood_group']; ?></td>
            <td><?php echo $donor['address']; ?></td>
            <td>
                <button onclick="sendMessage('<?php echo $donor['id']; ?>', '<?php echo $donor['mobile']; ?>')">Send Message</button>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="btn-container">
        <form action="show_location.php" method="GET">
            <input type="hidden" name="blood_group" value="<?php echo htmlspecialchars($_POST['blood_group']); ?>">
            <button type="submit">Show Location</button>
        </form>

        <form action="get_location.php" method="GET">
            <input type="hidden" name="blood_group" value="<?php echo htmlspecialchars($_POST['blood_group']); ?>">
            <button type="submit">Get Location</button>
        </form>

        <button onclick="window.history.back()">Go Back</button>
    </div>

<?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
    <p style="text-align: center; color: red;">No donors found for the selected blood group.</p>
<?php } ?>

</body>
</html>
