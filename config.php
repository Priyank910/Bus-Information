<?php
$host = "localhost";
$username = "root"; // or your actual MySQL username
$password = ""; // set this if your MySQL user has a password
$dbname = "bus_booking";


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>