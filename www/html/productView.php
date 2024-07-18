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
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <style>
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .admin-controls {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .admin-controls button {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container mx-auto mt-10">
        <div class="flex flex-col md:flex-row bg-white p-6 rounded-lg shadow-lg">
            <div class="md:w-1/2">
                <img src="admin/<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-3/4 h-3/4 rounded-lg">
            </div>
            <div class="md:w-1/2 md:pl-10 mt-4 md:mt-0">
                <div class="mt-6">
                    <a href="index.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back to Products</a>
                </div>
                <h2 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($product['tags']); ?></p>
                <div class="w-full md:w-3/4 lg:w-1/2 xl:w-1/3 px-4 py-2">
                    <p class="bg-gray-100 rounded-lg shadow-md p-4 text-sm text-gray-800 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
                <p class="text-2xl font-bold text-gray-800 mt-4">$<?php echo htmlspecialchars($product['price']); ?></p>
                <?php if ($is_admin) : ?>
                    <p class="text-sm text-gray-600 mt-2">Popularity: <?php echo htmlspecialchars($product['popularity']); ?></p>
                    <div class="admin-controls mt-4">
                        <a href="admin/editProducts.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                        <form method="post" action="admin/deleteProduct.php" class="inline">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>