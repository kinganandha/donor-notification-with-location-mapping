<?php
session_start();

// Example credentials
$valid_user = "donoradmin";
$valid_pass = "blood123";

if ($_POST['username'] === $valid_user && $_POST['password'] === $valid_pass) {
    $_SESSION['username'] = $valid_user;
    header("Location: header.php");
    exit();
} else {
    header("Location: login.php?error=1");
    exit();
}
?>
