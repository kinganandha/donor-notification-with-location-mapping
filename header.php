<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #d9534f;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .logo h1 {
            margin: 0;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Blood Donation System</h1>
        </div>
        <nav>
            <ul>
            
                <li><a href="about.php">About Us</a></li>
                <li><a href="why_donate.php">Why Donate Blood</a></li>
                <li><a href="become_donor.php">Become a Donor</a></li>
                <li><a href="need_blood.php">Need Blood</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

 <center>
    <img id="slideshow" src="img/_107317099_blooddonor976.jpg" alt="Blood Donation Image">
    </center>

    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        img {
            width: 1350px;
            height: 650px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }
    </style>



<script>
    // Array of images
    const images = [
        "img/im2.jpg",
        "img/im3.jpg",
        "img/_107317099_blooddonor976.jpg",
        "img/im4.jpg"
    ];

    let index = 0; // Track current image

    function changeImage() {
        index = (index + 1) % images.length; // Loop through images
        document.getElementById("slideshow").src = images[index];
    }

    // Change image every second (1000ms)
    setInterval(changeImage, 1000);
</script>

</body>
</html>
