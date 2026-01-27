<?php $title = 'Login'; ?>
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="max-w-md w-full">
        <!-- Login Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Login</h1>
                <p class="text-gray-600 mt-2">Sign in to your account</p>
            </div>

            <!-- Success Flash Messages -->
            <?php if (flash('login')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?= flash('login') ?></span>
                </div>
            <?php endif; ?>

            <?php if (flash('logout')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?= flash('logout') ?></span>
                </div>
            <?php endif; ?>

            <!-- Error Flash Messages -->
            <?php if (error('login')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?= error('login') ?></span>
                </div>
            <?php endif; ?>

            <!-- Validation Errors -->
            <?php if (error_exist()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Validation Errors:</strong>
                    <ul class="list-disc list-inside mt-2">
                        <?php foreach (all_errors() as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="/login" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        autocomplete="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Enter your email"
                        value="<?= htmlspecialchars(old('email') ?? '') ?>"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Enter your password"
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        Password must be at least 8 characters long.
                    </p>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium px-6 py-3 rounded-lg shadow transition duration-200"
                    >
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Don't have an account? 
                    <a href="/register" class="text-blue-500 hover:text-blue-700 font-medium underline">
                        Register here
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
