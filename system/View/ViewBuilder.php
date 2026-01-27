<?php

declare(strict_types=1);

namespace System\View;

class ViewBuilder
{
    public $content;

    /**
     * Loads a view and stores its content.
     * @param string $dir The path or name of the view to load.
     * @return void
     */
    public function run(string $dir): void
    {
        $this->content = $this->viewLoader($dir);
    }

    /**
     * Loads the view content from a file.
     * @param string $dir The view directory or name to load.
     * @return string The HTML content of the view.
     * @throws \Exception If the view file does not exist.
     */
    private function viewLoader(string $dir): string
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $dir)) {
            throw new \InvalidArgumentException('Invalid view name');
        }

        $dir = str_replace('.', DIRECTORY_SEPARATOR, $dir);

        $basePath = realpath(BASE_DIR . '/resources/view');
        $viewPath = realpath($basePath . '/' . $dir . '.blade.php');

        if ($viewPath === false || !str_starts_with($viewPath, $basePath)) {
            throw new \Exception('View not found');
        }

        $content = file_get_contents($viewPath);

        if ($content === false) {
            throw new \Exception('Unable to load view');
        }

        return $content;
    }

    /**
     * Render view content using a temporary file (secure alternative to eval).
     *
     * @param string $content The view content to render
     * @param array $vars Variables to extract into the view scope
     * @return string The rendered view content
     */
    public function render(array $vars = []): string
    {
        extract($vars, EXTR_SKIP);

        ob_start();
        $tempFile = sys_get_temp_dir() . '/view_' . uniqid('', true) . '.php';

        try {
            file_put_contents($tempFile, $this->content);
            include $tempFile;
        } finally {
            @unlink($tempFile);
        }

        return ob_get_clean();
    }
}