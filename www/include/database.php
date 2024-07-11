<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "database"; // Replace with your actual database server
$username = "admin"; // Replace with your actual database username
$password = "heslo"; // Replace with your actual database password
$database = "eshop"; // Replace with your actual database name

$mysqli = new mysqli($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
