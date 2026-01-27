<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_TITLE ?> | User Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
        </div>

        <!-- User Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                    <p class="text-lg text-gray-900"><?= htmlspecialchars($user['id'] ?? '') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                    <p class="text-lg text-gray-900"><?= htmlspecialchars($user['name'] ?? '') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-lg text-gray-900"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                </div>

                <?php if (!empty($user['created_at'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($user['created_at']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['updated_at'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Updated At</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($user['updated_at']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="/users" class="text-gray-600 hover:text-gray-800 underline">← Back to Users List</a>
                <div class="space-x-4">
                    <a href="/users/<?= $user['id'] ?>/edit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg shadow transition font-medium">
                        Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
