<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// You can access user information like this:
// $user_id = $_SESSION['user_id'];
// $user_email = $_SESSION['user_email'];
// $user_name = $_SESSION['user_name'];
?>