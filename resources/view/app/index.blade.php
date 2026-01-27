<?php 
$title = 'Home';
use System\Auth\Auth;
?>
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Latest Posts</h1>
        <p class="text-gray-600">Discover our latest articles and updates</p>
    </div>

    <!-- Posts Grid -->
    <?php if (empty($posts->items)): ?>
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No posts yet</h3>
                <p class="text-gray-600 mb-6">Be the first to create a post!</p>
                <?php if (Auth::checkLogin()): ?>
                    <a href="/posts/create" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow transition font-medium">
                        Create First Post
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($posts->items as $post): ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                    <!-- Post Image -->
                    <?php if (!empty($post['image'])): ?>
                        <a href="/posts/<?= $post['id'] ?>">
                            <div class="relative h-48 w-full overflow-hidden bg-gray-200">
                                <img 
                                    src="<?= asset(htmlspecialchars($post['image'])) ?>" 
                                    alt="<?= htmlspecialchars($post['title'] ?? '') ?>"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                >
                            </div>
                        </a>
                    <?php else: ?>
                        <a href="/posts/<?= $post['id'] ?>">
                            <div class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </a>
                    <?php endif; ?>

                    <!-- Post Content -->
                    <div class="p-6 flex-grow flex flex-col">
                        <!-- Status Badge -->
                        <?php if (isset($post['status']) && ($post['status'] == 'published' || $post['status'] == 1)): ?>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mb-3 w-fit">
                                Published
                            </span>
                        <?php else: ?>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mb-3 w-fit">
                                Draft
                            </span>
                        <?php endif; ?>

                        <!-- Post Title -->
                        <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                            <a href="/posts/<?= $post['id'] ?>" class="hover:text-blue-600 transition-colors">
                                <?= htmlspecialchars($post['title'] ?? 'Untitled') ?>
                            </a>
                        </h2>

                        <!-- Post Content Preview -->
                        <p class="text-gray-600 mb-4 line-clamp-3 flex-grow">
                            <?= htmlspecialchars(mb_substr($post['content'] ?? '', 0, 150)) ?><?= mb_strlen($post['content'] ?? '') > 150 ? '...' : '' ?>
                        </p>

                        <!-- Post Meta -->
                        <div class="mt-auto pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-700">
                                        <?= htmlspecialchars($post['user_name'] ?? 'Unknown Author') ?>
                                    </span>
                                </div>
                                <?php if (!empty($post['created_at'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>
                                            <?= date('M d, Y', strtotime($post['created_at'])) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Read More Link -->
                        <div class="mt-4">
                            <a href="/posts/<?= $post['id'] ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                                Read more
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($posts->lastPage > 1): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Pagination Info -->
                <div class="mb-4 text-center text-sm text-gray-600">
                    Showing <span class="font-medium text-gray-900"><?= $posts->firstItem() ?></span> to 
                    <span class="font-medium text-gray-900"><?= $posts->lastItem() ?></span> of 
                    <span class="font-medium text-gray-900"><?= $posts->total ?></span> posts
                </div>

                <!-- Pagination Controls -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Previous Button -->
                        <?php if ($posts->hasPreviousPages()): ?>
                            <a href="<?= $posts->previousPageUrl() ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors">
                                ← Previous
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
                                ← Previous
                            </span>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php 
                        $pageNumbers = $posts->getPageNumbers(2);
                        $showFirst = !in_array(1, $pageNumbers);
                        $showLast = !in_array($posts->lastPage, $pageNumbers);
                        ?>

                        <?php if ($showFirst): ?>
                            <a href="<?= $posts->url(1) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50 transition-colors">
                                1
                            </a>
                            <?php if ($pageNumbers[0] > 2): ?>
                                <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300">
                                    ...
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php foreach ($pageNumbers as $pageNum): ?>
                            <?php if ($pageNum == $posts->currentPage): ?>
                                <span class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border-t border-b border-blue-300">
                                    <?= $pageNum ?>
                                </span>
                            <?php else: ?>
                                <a href="<?= $posts->url($pageNum) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50 transition-colors">
                                    <?= $pageNum ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ($showLast): ?>
                            <?php if ($pageNumbers[count($pageNumbers) - 1] < $posts->lastPage - 1): ?>
                                <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300">
                                    ...
                                </span>
                            <?php endif; ?>
                            <a href="<?= $posts->url($posts->lastPage) ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-300 hover:bg-gray-50 transition-colors">
                                <?= $posts->lastPage ?>
                            </a>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <?php if ($posts->hasMorePages()): ?>
                            <a href="<?= $posts->nextPageUrl() ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors">
                                Next →
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
                                Next →
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Per Page Selector -->
                    <div class="flex items-center">
                        <label for="per_page" class="text-sm text-gray-700 mr-2">Per page:</label>
                        <select 
                            id="per_page" 
                            name="per_page" 
                            onchange="window.location.href='<?= $posts->path ?>?page=1&per_page=' + this.value" 
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="6" <?= $posts->perPage == 6 ? 'selected' : '' ?>>6</option>
                            <option value="9" <?= $posts->perPage == 9 ? 'selected' : '' ?>>9</option>
                            <option value="12" <?= $posts->perPage == 12 ? 'selected' : '' ?>>12</option>
                            <option value="15" <?= $posts->perPage == 15 ? 'selected' : '' ?>>15</option>
                            <option value="18" <?= $posts->perPage == 18 ? 'selected' : '' ?>>18</option>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
