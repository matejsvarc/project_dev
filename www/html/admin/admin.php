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
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mt-4 mb-8 text-center">Admin Dashboard - Users</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-2 px-4 text-center">ID</th>
                        <th class="py-2 px-4 text-center">Username</th>
                        <th class="py-2 px-4 text-center">Role</th>
                        <th class="py-2 px-4 text-center">Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="py-2 px-4 text-center">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <select name="newRole" class="py-1 px-2 border border-gray-300 rounded">
                                        <option value="uzivatel" <?php echo ($user['role'] === 'uzivatel') ? 'selected' : ''; ?>>uzivatel</option>
                                        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>admin</option>
                                    </select>
                                    <button type="submit" name="changeRole" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded">Change</button>
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