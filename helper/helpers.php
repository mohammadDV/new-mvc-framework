<?php

/**
 * View
 *
 * @param string $dir The directory of the view
 * @param array $vars The variables of the view
 * @return void
 */
function view(string $dir, array $vars = []): void
{
    $viewBuilder = new \System\View\ViewBuilder();
    $viewBuilder->run($dir);
    
    // Determine layout name (default to 'app' if not specified)
    $layoutName = $vars['_layout'] ?? 'app';
    unset($vars['_layout']);
    
    // Pass rendered content to layout
    $vars['content'] = $viewBuilder->render($vars);
    layout($layoutName, $vars);
}

/**
* Layout
*
* @param string $layoutName The name of the layout
* @param array $vars The variables of the layout
* @throws \Exception If the layout file is not found
* @return void
*/
function layout($layoutName, $vars = []): void
{
    empty($vars) ? : extract($vars, EXTR_SKIP);
    $layoutPath = BASE_DIR . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . "{$layoutName}.blade.php";

    if (file_exists($layoutPath)) {
        include $layoutPath;
    } else {
        throw new \Exception("Layout not found: {$layoutName}");
    }
}


/**
* Current domain
*
* @return string
*/
function current_domain(): string
{
    $httpProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on") ? "https://" : "http://";
    $currentUrl = $_SERVER['HTTP_HOST'];
    return $httpProtocol . $currentUrl;
}

/**
* Redirect away
*
* @param string $url The url to redirect to
* @return void
*/
function redirect_out($url): void
{
    header("Location: ". $url);
    exit;
}

/**
* Redirect
*
* @param string $url The url to redirect to
* @return void
*/
function redirect($url): void
{
    $url = trim($url, '/ ');
    $url = strpos('z' . $url, current_domain()) === 0 ? $url : current_domain() . '/' . $url;
    header("Location: ". $url);
    exit;
}

/**
* Back
*
* @return void
*/
function back(): void
{
    $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    redirect($http_referer);
}


/**
* Old
*
* @param string $name The name of the old
* @return string|null
*/
function old($name): string|null
{
    if(isset($_SESSION['temporary_old'][$name])){
        return $_SESSION['temporary_old'][$name];
    }
    else{
        return null;
    }
}

/**
* Set flash
*
* @param string $name The name of the flash
* @param string|null $message The message of the flash
* @return void
*/
function flash($name, $message = null)
{
    if(empty($message)) {
        if (isset($_SESSION['temporary_flash'][$name])) {
            $temporary = $_SESSION['temporary_flash'][$name];
            unset($_SESSION['temporary_flash'][$name]);
            return $temporary;
        } else {
            return false;
        }
    }else{
        $_SESSION['flash'][$name] = $message;
    }
}

/**
* Set error
*
* @param string $name The name of the error
* @param string|null $message The message of the error
* @return void
*/
function error($name, $message = null)
{
    if(empty($message))
    {
        if (isset($_SESSION['temporary_errorFlash'][$name])) {
            $temporary = $_SESSION['temporary_errorFlash'][$name];
            unset($_SESSION['temporary_errorFlash'][$name]);
            return $temporary;
        } else {
            return false;
        }
    } else {
        $_SESSION['errorFlash'][$name] = $message;
    }
}

/**
* Check if error exists
*
* @param string|null $name The name of the error
* @return bool|int
*/
function error_exist($name = null) : bool|int
{
    if($name === null) {
        return isset($_SESSION['temporary_errorFlash']) === true ? count($_SESSION['temporary_errorFlash']) : false;
    } else {
        return isset($_SESSION['temporary_errorFlash'][$name]) === true ? true : false;
    }
}

/**
* Get all errors
*
* @return array|false
*/
function all_errors(): array|bool
{
    if (isset($_SESSION['temporary_errorFlash'])) {
        $temporary = $_SESSION['temporary_errorFlash'];
        unset($_SESSION['temporary_errorFlash']);
        return $temporary;
    } else{
        return false;
    }
}

/**
* Generate an asset URL
*
* @param string $src The source path
* @return string The asset URL
*/
function asset($src)
{
    return current_domain() . ("/" . trim($src, "/ "));
}

/**
* Check if user is authenticated
*
* @return bool
*/
function auth(): bool
{
    return \System\Auth\Auth::checkLogin();
}

/**
* Get the authenticated user
*
* @return array|null
*/
function user(): ?array
{
    if (\System\Auth\Auth::checkLogin()) {
        return \System\Session\Session::get('user');
    }
    return null;
}

/**
* Generate a CSRF token hidden input field for forms
*
* @return string HTML input field with CSRF token
*/
function csrf_field(): string
{
    $token = \App\Services\Csrf\CsrfToken::get();
    return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
* Get the current CSRF token value
*
* @return string The CSRF token
*/
function csrf_token(): string
{
    return \App\Services\Csrf\CsrfToken::get();
}