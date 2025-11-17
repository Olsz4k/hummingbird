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
    public const METHOD_AUTO = 'auto';
    public const METHOD_GD = 'gd';
    public const METHOD_IMAGICK = 'imagick';

    private int $quality = 80;
    private string $method = self::METHOD_AUTO;
    private bool $keepOriginal = true;
    private array $errors = [];

    /**
     * Constructor
     *
     * @param int $quality WebP quality (60-100)
     * @param string $method Conversion method (auto, gd, imagick)
     * @param bool $keepOriginal Keep original files
     */
    public function __construct(int $quality = 80, string $method = self::METHOD_AUTO, bool $keepOriginal = true)
    {
        $this->quality = $quality;
        $this->method = $method;
        $this->keepOriginal = $keepOriginal;
    }

    /**
     * Convert single image to WebP
     *
     * @param string $sourcePath Path to source image
     * @param int|null $quality Quality override
     * @return array<string, mixed> Result with success status and info
     */
    public function convertToWebP(string $sourcePath, ?int $quality = null): array
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
    private function convertWithImageMagick(string $source, string $destination, int $quality): bool
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
    private function convertWithGD(string $source, string $destination, int $quality): bool
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
     * @return array<string, int> Statistics
     */
    public function convertDirectory(string $directory, ?int $quality = null, bool $recursive = true): array
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
     * @return string|null Method constant or null if no method available
     */
    private function detectBestMethod(): ?string
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
     * @return array<string, bool> Status of available methods
     */
    public static function checkSupport(): array
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
     * @return array<int, string> Errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Clear errors
     *
     * @return void
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Generate HTML picture tag with WebP and fallback
     *
     * @param string $imagePath Original image path/URL
     * @param string $alt Alt text
     * @param string $class CSS class
     * @param array<string, string> $attributes Additional attributes
     * @return string HTML picture tag
     */
    public static function generatePictureTag(string $imagePath, string $alt = '', string $class = '', array $attributes = []): string
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
