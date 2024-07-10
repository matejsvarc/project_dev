<?php
session_start();

// Připojení k databázi
$servername = "database"; // Opraveno na název hostitele služby MySQL
$username = "admin"; // Vaše uživatelské jméno
$password = "heslo"; // Vaše heslo
$database = "eshop"; // Název vaší databáze

$mysqli = new mysqli($servername, $username, $password, $database); {
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);

    // Ověření uživatele
    $sql = "SELECT * FROM users WHERE username = '$username'"; // Předpokládáme, že tabulka se jmenuje 'users'
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Předpokládáme, že hesla jsou hashovaná
        if (password_verify($password, $row['password'])) {
            // Přihlášení úspěšné, nastavení session
            $_SESSION['username'] = $username;
            header("Location: index.php"); // Přesměrování na hlavní stránku
            exit;
        } else {
            $error = "Nesprávné uživatelské jméno nebo heslo";
        }
    } else {
        $error = "Nesprávné uživatelské jméno nebo heslo";
    }
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