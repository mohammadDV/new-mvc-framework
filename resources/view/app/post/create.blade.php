<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_TITLE ?> | Create Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create New Post</h1>
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
            <form action="/posts" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?= csrf_field() ?>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter post title"
                        value="<?= htmlspecialchars(old('title') ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="6"
                        required
                        maxlength="1000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter post content (max 1000 characters)"
                    ><?= htmlspecialchars(old('content') ?? '') ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters.</p>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/svg"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="mt-1 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB.</p>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select 
                        id="status" 
                        name="status" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="draft" <?= (old('status') ?? 'draft') == 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= (old('status') ?? 'published') == 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="/posts" class="text-gray-600 hover:text-gray-800 underline">Cancel</a>
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow transition font-medium"
                    >
                        Create Post
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Posts -->
        <div class="mt-6 text-center">
            <a href="/posts" class="text-gray-600 hover:text-gray-800 underline">← Back to Posts List</a>
        </div>
    </div>
</body>
</html>
