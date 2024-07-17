<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
$user = isset($_SESSION['username']) ? $_SESSION['username'] : false;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : false;

if ($role !== 'admin') {
    // Redirect non-admin users to the home page or login page
    header('Location: ../index.php');
    exit;
}

require '/var/www/include/database.php'; // Use absolute path
require 'adminNavbar.php';

// Handle product update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateProduct'])) {
    $id = (int)$_POST['id'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $quantity = (int)$_POST['quantity'];
    $description = $mysqli->real_escape_string($_POST['description']);
    $price = (int)$_POST['price'];

    $sql = "UPDATE product SET name='$name', quantity='$quantity', description='$description', price='$price' WHERE id='$id'";

    if ($mysqli->query($sql) === TRUE) {
        $message = "Product updated successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteProduct'])) {
    $id = (int)$_POST['id'];

    $sql = "DELETE FROM product WHERE id='$id'";

    if ($mysqli->query($sql) === TRUE) {
        $message = "Product deleted successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

// Handle batch deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteSelected'])) {
    $ids = $_POST['ids'];

    $ids = implode(',', array_map('intval', $ids)); // Sanitize input

    $sql = "DELETE FROM product WHERE id IN ($ids)";

    if ($mysqli->query($sql) === TRUE) {
        $message = "Selected products deleted successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

// Handle search and sorting
$search_query = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$sort_columns = ['name', 'date_added', 'quantity'];
$order = $order === 'DESC' ? 'DESC' : 'ASC';

if (!in_array($sort_by, $sort_columns)) {
    $sort_by = 'name';
}

$search_sql = $search_query ? "WHERE name LIKE '%$search_query%'" : '';

// Pagination settings
$items_per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total number of products
$total_result = $mysqli->query("SELECT COUNT(*) AS count FROM product $search_sql");
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['count'];
$total_pages = ceil($total_items / $items_per_page);

// Get products for the current page
$result = $mysqli->query("SELECT * FROM product $search_sql ORDER BY $sort_by $order LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">
    <title>Edit Products</title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Edit Products</h2>
        <?php if (!empty($message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="overflow-x-auto">
            <form id="searchForm" action="editProducts.php" method="get" class="mb-4">
                <div class="flex items-center mb-4">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>" class="p-2 border rounded mr-2">
                    <select name="sort_by" class="p-2 border rounded mr-2">
                        <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                        <option value="quantity" <?php echo $sort_by === 'quantity' ? 'selected' : ''; ?>>Sort by Quantity</option>
                    </select>
                    <select name="order" class="p-2 border rounded mr-2">
                        <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                        <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    </select>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search</button>
                </div>
            </form>
            <form id="batchDeleteForm" action="editProducts.php" method="post">
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll">Select All</label>
                </div>
                <button type="submit" name="deleteSelected" class="relative bottom-5 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">Delete Selected</button>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <div class="bg-white border border-gray-200 p-2 rounded-md">
                            <input type="checkbox" name="ids[]" value="<?php echo htmlspecialchars($row['id']); ?>" class="selectItem mb-2">
                            <form action="editProducts.php" method="post">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <div class="mb-2">
                                    <label for="name_<?php echo $row['id']; ?>" class="block text-gray-700">Name</label>
                                    <input type="text" id="name_<?php echo $row['id']; ?>" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="mb-2">
                                    <label for="quantity_<?php echo $row['id']; ?>" class="block text-gray-700">Quantity</label>
                                    <input type="number" id="quantity_<?php echo $row['id']; ?>" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="mb-2">
                                    <label for="description_<?php echo $row['id']; ?>" class="block text-gray-700">Description</label>
                                    <textarea id="description_<?php echo $row['id']; ?>" name="description" required class="mt-1 p-1 w-full border rounded-md"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                </div>
                                <div class="mb-2">
                                    <label for="price_<?php echo $row['id']; ?>" class="block text-gray-700">Price</label>
                                    <input type="number" id="price_<?php echo $row['id']; ?>" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="flex justify-center mt-2">
                                    <button type="submit" name="updateProduct" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded mr-2">Update</button>
                                    <button type="submit" name="deleteProduct" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded">Delete</button>
                                </div>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </form>
        </div>
        <div class="mt-4 flex justify-center">
            <nav class="inline-flex">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&sort_by=<?php echo urlencode($sort_by); ?>&order=<?php echo urlencode($order); ?>" class="px-4 py-2 mx-1 border <?php echo $i === $page ? 'bg-blue-500 text-white' : 'bg-white'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </nav>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            var checkboxes = document.querySelectorAll('.selectItem');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
</body>

</html>