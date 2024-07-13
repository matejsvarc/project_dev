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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $mysqli->real_escape_string($_POST['name']);
    $quantity = (int)$_POST['quantity'];
    $description = $mysqli->real_escape_string($_POST['description']);
    $price = (int)$_POST['price'];

    // Handle image upload
    $uploadOk = 1;
    $img = "";
    if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $message = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["img"]["size"] > 5000000) { // 5MB
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $message = "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $img = $target_file;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if ($uploadOk == 1 && !empty($img)) {
        $sql = "INSERT INTO product (name, quantity, description, img, price) VALUES ('$name', '$quantity', '$description', '$img', '$price')";

        if ($mysqli->query($sql) === TRUE) {
            $message = "Product added successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styling.scss">
    <title>Add Product</title>
</head>

<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl mb-4">Add New Product</h2>
        <?php if (!empty($message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="productAdd.php" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" id="name" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" required class="mt-1 p-2 w-full border rounded-md"></textarea>
            </div>
            <div class="mb-4">
                <label for="img" class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="img" id="img" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" required class="mt-1 p-2 w-full border rounded-md">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">Add Product</button>
        </form>
    </div>
</body>

</html>