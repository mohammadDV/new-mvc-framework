<?php
use System\Session\Session;
use System\Auth\Auth;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_TITLE ?> | Post Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Post Details</h1>
        </div>

        <!-- Post Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="space-y-6">
                <!-- Image -->
                <?php if (!empty($post['image'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Image</label>
                        <img src="<?= asset(htmlspecialchars($post['image'])) ?>" alt="Post image" class="max-w-full h-auto rounded-lg shadow-md">
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                    <p class="text-lg text-gray-900"><?= htmlspecialchars($post['id'] ?? '') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Title</label>
                    <p class="text-lg text-gray-900 font-semibold"><?= htmlspecialchars($post['title'] ?? '') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Content</label>
                    <p class="text-lg text-gray-900 whitespace-pre-wrap"><?= htmlspecialchars($post['content'] ?? '') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <p class="text-lg">
                        <?php if (isset($post['status']) && $post['status'] == 'published'): ?>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                        <?php else: ?>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (!empty($post['created_at'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($post['created_at']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($post['updated_at'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Updated At</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($post['updated_at']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="/posts" class="text-gray-600 hover:text-gray-800 underline">← Back to Posts List</a>
                <?php if (Auth::checkLogin()): ?>
                    <div class="space-x-4">
                        <a href="/posts/<?= $post['id'] ?>/edit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg shadow transition font-medium">
                            Edit Post
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
