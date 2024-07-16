<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path
require 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styling.scss">
    <title>Product List</title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <?php require 'productList.php'; ?>
    </div>
</body>

</html>