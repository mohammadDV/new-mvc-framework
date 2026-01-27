<?php
use System\Session\Session;
use System\Auth\Auth;

$isLoggedIn = Auth::checkLogin();
$currentUser = $isLoggedIn ? Session::get('user') : null;
$pageTitle = isset($title) ? $title . ' | ' . APP_TITLE : APP_TITLE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600 hover:text-blue-800">
                        <?= APP_TITLE ?>
                    </a>
                </div>

                <!-- Navigation Menu -->
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn && $currentUser): ?>
                        <!-- Authenticated User Menu -->
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700 text-sm">
                                Welcome, <span class="font-semibold"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                            </span>
                            
                            <!-- Users Menu -->
                            <a href="/users" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">
                                Users
                            </a>

                            <!-- Posts Menu -->
                            <a href="/posts" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">
                                Posts
                            </a>

                            <!-- Profile Link -->
                            <a href="/profile" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">
                                Profile
                            </a>

                            <!-- Logout Button -->
                            <form action="/logout" method="POST" class="inline">
                                <?= csrf_field() ?>
                                <button 
                                    type="submit" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition"
                                    onclick="return confirm('Are you sure you want to logout?');"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <div class="flex items-center space-x-4">
                            <a href="/login" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition">
                                Login
                            </a>
                            <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; <?= date('Y') ?> <?= APP_TITLE ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
