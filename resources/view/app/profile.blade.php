<?php $title = 'Profile'; ?>
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
            <p class="text-gray-600 mt-2">We're glad to have you here.</p>
        </div>

        <div class="mt-6 flex flex-col items-center">
            <?php if (!empty($user['picture'])): ?>
                <img src="<?= htmlspecialchars($user['picture']); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full shadow-md">
            <?php else: ?>
                <div class="w-24 h-24 bg-gray-200 rounded-full shadow-md flex items-center justify-center">
                    <span class="text-gray-500">No Image</span>
                </div>
            <?php endif; ?>
            
            <div class="mt-4 text-center">
                <p class="text-gray-700 font-semibold">Your Name:</p>
                <p class="text-gray-600 text-sm"><?= htmlspecialchars($user['name']); ?></p>
            </div>

            <div class="mt-4 text-center">
                <p class="text-gray-700 font-semibold">Your Email:</p>
                <?php if (!empty($user['email'])): ?>
                <p class="text-gray-600 text-sm"><?= htmlspecialchars($user['email']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
