<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blood Donor Notification - Login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f8d7da;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      width: 320px;
    }

    .login-container h2 {
      text-align: center;
      color: #c82333;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #c82333;
      border-radius: 4px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #c82333;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 10px;
    }

    button:hover {
      background-color: #a71d2a;
    }

    .error {
      color: red;
      text-align: center;
      margin-top: 10px;
    }

    .footer {
      text-align: center;
      font-size: 12px;
      color: #555;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Blood Donor Login</h2>
    <form method="POST" action="login_action.php">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>

    <?php
      if (isset($_GET['error'])) {
        echo '<div class="error">Invalid username or password</div>';
      }
    ?>

    <div class="footer">Donor Notification System © 2025</div>
  </div>
</body>
</html>
