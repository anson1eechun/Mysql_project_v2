<?php
$valid_username = "123";
$valid_password = "123";

$username = $_POST['username'];
$password = $_POST['password'];

if ($username === $valid_username && $password === $valid_password) {
    header("Location: main.php");
    exit();
} else {
    header("Location: login.php?error=true");
    exit();
}
?>