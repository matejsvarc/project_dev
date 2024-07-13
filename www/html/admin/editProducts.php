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

// Get all products
$result = $mysqli->query("SELECT * FROM product");

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
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-2 px-4 text-center">ID</th>
                        <th class="py-2 px-4 text-center">Name</th>
                        <th class="py-2 px-4 text-center">Quantity</th>
                        <th class="py-2 px-4 text-center">Description</th>
                        <th class="py-2 px-4 text-center">Price</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($row['price']); ?></td>
                            <td class="py-2 px-4 text-center">
                                <form action="editProducts.php" method="post">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required class="mt-1 p-2 w-full border rounded-md">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required class="mt-1 p-2 w-full border rounded-md">
                                    <textarea name="description" required class="mt-1 p-2 w-full border rounded-md"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    <input type="number" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required class="mt-1 p-2 w-full border rounded-md">
                                    <button type="submit" name="updateProduct" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded mt-2">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>