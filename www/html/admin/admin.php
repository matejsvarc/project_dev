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
    $stmt = $mysqli->prepare("SELECT id, username, role FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    if (!$users) {
        echo "No users found.";
    }
} catch (mysqli_sql_exception $e) {
    // Handle error
    die("Could not connect to the database: " . $e->getMessage());
}

// Handle role change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['changeRole']) && isset($_POST['userId']) && isset($_POST['newRole'])) {
        $userId = $_POST['userId'];
        $newRole = $_POST['newRole'];

        // Update user role
        $updateStmt = $mysqli->prepare("UPDATE users SET role = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newRole, $userId);
        $updateStmt->execute();

        // Redirect to refresh the page after update
        header('Location: admin.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Admin Dashboard</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="border p-4 rounded-lg text-center">
                <a href="productAdd.php" class="text-xl font-bold">Adding Product</a>
            </div>
            <div class="border p-4 rounded-lg text-center">
                <a href="editProducts.php" class="text-xl font-bold">Editing Products</a>
            </div>
            <div class="col-span-2 border p-4 rounded-lg text-center">
                <a href="overview.php" class="text-xl font-bold">Overview/Popularity</a>
            </div>
        </div>
    </div>
</body>

</html>