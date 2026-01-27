<?php

// Define a constant for the application title.
define('APP_TITLE', 'Project');

// Define the base URL for the application.
define('BASE_URL', 'http://localhost:8000');

// Define the base directory of the application.
define('BASE_DIR', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

// current route
define('CURRENT_ROUTE', trim(str_replace(BASE_URL, '', explode('?', $_SERVER['REQUEST_URI'])[0]), "/ "));

//providers list
define('PROVIDERS', [
    \App\Providers\SessionProvider::class,
    \App\Providers\RepositoryServiceProvider::class,
 ]);