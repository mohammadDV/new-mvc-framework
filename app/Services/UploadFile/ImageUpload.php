<?php
namespace App\Services\UploadFile;

use App\Exceptions\UnauthorizedException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageUpload
{
    /**
     * Upload and fit image to the given path and name
     * @param $file
     * @param $path
     * @param $name
     * @param $width
     * @param $height
     * @return string
     */
    public static function UploadAndFitImage(array $file, string $path = 'posts', ?int $width = null, ?int $height = null)
    {
        // Get the public directory path
        $publicDir = BASE_DIR . DIRECTORY_SEPARATOR . 'public';

        $datePath = str_replace('/', DIRECTORY_SEPARATOR, date('Y/M/d'));
        $path = 'images' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $datePath;
        $name = date('Y_m_d_H_i_s_') . rand(10,99);
        
        // Clean and prepare the relative path
        $path = trim($path, '\/') . DIRECTORY_SEPARATOR; 
        $name = trim($name, '\/') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        
        // Construct the full path within public directory
        $fullPath = $publicDir . DIRECTORY_SEPARATOR . $path;
        
        // Create directory if it doesn't exist
        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0777, true))
            {
                throw new UnauthorizedException('image resize : failed to create directory');
            }
        }

        // Check if the directory is writable
        if (!is_writable($fullPath)) {
            throw new UnauthorizedException('Directory is not writable');
        }
            
        $manager = new ImageManager(new GdDriver());

        // If width and height are provided, resize the image
        if ($width && $height) {
            $image = $manager->read($file['tmp_name'])->resize($width, $height);
        } else {
            $image = $manager->read($file['tmp_name']);
        }
        
        // Save the image to the given path and name
        $image->save($fullPath . $name);
        
        // Return the path relative to public directory (for use in URLs)
        // Convert directory separator to forward slash for URL
        $urlPath = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        return '/' . $urlPath . $name;
    }
}