<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path
require 'navbar.php';
$sql = "SELECT * FROM product";
$result = $mysqli->query($sql);
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
        <h2 class="text-2xl mb-4">Product List</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="border p-4 rounded-lg">';
                    echo '<h3 class="text-xl font-bold mb-2">' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<img src="admin/' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['name']) . '" class="mb-2">';
                    echo '<p class="mb-2">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p class="font-bold">$' . htmlspecialchars($row['price']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
    </div>
</body>

</html>