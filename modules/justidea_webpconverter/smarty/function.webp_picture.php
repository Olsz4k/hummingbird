<?php
/**
 * Smarty plugin: webp_picture
 * Type: function
 * Name: webp_picture
 * Purpose: Generate HTML picture tag with WebP source and fallback
 *
 * Usage in templates:
 * {webp_picture src=$product.cover.large.url alt=$product.name class="img-fluid"}
 *
 * @author Justidea Agency
 * @copyright 2025 Justidea Agency
 * @license AFL-3.0
 */

function smarty_function_webp_picture($params, $smarty)
{
    if (!isset($params['src'])) {
        return '';
    }

    $src = $params['src'];
    $alt = isset($params['alt']) ? htmlspecialchars($params['alt']) : '';
    $class = isset($params['class']) ? htmlspecialchars($params['class']) : '';
    $loading = isset($params['loading']) ? htmlspecialchars($params['loading']) : 'lazy';
    $width = isset($params['width']) ? ' width="' . htmlspecialchars($params['width']) . '"' : '';
    $height = isset($params['height']) ? ' height="' . htmlspecialchars($params['height']) . '"' : '';

    // Check if WebP conversion is enabled
    if (!Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
        return sprintf(
            '<img src="%s" alt="%s" class="%s" loading="%s"%s%s>',
            htmlspecialchars($src),
            $alt,
            $class,
            $loading,
            $width,
            $height
        );
    }

    // Generate WebP path
    $pathInfo = pathinfo($src);
    $webpSrc = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

    // Build additional attributes
    $additionalAttrs = '';
    foreach ($params as $key => $value) {
        if (!in_array($key, ['src', 'alt', 'class', 'loading', 'width', 'height'])) {
            $additionalAttrs .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
    }

    // Generate picture tag
    return sprintf(
        '<picture><source srcset="%s" type="image/webp"><img src="%s" alt="%s" class="%s" loading="%s"%s%s%s></picture>',
        htmlspecialchars($webpSrc),
        htmlspecialchars($src),
        $alt,
        $class,
        $loading,
        $width,
        $height,
        $additionalAttrs
    );
}
