<?php

declare(strict_types=1);

namespace App\Providers;


class SessionProvider extends Provider
{
    /**
     * Boot the session provider.
     *
     * @return void
     */
    public function boot(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->handleFlash('flash');
        $this->handleFlash('errorFlash');
        $this->handleOldInputs();
    }

    /**
     * Handle the flash messages.
     *
     * @param string $key The key of the flash message.
     * @return void
     */
    private function handleFlash(string $key): void
    {
        $temporaryKey = "temporary_{$key}";

        unset($_SESSION[$temporaryKey]);

        if (isset($_SESSION[$key])) {
            $_SESSION[$temporaryKey] = $_SESSION[$key];
            unset($_SESSION[$key]);
        }
    }

    /**
     * Handle the old inputs.
     *
     * @return void
     */
    private function handleOldInputs(): void
    {
        unset($_SESSION['temporary_old']);

        if (isset($_SESSION['old'])) {
            $_SESSION['temporary_old'] = $_SESSION['old'];
            unset($_SESSION['old']);
        }

        $_SESSION['old'] = array_merge($_GET, $_POST);
    }
}