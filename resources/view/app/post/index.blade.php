<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_TITLE ?> | Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Posts Management</h1>
                <a href="/posts/create" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow transition font-medium">
                    Create New Post
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($_GET["success"])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?= $_GET["success"] ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET["error"])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $_GET["error"] ?></span>
            </div>
        <?php endif; ?>

        <!-- Posts Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <?php if (empty($paginator->items)): ?>
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">No posts found.</p>
                    <a href="/posts/create" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow transition">
                        Create First Post
                    </a>
                </div>
            <?php else: ?>
                <!-- Pagination Info -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?= $paginator->firstItem() ?></span> to 
                        <span class="font-medium"><?= $paginator->lastItem() ?></span> of 
                        <span class="font-medium"><?= $paginator->total ?></span> posts
                    </p>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($paginator->items as $post): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($post['id'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($post['image'])): ?>
                                        <img src="<?= asset(htmlspecialchars($post['image'])) ?>" alt="Post image" class="h-16 w-16 object-cover rounded">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($post['title'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    <?= htmlspecialchars($post['content'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (isset($post['status']) && $post['status'] == 'published'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($post['created_at'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/posts/<?= $post['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                                    <a href="/posts/<?= $post['id'] ?>/edit" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <form action="/posts/<?= $post['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination Links -->
                <?php if ($paginator->lastPage > 1): ?>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <!-- Previous Button -->
                                <?php if ($paginator->hasPreviousPages()): ?>
                                    <a href="<?= $paginator->previousPageUrl() ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
                                        Previous
                                    </span>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php 
                                $pageNumbers = $paginator->getPageNumbers(2);
                                $showFirst = !in_array(1, $pageNumbers);
                                $showLast = !in_array($paginator->lastPage, $pageNumbers);
                                ?>

                                <?php if ($showFirst): ?>
                                    <a href="<?= $paginator->url(1) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50">
                                        1
                                    </a>
                                    <?php if ($pageNumbers[0] > 2): ?>
                                        <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300">
                                            ...
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php foreach ($pageNumbers as $pageNum): ?>
                                    <?php if ($pageNum == $paginator->currentPage): ?>
                                        <span class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border-t border-b border-blue-300">
                                            <?= $pageNum ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= $paginator->url($pageNum) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50">
                                            <?= $pageNum ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if ($showLast): ?>
                                    <?php if ($pageNumbers[count($pageNumbers) - 1] < $paginator->lastPage - 1): ?>
                                        <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300">
                                            ...
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?= $paginator->url($paginator->lastPage) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50">
                                        <?= $paginator->lastPage ?>
                                    </a>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <?php if ($paginator->hasMorePages()): ?>
                                    <a href="<?= $paginator->nextPageUrl() ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
                                        Next
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Per Page Selector -->
                            <div class="flex items-center">
                                <label for="per_page" class="text-sm text-gray-700 mr-2">Per page:</label>
                                <select id="per_page" name="per_page" onchange="window.location.href='<?= $paginator->path ?>?page=1&per_page=' + this.value" class="px-3 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="10" <?= $paginator->perPage == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="15" <?= $paginator->perPage == 15 ? 'selected' : '' ?>>15</option>
                                    <option value="25" <?= $paginator->perPage == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $paginator->perPage == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $paginator->perPage == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="/" class="text-gray-600 hover:text-gray-800 underline">Back to Home</a>
        </div>
    </div>
</body>
</html>
