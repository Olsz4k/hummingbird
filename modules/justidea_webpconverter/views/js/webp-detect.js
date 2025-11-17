/**
 * Justidea WebP Converter - Frontend Detection
 *
 * @author Justidea Agency
 * @copyright 2025 Justidea Agency
 * @license AFL-3.0
 */

(function() {
    'use strict';

    /**
     * Check if browser supports WebP
     */
    function supportsWebP() {
        return new Promise((resolve) => {
            const webpData = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=';
            const img = new Image();

            img.onload = function() {
                resolve(img.width === 1);
            };

            img.onerror = function() {
                resolve(false);
            };

            img.src = webpData;
        });
    }

    /**
     * Add WebP class to HTML element
     */
    supportsWebP().then((supported) => {
        if (supported) {
            document.documentElement.classList.add('webp');
        } else {
            document.documentElement.classList.add('no-webp');
        }
    });

    /**
     * Lazy load WebP images
     * Replaces image sources with WebP versions if available
     */
    function replaceWithWebP() {
        supportsWebP().then((supported) => {
            if (!supported) {
                return;
            }

            // Find all img tags without data-webp-processed
            const images = document.querySelectorAll('img:not([data-webp-processed])');

            images.forEach((img) => {
                const src = img.getAttribute('src');

                if (!src) {
                    return;
                }

                // Skip if already WebP
                if (src.endsWith('.webp')) {
                    img.setAttribute('data-webp-processed', 'true');
                    return;
                }

                // Generate WebP path
                const webpSrc = src.replace(/\.(jpg|jpeg|png|gif)$/i, '.webp');

                // Check if WebP version exists (using a HEAD request)
                checkImageExists(webpSrc).then((exists) => {
                    if (exists) {
                        // Create picture element
                        const picture = document.createElement('picture');
                        const source = document.createElement('source');
                        const newImg = img.cloneNode(true);

                        source.setAttribute('srcset', webpSrc);
                        source.setAttribute('type', 'image/webp');

                        picture.appendChild(source);
                        picture.appendChild(newImg);

                        // Replace img with picture
                        img.parentNode.replaceChild(picture, img);
                    }

                    img.setAttribute('data-webp-processed', 'true');
                });
            });
        });
    }

    /**
     * Check if image exists using HEAD request
     */
    function checkImageExists(url) {
        return new Promise((resolve) => {
            const img = new Image();

            img.onload = function() {
                resolve(true);
            };

            img.onerror = function() {
                resolve(false);
            };

            img.src = url;
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', replaceWithWebP);
    } else {
        replaceWithWebP();
    }

    // Re-run on dynamic content load (for AJAX)
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updatedProduct', replaceWithWebP);
        prestashop.on('updateCart', replaceWithWebP);
    }

})();
