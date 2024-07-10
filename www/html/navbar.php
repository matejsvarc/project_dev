<?php
// Předpokládejme, že máte session start nebo nějakou logiku pro ověření přihlášeného uživatele
// session_start();
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
?>

<nav class="bg-gray-800 p-4 text-white flex justify-between items-center">
    <div class="flex items-center">
        <a href="/" class="text-xl font-bold">Název Webu</a>
    </div>
    <div>
        <?php if ($user) : ?>
            <span class="mr-4">Vítejte, <?php echo htmlspecialchars($user); ?></span>
        <?php else : ?>
            <a href="logAcc.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Přihlašte se</a>
        <?php endif; ?>
        <a href="outAcc.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Odhlásit se</a>
    </div>
</nav>