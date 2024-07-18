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
    $price = (float)$_POST['price'];
    $tags = $mysqli->real_escape_string($_POST['tags']);

    $sql = "UPDATE product SET name='$name', quantity='$quantity', description='$description', price='$price', tags='$tags' WHERE id='$id'";

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
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

// Modify the SQL query to include searching by tags
$sql = "SELECT * FROM product WHERE (name LIKE ? OR tags LIKE ?) ORDER BY $sort $order";
$stmt = $mysqli->prepare($sql);
$searchTerm = '%' . $search . '%';
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Pagination settings
$items_per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total number of products
$total_result = $mysqli->query("SELECT COUNT(*) AS count FROM product $search");
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['count'];
$total_pages = ceil($total_items / $items_per_page);

// Get products for the current page
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">
    <title>Edit Products</title>
    <style>
        .tag {
            display: inline-block;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    </style>
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
            <form method="get" class="mb-6 flex flex-col md:flex-row items-center justify-center">
                <input type="text" name="search" placeholder="Search products" value="<?php echo htmlspecialchars($search); ?>" class="border p-2 rounded mb-2 md:mb-0 md:mr-2 w-full md:w-1/3">

                <select name="sort" class="border p-2 rounded mb-2 md:mb-0 md:mr-2 w-full md:w-1/6">
                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                    <option value="popularity" <?php echo $sort === 'popularity' ? 'selected' : ''; ?>>Sort by Popularity</option>
                    <option value="quantity" <?php echo $sort === 'quantity' ? 'selected' : ''; ?>>Sort by Quantity</option>
                </select>

                <select name="order" class="border p-2 rounded mb-2 md:mb-0 md:mr-2 w-full md:w-1/6">
                    <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Descending</option>
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full md:w-auto">Search</button>
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
                            <form action="editProducts.php" method="post" enctype="multipart/form-data">
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
                                    <input type="number" id="price_<?php echo $row['id']; ?>" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" step="0.01" required class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="mb-2">
                                    <label for="tags_<?php echo $row['id']; ?>" class="block text-gray-700">Tags</label>
                                    <input type="text" id="tags_<?php echo $row['id']; ?>" name="tags" value="<?php echo htmlspecialchars($row['tags']); ?>" class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="mb-2">
                                    <label for="image_<?php echo $row['id']; ?>" class="block text-gray-700">Image</label>
                                    <input type="file" id="image_<?php echo $row['id']; ?>" name="image" accept="image/*" class="mt-1 p-1 w-full border rounded-md">
                                </div>
                                <div class="flex justify-between mt-4">
                                    <button type="submit" name="updateProduct" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                                    <button type="submit" name="deleteProduct" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                                </div>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </form>
        </div>
        <div class="mt-6">
            <?php if ($page > 1) : ?>
                <a href="?search=<?php echo urlencode($search_query); ?>&sort_by=<?php echo $sort_by; ?>&order=<?php echo $order; ?>&page=<?php echo $page - 1; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Previous</a>
            <?php endif; ?>
            <?php if ($page < $total_pages) : ?>
                <a href="?search=<?php echo urlencode($search_query); ?>&sort_by=<?php echo $sort_by; ?>&order=<?php echo $order; ?>&page=<?php echo $page + 1; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.selectItem');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>

</html>