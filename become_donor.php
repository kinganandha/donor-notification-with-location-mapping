<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Donor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        header {
            background-color: #d9534f;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #d9534f;
            margin-bottom: 30px;
        }

        form {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        label {
            font-weight: bold;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #d9534f;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #c9302c;
        }

        .full-width {
            grid-column: span 3;
        }

        /* Extra spacing between Full Name, Mobile, and Age */
        .spaced {
            grid-column: span 2;
        }
    </style>

    <script>
        function openLocationPicker() {
            let locationWindow = window.open("location_picker.php", "_blank", "width=800,height=600");
            window.addEventListener("message", function(event) {
                if (event.origin !== window.location.origin) return;
                let data = event.data;
                if (data.lat && data.lng) {
                    document.getElementById("latitude").value = data.lat;
                    document.getElementById("longitude").value = data.lng;
                }
            }, false);
        }

        function validateForm() {
            let name = document.forms["donorForm"]["fullname"].value.trim();
            let mobile = document.forms["donorForm"]["mobile"].value.trim();
            let email = document.forms["donorForm"]["email"].value.trim();
            let age = document.forms["donorForm"]["age"].value.trim();
            let gender = document.forms["donorForm"]["gender"].value;
            let bloodGroup = document.forms["donorForm"]["blood_group"].value;
            let address = document.forms["donorForm"]["address"].value.trim();
            let latitude = document.forms["donorForm"]["latitude"].value;
            let longitude = document.forms["donorForm"]["longitude"].value;

            let mobilePattern = /^[6-9]\d{9}$/;
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (name === "") {
                alert("Full Name is required");
                return false;
            }
            if (!mobilePattern.test(mobile)) {
                alert("Enter a valid 10-digit mobile number starting with 6-9");
                return false;
            }
            if (!emailPattern.test(email)) {
                alert("Enter a valid email address");
                return false;
            }
            if (isNaN(age) || age < 18 || age > 65) {
                alert("Age must be between 18 and 65");
                return false;
            }
            if (gender === "") {
                alert("Please select a gender");
                return false;
            }
            if (bloodGroup === "") {
                alert("Please select a blood group");
                return false;
            }
            if (address === "") {
                alert("Address is required");
                return false;
            }
            if (latitude === "" || longitude === "") {
                alert("Please set your location.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>

<header>
    <h1>Become a Donor</h1>
</header>

<div class="container">
    <h2>Register as a Blood Donor</h2>
    <form name="donorForm" action="store_donor.php" method="post" onsubmit="return validateForm()">
        
        <!-- Full Name -->
        <div class="spaced">
            <label>Full Name</label>
            <input type="text" name="fullname" required>
        </div>

        <!-- Mobile Number -->
        <div>
            <label>Mobile No</label>
            <input type="text" name="mobile" required>
        </div>

        <!-- Age -->
        <div>
            <label>Age</label>
            <input type="number" name="age" required>
        </div>

        <!-- Email -->
        <div>
            <label>Email ID</label>
            <input type="email" name="email" required>
        </div>

        <!-- Gender -->
        <div>
            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <!-- Blood Group -->
        <div>
            <label>Blood Group</label>
            <select name="blood_group" required>
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
        </div>

        <!-- Address -->
        <div class="full-width">
            <label>Address</label>
            <input type="text" name="address" required>
        </div>

        <!-- Location Picker -->
        <div class="full-width">
            <button type="button" onclick="openLocationPicker()">Set Location</button>
        </div>

        <!-- Submit Button -->
        <div class="full-width">
            <button type="submit">Submit</button>
        </div>

        <!-- Hidden Fields for Latitude & Longitude -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
    </form>
</div>

</body>
</html>
