<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path
require 'adminNavbar.php';
// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: ../accManagment/logAcc.php');
    exit;
}

$user_id = $_SESSION['id'];

try {
    // Use the $mysqli variable initialized in 'database.php'
    $stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['role'] !==  'admin') {
        // User is not an admin, redirect to login page or a not authorized page
        header('Location: ../index.php');
        exit;
    }
} catch (mysqli_sql_exception $e) {
    // Handle error
    die("Could not connect to the database: " . $e->getMessage());
}

// If the user is an admin, the rest of the page content will be displayed
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
</head>

<body>

</body>

</html>