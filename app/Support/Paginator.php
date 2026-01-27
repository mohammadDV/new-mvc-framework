<?php

namespace App\Support;

/**
 * Paginator Class
 * 
 * Handles pagination logic and data.
 */
class Paginator
{
    /**
     * The items for the current page.
     *
     * @var array
     */
    public array $items;

    /**
     * The total number of items.
     *
     * @var int
     */
    public int $total;

    /**
     * The number of items per page.
     *
     * @var int
     */
    public int $perPage;

    /**
     * The current page number.
     *
     * @var int
     */
    public int $currentPage;

    /**
     * The total number of pages.
     *
     * @var int
     */
    public int $lastPage;

    /**
     * The base URL for pagination links.
     *
     * @var string
     */
    public string $path;

    /**
     * Paginator constructor.
     *
     * @param array $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param string $path
     */
    public function __construct(array $items, int $total, int $perPage, int $currentPage, string $path = '')
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->lastPage = max(1, (int) ceil($total / $perPage));
        $this->path = $path ?: $this->getCurrentPath();
    }

    /**
     * Get the current path from the request.
     *
     * @return string
     */
    private function getCurrentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        // Remove query string
        return strtok($path, '?');
    }

    /**
     * Get the URL for a given page number.
     *
     * @param int $page
     * @return string
     */
    public function url(int $page): string
    {
        if ($page <= 0) {
            $page = 1;
        }
        if ($page > $this->lastPage) {
            $page = $this->lastPage;
        }

        $queryParams = $_GET;
        $queryParams['page'] = $page;
        
        return $this->path . '?' . http_build_query($queryParams);
    }

    /**
     * Determine if there are more pages.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * Determine if there are previous pages.
     *
     * @return bool
     */
    public function hasPreviousPages(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl(): ?string
    {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage + 1);
        }
        return null;
    }

    /**
     * Get the URL for the previous page.
     *
     * @return string|null
     */
    public function previousPageUrl(): ?string
    {
        if ($this->hasPreviousPages()) {
            return $this->url($this->currentPage - 1);
        }
        return null;
    }

    /**
     * Get the first item number for the current page.
     *
     * @return int
     */
    public function firstItem(): int
    {
        return ($this->currentPage - 1) * $this->perPage + 1;
    }

    /**
     * Get the last item number for the current page.
     *
     * @return int
     */
    public function lastItem(): int
    {
        return min($this->currentPage * $this->perPage, $this->total);
    }

    /**
     * Get an array of page numbers to display.
     *
     * @param int $onEachSide
     * @return array
     */
    public function getPageNumbers(int $onEachSide = 2): array
    {
        $pages = [];
        
        $start = max(1, $this->currentPage - $onEachSide);
        $end = min($this->lastPage, $this->currentPage + $onEachSide);

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        return $pages;
    }
}