<?php

declare(strict_types=1);

namespace App\Services\UploadFile;

use App\Exceptions\UnauthorizedException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageUpload
{
    /**
     * Upload and fit image to the given path and name
     * @param array $file
     * @param string $path
     * @param string $name
     * @param int|null $width
     * @param int|null $height
     * @return string
     */
    public static function uploadAndFitImage(
        array $file,
        string $path = 'posts',
        ?int $width = null,
        ?int $height = null
    ): string {
        // Base public directory
        $publicDir = BASE_DIR . DIRECTORY_SEPARATOR . 'public';
    
        // Create date-based subpath: images/posts/2026/01/28/
        $datePath = date('Y' . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'd');
        $relativePath = 'images' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $datePath;
        
        // Generate unique file name
        $name = date('Y_m_d_H_i_s_') . rand(10, 99) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    
        // Full path on server
        $fullPath = $publicDir . DIRECTORY_SEPARATOR . trim($relativePath, '\/') . DIRECTORY_SEPARATOR;
    
        // Ensure directory exists
        if (!is_dir($fullPath) && !mkdir($fullPath, 0777, true)) {
            throw new UnauthorizedException('Image upload failed: unable to create directory.');
        }
    
        // Check writability
        if (!is_writable($fullPath)) {
            throw new UnauthorizedException('Directory is not writable.');
        }
    
        // Initialize image manager
        $manager = new ImageManager(new GdDriver());
        $image = $manager->read($file['tmp_name']);
    
        // Resize if both width and height are provided
        if ($width && $height) {
            $image->resize($width, $height);
        }
    
        // Save the image
        $image->save($fullPath . $name);
    
        // Return relative URL path (forward slashes)
        $urlPath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        return '/' . $urlPath . '/' . $name;
    }
}