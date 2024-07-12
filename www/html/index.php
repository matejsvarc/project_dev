<?php
require '../include/database.php';
require 'navbar.php';
// Fetch all products from the database
$sql = "SELECT * FROM product";
$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">

    <title>dev_project</title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Product Listing</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($product = $result->fetch_assoc()) : ?>
                <div class="border p-4 rounded-lg">
                    <img src="admin/uploads/<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover rounded-t-lg mb-4">
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-gray-700 mb-2">Quantity: <?php echo htmlspecialchars($product['quantity']); ?></p>
                    <p class="text-gray-700 mb-2">Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                    <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($product['description']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>


</html>