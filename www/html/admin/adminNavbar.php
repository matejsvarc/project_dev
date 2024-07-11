<?php
require '/var/www/include/database.php'; // Use absolute path
// Check if the user is logged in and is an admin
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : false;

if ($role !== 'admin') {
    // Redirect non-admin users to the home page or login page
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">
    <title>Admin Dashboard</title>
</head>

<body>
    <nav class="bg-gray-800 p-4 text-white flex justify-between items-center">
        <div class="flex items-center">
            <a href="admin.php" class="text-xl font-bold">Admin Dashboard</a>
        </div>
        <div>
            <?php if ($user) : ?>
                <span class="mr-4">Vítejte, <?php echo htmlspecialchars($user); ?></span>
                <a href="../accManagment/outAcc.php">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-blue-800 active:bg-blue-900 focus:outline-none">
                        Logout
                    </button>
                </a>
            <?php else : ?>
                <a href="../accManagment/logAcc.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Přihlašte se</a>
            <?php endif; ?>
        </div>
    </nav>
</body>

</html>