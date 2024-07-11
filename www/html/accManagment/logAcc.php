<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);

    // Ověření uživatele
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Přihlášení úspěšné, nastavení session
        $_SESSION['id'] = $user['id']; // Uložení skutečného ID uživatele z DB
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Uložení role uživatele do session
        header("Location: ../index.php"); // Přesměrování na hlavní stránku
        exit;
    } else {
        $error = "Nesprávné uživatelské jméno nebo heslo";
    }
    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl mb-4">Přihlášení do účtu</h2>
        <form action="logAcc.php" method="post">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Uživatelské jméno</label>
                <input type="text" name="username" id="username" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Heslo</label>
                <input type="password" name="password" id="password" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Přihlásit se</button>
            <a href="createAcc.php" class="block mt-4 text-blue-500 hover:underline">Nemáte účet? Vytvořte si ho zde!</a>
        </form>
    </div>
</body>

</html>