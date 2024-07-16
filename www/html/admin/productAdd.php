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
    foreach ($_POST['products'] as $index => $product) {
        $name = $mysqli->real_escape_string($product['name']);
        $quantity = (int)$product['quantity'];
        $description = $mysqli->real_escape_string($product['description']);
        $price = isset($product['price']) ? floatval($product['price']) : 0.0; // Convert to float

        // Handle image upload
        $img = "";
        if (isset($_FILES["img"]) && $_FILES["img"]["error"][$index] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["img"]["name"][$index]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if file is an image
            $check = getimagesize($_FILES["img"]["tmp_name"][$index]);
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
            if ($_FILES["img"]["size"][$index] > 5000000) { // 5MB
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
                if (move_uploaded_file($_FILES["img"]["tmp_name"][$index], $target_file)) {
                    $img = $target_file;
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }

        if (!empty($img)) {
            $sql = "INSERT INTO product (name, quantity, description, img, price, popularity) 
                    VALUES ('$name', '$quantity', '$description', '$img', '$price', 0)";

            if ($mysqli->query($sql) === TRUE) {
                $message = "Product added successfully";
            } else {
                $message = "Error: " . $sql . "<br>" . $mysqli->error;
            }
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
    <title>Add Product</title>
    <style>
        .product-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f9fafb;
        }

        .product-card input,
        .product-card textarea,
        .product-card button {
            margin-top: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white shadow-md rounded-md">
        <h2 class="text-3xl mb-6 font-semibold text-gray-700">Add New Products</h2>
        <?php if (!empty($message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="productAdd.php" method="post" enctype="multipart/form-data" id="productForm">
            <div id="productContainer">
                <div class="product-card">
                    <h3 class="text-xl font-medium text-gray-600">Product 1</h3>
                    <label class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="products[0][name]" required class="mt-1 p-2 w-full border rounded-md">
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="products[0][quantity]" required class="mt-1 p-2 w-full border rounded-md">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="products[0][description]" required class="mt-1 p-2 w-full border rounded-md"></textarea>
                    <label class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" name="img[0]" required class="mt-1 p-2 w-full border rounded-md">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="products[0][price]" required class="mt-1 p-2 w-full border rounded-md">
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <button type="button" id="addProduct" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">Add Another Product</button>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">Submit All Products</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('addProduct').addEventListener('click', function() {
            let productIndex = document.querySelectorAll('.product-card').length;
            let productContainer = document.getElementById('productContainer');

            let newProduct = document.createElement('div');
            newProduct.classList.add('product-card');

            newProduct.innerHTML = `
                <h3 class="text-xl font-medium text-gray-600">Product ${productIndex + 1}</h3>
                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="products[${productIndex}][name]" required class="mt-1 p-2 w-full border rounded-md">
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="products[${productIndex}][quantity]" required class="mt-1 p-2 w-full border rounded-md">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="products[${productIndex}][description]" required class="mt-1 p-2 w-full border rounded-md"></textarea>
                <label class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="img[${productIndex}]" required class="mt-1 p-2 w-full border rounded-md">
                <label class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="products[${productIndex}][price]" required class="mt-1 p-2 w-full border rounded-md">
            `;

            productContainer.appendChild(newProduct);
        });
    </script>
</body>

</html>