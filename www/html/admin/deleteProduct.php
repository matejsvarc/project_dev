<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : false;

if ($role !== 'admin') {
    // Redirect non-admin users to the home page or login page
    header('Location: ../index.php');
    exit;
}

require '/var/www/include/database.php'; // Use absolute path

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $sql = "DELETE FROM product WHERE id='$id'";

    if ($mysqli->query($sql) === TRUE) {
        $_SESSION['message'] = "Product deleted successfully";
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $mysqli->error;
    }

    // Redirect back to the editProducts.php page
    header('Location: editProducts.php');
    exit;
} else {
    // Redirect if the request is not a POST request or if the ID is not set
    header('Location: editProducts.php');
    exit;
}
