<?php
session_start();
// Nastavení připojení k databázi
$servername = "database"; // Opraveno na název hostitele služby MySQL
$username = "admin"; // Vaše uživatelské jméno
$password = "heslo"; // Vaše heslo
$database = "eshop"; // Název vaší databáze

$mysqli = new mysqli($servername, $username, $password, $database); {
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute SQL query
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "User created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>

</body>

</html>