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
    $stmt = $mysqli->prepare("SELECT id, username, email, role FROM users");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            background-color: #f3f4f6;
        }

        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container mx-auto mt-10">
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold text-gray-800">Admin Dashboard</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card bg-white p-6 rounded-lg shadow-lg text-center">
                <a href="productAdd.php" class="text-2xl font-semibold text-gray-700 hover:text-blue-600">
                    <i class="fas fa-plus-circle"></i> Add Product
                </a>
            </div>
            <div class="card bg-white p-6 rounded-lg shadow-lg text-center">
                <a href="editProducts.php" class="text-2xl font-semibold text-gray-700 hover:text-blue-600">
                    <i class="fas fa-edit"></i> Edit Products
                </a>
            </div>
            <div class="card bg-white p-6 rounded-lg shadow-lg text-center col-span-1 md:col-span-3">
                <a href="productOverview.php" class="text-2xl font-semibold text-gray-700 hover:text-blue-600">
                    <i class="fas fa-chart-line"></i> Overview/Popularity
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Manage Users</h3>
            <table class="w-full table-auto">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td class="border px-4 py-2 text-center"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="border px-4 py-2 text-center"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="border px-4 py-2 text-center"><?= htmlspecialchars($user['role']) ?></td>
                            <td class="border px-4 py-2">
                                <form method="post" class="flex items-center justify-center">
                                    <input type="hidden" name="userId" value="<?= htmlspecialchars($user['id']) ?>">
                                    <select name="newRole" class="border p-2 rounded mr-2">
                                        <option value="uzivatel" <?= $user['role'] === 'uzivatel' ? 'selected' : '' ?>>uzivatel</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                    </select>
                                    <button type="submit" name="changeRole" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Change Role
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>