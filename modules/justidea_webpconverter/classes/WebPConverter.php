<?php
/**
 * WebP Converter Class
 *
 * @author    Justidea Agency
 * @copyright 2025 Justidea Agency
 * @license   AFL-3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class WebPConverter
{
    const METHOD_AUTO = 'auto';
    const METHOD_GD = 'gd';
    const METHOD_IMAGICK = 'imagick';

    private $quality = 80;
    private $method = self::METHOD_AUTO;
    private $keepOriginal = true;
    private $errors = [];

    /**
     * Constructor
     */
    public function __construct($quality = 80, $method = self::METHOD_AUTO, $keepOriginal = true)
    {
        $this->quality = (int) $quality;
        $this->method = $method;
        $this->keepOriginal = (bool) $keepOriginal;
    }

    /**
     * Convert single image to WebP
     *
     * @param string $sourcePath Path to source image
     * @param int|null $quality Quality override
     * @return array Result with success status and info
     */
    public function convertToWebP($sourcePath, $quality = null)
    {
        $result = [
            'success' => false,
            'webp_path' => null,
            'original_size' => 0,
            'webp_size' => 0,
            'saved_bytes' => 0,
            'error' => null,
        ];

        // Check if source file exists
        if (!file_exists($sourcePath)) {
            $result['error'] = 'Source file does not exist: ' . $sourcePath;
            $this->errors[] = $result['error'];
            return $result;
        }

        // Get file info
        $pathInfo = pathinfo($sourcePath);
        $extension = strtolower($pathInfo['extension']);

        // Check if file is an image
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $result['error'] = 'Unsupported file format: ' . $extension;
            $this->errors[] = $result['error'];
            return $result;
        }

        // Generate WebP path
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        // Get original file size
        $result['original_size'] = filesize($sourcePath);

        // Convert based on available method
        $quality = $quality ?? $this->quality;
        $method = $this->detectBestMethod();

        try {
            switch ($method) {
                case self::METHOD_IMAGICK:
                    $success = $this->convertWithImageMagick($sourcePath, $webpPath, $quality);
                    break;

                case self::METHOD_GD:
                    $success = $this->convertWithGD($sourcePath, $webpPath, $quality);
                    break;

                default:
                    $result['error'] = 'No conversion method available';
                    $this->errors[] = $result['error'];
                    return $result;
            }

            if ($success && file_exists($webpPath)) {
                $result['success'] = true;
                $result['webp_path'] = $webpPath;
                $result['webp_size'] = filesize($webpPath);
                $result['saved_bytes'] = $result['original_size'] - $result['webp_size'];

                // Delete original if configured
                if (!$this->keepOriginal && $result['saved_bytes'] > 0) {
                    @unlink($sourcePath);
                }
            } else {
                $result['error'] = 'Conversion failed';
                $this->errors[] = $result['error'];
            }
        } catch (Exception $e) {
            $result['error'] = 'Exception: ' . $e->getMessage();
            $this->errors[] = $result['error'];
        }

        return $result;
    }

    /**
     * Convert with ImageMagick
     *
     * @param string $source Source file path
     * @param string $destination WebP file path
     * @param int $quality Quality level
     * @return bool Success status
     */
    private function convertWithImageMagick($source, $destination, $quality)
    {
        if (!extension_loaded('imagick')) {
            return false;
        }

        try {
            $image = new Imagick($source);

            // Set format and quality
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($quality);

            // Optional: Remove metadata to reduce size
            $image->stripImage();

            // Write WebP file
            $success = $image->writeImage($destination);

            // Clean up
            $image->clear();
            $image->destroy();

            return $success;
        } catch (Exception $e) {
            $this->errors[] = 'ImageMagick error: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Convert with GD Library
     *
     * @param string $source Source file path
     * @param string $destination WebP file path
     * @param int $quality Quality level
     * @return bool Success status
     */
    private function convertWithGD($source, $destination, $quality)
    {
        if (!function_exists('imagewebp')) {
            return false;
        }

        $pathInfo = pathinfo($source);
        $extension = strtolower($pathInfo['extension']);

        // Create image resource from source
        $image = null;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($source);
                break;
            case 'png':
                $image = @imagecreatefrompng($source);
                // Preserve transparency
                if ($image) {
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'gif':
                $image = @imagecreatefromgif($source);
                break;
        }

        if (!$image) {
            $this->errors[] = 'GD: Failed to create image resource from ' . $source;
            return false;
        }

        // Convert to WebP
        $success = imagewebp($image, $destination, $quality);

        // Clean up
        imagedestroy($image);

        return $success;
    }

    /**
     * Convert entire directory recursively
     *
     * @param string $directory Directory path
     * @param int|null $quality Quality override
     * @param bool $recursive Process subdirectories
     * @return array Statistics
     */
    public function convertDirectory($directory, $quality = null, $recursive = true)
    {
        $stats = [
            'total' => 0,
            'converted' => 0,
            'failed' => 0,
            'skipped' => 0,
            'saved_bytes' => 0,
        ];

        if (!is_dir($directory)) {
            return $stats;
        }

        $iterator = $recursive
            ? new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            )
            : new DirectoryIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $stats['total']++;

                    $sourcePath = $file->getPathname();
                    $webpPath = pathinfo($sourcePath, PATHINFO_DIRNAME) . '/' .
                                pathinfo($sourcePath, PATHINFO_FILENAME) . '.webp';

                    // Skip if WebP already exists
                    if (file_exists($webpPath)) {
                        $stats['skipped']++;
                        continue;
                    }

                    $result = $this->convertToWebP($sourcePath, $quality);

                    if ($result['success']) {
                        $stats['converted']++;
                        $stats['saved_bytes'] += $result['saved_bytes'];
                    } else {
                        $stats['failed']++;
                    }
                }
            }
        }

        return $stats;
    }

    /**
     * Detect best conversion method available
     *
     * @return string Method constant
     */
    private function detectBestMethod()
    {
        if ($this->method !== self::METHOD_AUTO) {
            return $this->method;
        }

        // Prefer ImageMagick for better quality
        if (extension_loaded('imagick')) {
            return self::METHOD_IMAGICK;
        }

        // Fallback to GD
        if (function_exists('imagewebp')) {
            return self::METHOD_GD;
        }

        return null;
    }

    /**
     * Check if WebP conversion is supported
     *
     * @return array Status of available methods
     */
    public static function checkSupport()
    {
        return [
            'imagick' => extension_loaded('imagick'),
            'gd' => function_exists('imagewebp'),
            'any' => extension_loaded('imagick') || function_exists('imagewebp'),
        ];
    }

    /**
     * Get conversion errors
     *
     * @return array Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Clear errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Generate HTML picture tag with WebP and fallback
     *
     * @param string $imagePath Original image path/URL
     * @param string $alt Alt text
     * @param string $class CSS class
     * @param array $attributes Additional attributes
     * @return string HTML picture tag
     */
    public static function generatePictureTag($imagePath, $alt = '', $class = '', $attributes = [])
    {
        $pathInfo = pathinfo($imagePath);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        // Build attributes string
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        $classAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';
        $altAttr = ' alt="' . htmlspecialchars($alt) . '"';

        // Check if WebP version exists (for server-side rendering)
        $webpExists = false;
        if (strpos($imagePath, 'http') !== 0) {
            // Local path
            $webpExists = file_exists(_PS_ROOT_DIR_ . $webpPath);
        }

        if ($webpExists || Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
            return sprintf(
                '<picture><source srcset="%s" type="image/webp"><img src="%s"%s%s%s loading="lazy"></picture>',
                htmlspecialchars($webpPath),
                htmlspecialchars($imagePath),
                $altAttr,
                $classAttr,
                $attrString
            );
        }

        // Fallback: just img tag
        return sprintf(
            '<img src="%s"%s%s%s loading="lazy">',
            htmlspecialchars($imagePath),
            $altAttr,
            $classAttr,
            $attrString
        );
    }
}
