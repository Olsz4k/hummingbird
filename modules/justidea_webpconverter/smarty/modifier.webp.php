<?php
/**
 * Smarty plugin: webp modifier
 * Type: modifier
 * Name: webp
 * Purpose: Convert image URL to WebP version
 *
 * Usage in templates:
 * {$product.cover.large.url|webp}
 * <img src="{$product.cover.large.url|webp}" alt="{$product.name}">
 *
 * @author Justidea Agency
 * @copyright 2025 Justidea Agency
 * @license AFL-3.0
 */

function smarty_modifier_webp($imageUrl, $checkExists = false)
{
    if (empty($imageUrl)) {
        return $imageUrl;
    }

    // Check if WebP conversion is enabled
    if (!Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
        return $imageUrl;
    }

    // Already WebP
    if (preg_match('/\.webp$/i', $imageUrl)) {
        return $imageUrl;
    }

    // Generate WebP URL
    $webpUrl = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $imageUrl);

    // Optionally check if file exists
    if ($checkExists) {
        $parsedUrl = parse_url($webpUrl);
        $filePath = _PS_ROOT_DIR_ . $parsedUrl['path'];

        if (!file_exists($filePath)) {
            return $imageUrl; // Return original if WebP doesn't exist
        }
    }

    return $webpUrl;
}
