<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '/var/www/include/database.php'; // Use absolute path
require 'adminNavbar.php';

// Handle search and sorting
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">
    <title>Product Overview</title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Product Overview</h2>

        <!-- Search and Sort form -->
        <form method="get" class="mb-4 flex flex-col md:flex-row md:items-center">
            <input type="text" name="search" placeholder="Search products" value="<?php echo htmlspecialchars($search); ?>" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">

            <select name="sort" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">
                <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                <option value="popularity" <?php echo $sort === 'popularity' ? 'selected' : ''; ?>>Sort by Popularity</option>
                <option value="quantity" <?php echo $sort === 'quantity' ? 'selected' : ''; ?>>Sort by Quantity</option>
            </select>

            <select name="order" class="border p-2 rounded mb-2 md:mb-0 md:mr-2">
                <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Descending</option>
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="border p-4 rounded-lg">';
                    echo '<h3 class="text-xl font-bold mb-2">' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<img src="../' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['name']) . '" class="mb-2">';
                    echo '<p class="mb-2">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p class="mb-2">Quantity: ' . htmlspecialchars($row['quantity']) . '</p>';
                    echo '<p class="mb-2">Popularity: ' . htmlspecialchars($row['popularity']) . '</p>';
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