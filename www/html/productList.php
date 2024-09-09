<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path

$sql = "SELECT * FROM product";
$result = $mysqli->query($sql);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$sql = "SELECT * FROM product WHERE name LIKE ? ORDER BY $sort $order";
$stmt = $mysqli->prepare($sql);
$searchTerm = '%' . $search . '%';
$stmt->bind_param('s', $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Optional: Add any additional custom styles here */
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Product List</h2>
        <form method="get" class="mb-4 flex flex-col md:flex-row md:items-center">
            <input type="text" name="search" placeholder="Search products" value="<?php echo htmlspecialchars($search); ?>" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">

            <select name="sort" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">
                <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                <option value="popularity" <?php echo $sort === 'popularity' ? 'selected' : ''; ?>>Sort by Popularity</option>
                <option value="quantity" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Sort by Price</option>
            </select>

            <select name="order" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">
                <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Descending</option>
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search</button>
        </form>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="border p-4 rounded-lg hover:shadow-lg transition duration-300 flex flex-col justify-between">
                        <div>
                            <a href="productView.php?id=<?= htmlspecialchars($row['id']) ?>">
                                <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($row['name']) ?></h3>
                                <img src="admin/<?= htmlspecialchars($row['img']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="mb-2 w-full h-80 object-cover rounded-lg">
                                <p class="mb-2 text-gray-700 truncate"><?= htmlspecialchars($row['description']) ?></p>
                                <p class="font-bold text-gray-900">$<?= htmlspecialchars($row['price']) ?></p>
                            </a>
                        </div>
                        <div class="mt-4">
                            <a href="test.php"><button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">Buy</button></a>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <p class="text-gray-700">No products found.</p>
            <?php
            }
            ?>
        </div>
    </div>
</body>

</html>