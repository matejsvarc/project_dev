<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in and get their role
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">

    <title>dev_project</title>

</head>

<body>
    <nav class="bg-gray-800 p-4 text-white flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="text-xl font-bold">dev project</a>
        </div>
        <div>
            <?php if ($user) : ?>
                <?php if ($role === 'admin') : ?>
                    <!-- For admin, display link to admin section -->
                    Vítejte, <a href="./admin/admin.php" class="hover:text-blue-400"><button><?php echo htmlspecialchars($user); ?></button></a>
                <?php else : ?>
                    <!-- For regular users, display greeting -->
                    <span class="mr-4">Vítejte, <?php echo htmlspecialchars($user); ?></span>
                <?php endif; ?>
                <a href="./accManagment/outAcc.php">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-blue-800 active:bg-blue-900 focus:outline-none">
                        Logout
                    </button>
                </a>
            <?php else : ?>
                <!-- If not logged in, display login link -->
                <a href="./accManagment/logAcc.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Přihlašte se</a>
            <?php endif; ?>
        </div>
    </nav>
</body>

</html>