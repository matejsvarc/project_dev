<?php
require '../../include/database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="mt-8 max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" id="username" name="username" required class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <input type="submit" value="Create User" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 cursor-pointer transition duration-300 ease-in-out">
                    <a href="../index.php" class="inline-block mt-4 w-full text-center bg-gray-200 text-gray-700 p-2 rounded-md hover:bg-gray-300 cursor-pointer transition duration-300 ease-in-out">Zpět</a>
                    <a class="block mt-4 text-blue-500 hover:underline" href="logAcc.php">Již máte účet ? Přihlašte se zde!</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>