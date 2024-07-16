<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path
require 'navbar.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Increment the popularity
    $update_sql = "UPDATE product SET popularity = popularity + 1 WHERE id = $product_id";
    $mysqli->query($update_sql);

    // Retrieve the product details
    $sql = "SELECT * FROM product WHERE id = $product_id";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

// Check if the user is logged in and is an admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styling.scss">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/2">
                <img src="admin/<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full">
            </div>
            <div class="md:w-1/2 md:pl-10">
                <h2 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="text-2xl font-bold text-gray-800 mb-4">$<?php echo htmlspecialchars($product['price']); ?></p>
                <?php if ($is_admin) : ?>
                    <p class="text-sm text-gray-600">Popularity: <?php echo htmlspecialchars($product['popularity']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>