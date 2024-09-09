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

        // Handle tags
        $tagsArray = json_decode($product['tags'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $tags = $mysqli->real_escape_string(implode(', ', $tagsArray));
        } else {
            $tags = '';
        }

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

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if file size is within limit
            if ($_FILES["img"]["size"][$index] > 5000000) { // 5MB
                $message = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
            } else {
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["img"]["tmp_name"][$index], $target_file)) {
                        $img = $target_file;
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $img = $target_file;
                }
            }
        }

        if (!empty($img)) {
            $sql = "INSERT INTO product (name, quantity, description, img, price, tags, popularity) VALUES ('$name', $quantity, '$description', '$img', $price, '$tags', 0)";

            if ($mysqli->query($sql) === TRUE) {
                $message = "New product(s) added successfully.";
            } else {
                $message = "Error: " . $sql . "<br>" . $mysqli->error;
            }
        }
    }
}

$mysqli->close();
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
                    <label class="block text-sm font-medium text-gray-700">Tags (press space to separate)</label>
                    <div class="tag-container" id="tag-container-0"></div>
                    <input type="text" id="tag-input-0" class="mt-1 p-2 w-full border rounded-md" placeholder="e.g., electronics, gadgets, new">
                    <input type="hidden" name="products[0][tags]">
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
                <label class="block text-sm font-medium text-gray-700">Tags (press space to separate)</label>
                <div class="tag-container" id="tag-container-${productIndex}"></div>
                <input type="text" id="tag-input-${productIndex}" class="mt-1 p-2 w-full border rounded-md" placeholder="e.g., electronics, gadgets, new">
                <input type="hidden" name="products[${productIndex}][tags]">
            `;

            productContainer.appendChild(newProduct);
            addTagEventListener(productIndex);
        });

        function addTagEventListener(index) {
            const tagInput = document.getElementById(`tag-input-${index}`);
            const tagContainer = document.getElementById(`tag-container-${index}`);
            const hiddenInput = document.querySelector(`input[name="products[${index}][tags]"]`);
            let tags = [];

            tagInput.addEventListener('keyup', function(event) {
                if (event.key === ' ') {
                    const tagText = tagInput.value.trim();
                    if (tagText !== '' && !tags.includes(tagText)) {
                        tags.push(tagText);
                        const tagElement = document.createElement('span');
                        tagElement.classList.add('tag');
                        tagElement.textContent = tagText;
                        tagElement.addEventListener('click', function() {
                            tagContainer.removeChild(tagElement);
                            tags = tags.filter(tag => tag !== tagText);
                            hiddenInput.value = JSON.stringify(tags);
                        });
                        tagContainer.appendChild(tagElement);
                        hiddenInput.value = JSON.stringify(tags);
                    }
                    tagInput.value = '';
                }
            });
        }

        document.querySelectorAll('.product-card').forEach((card, index) => {
            addTagEventListener(index);
        });
    </script>
</body>

</html>