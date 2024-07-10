<?php

// Předpokládejme, že máte session start nebo nějakou logiku pro ověření přihlášeného uživatele
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
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
                <?php if ($user == 'admin') : ?>
                    <!-- Pro admina zobrazit speciální odkaz na admin sekci -->
                    Vítejte, <a href="./admin/overview.php" class="mr-4 bg-blue-500 hover:bg-blue-800 text-white font-bold "><button> <?php echo htmlspecialchars($user); ?></button></a>
                <?php else : ?>
                    <!-- Pro běžné uživatele zobrazit jen text -->
                    <span class="mr-4">Vítejte, <?php echo htmlspecialchars($user); ?></span>
                <?php endif; ?>
                <a href="./accManagment/outAcc.php">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-blue-800 active:bg-blue-900 focus:outline-none">
                        Logout
                    </button>
                </a>
            <?php else : ?>
                <a href="./accManagment/logAcc.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Přihlašte se</a>
            <?php endif; ?>
        </div>
    </nav>
</body>

</html>