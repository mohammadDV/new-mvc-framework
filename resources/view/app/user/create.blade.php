<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_TITLE ?> | Create User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create New User</h1>
        </div>

        <!-- Error Message -->
        <?php if (!empty($_GET["error"])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $_GET["error"] ?></span>
            </div>
        <?php endif; ?>

        <?php if(error_exist()) { ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul style="list-style-type:none">
                <?php foreach(all_errors() as $error) { ?>
                    <li>
                     <span class="block sm:inline"><?= $error ?></span>
                    </li>
                    <?php } ?>
                  
                </ul>
            </div>
        <?php } ?>

        <!-- Create Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/users" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter user name"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter user email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter password"
                    >
                    <p class="mt-1 text-sm text-gray-500">Password must be at least 6 characters long.</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter confirm password"
                    >
                    <p class="mt-1 text-sm text-gray-500">Confirm password must be the same as password.</p>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="/users" class="text-gray-600 hover:text-gray-800 underline">Cancel</a>
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow transition font-medium"
                    >
                        Create User
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Users -->
        <div class="mt-6 text-center">
            <a href="/users" class="text-gray-600 hover:text-gray-800 underline">← Back to Users List</a>
        </div>
    </div>
</body>
</html>
