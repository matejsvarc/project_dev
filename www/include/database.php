<?php

// Nastavení připojení k databázi

$servername = "database"; // Opraveno na název hostitele služby MySQL
$username = "admin"; // Vaše uživatelské jméno
$password = "heslo"; // Vaše heslo
$database = "eshop"; // Název vaší databáze

$conn = new mysqli($servername, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
