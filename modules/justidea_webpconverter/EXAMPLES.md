# Usage Examples - Justidea WebP Converter

Complete guide with real-world examples for Hummingbird theme integration.

---

## 📝 Table of Contents

1. [Smarty Function Examples](#smarty-function-examples)
2. [Smarty Modifier Examples](#smarty-modifier-examples)
3. [Hummingbird Theme Integration](#hummingbird-theme-integration)
4. [Advanced Use Cases](#advanced-use-cases)
5. [PHP Integration](#php-integration)

---

## 1. Smarty Function Examples

### Basic Usage

```smarty
{* Simple image with WebP *}
{webp_picture src=$product.cover.large.url alt=$product.name}
```

**Output:**
```html
<picture>
    <source srcset="/img/p/1/2/3/123.webp" type="image/webp">
    <img src="/img/p/1/2/3/123.jpg" alt="Product Name" loading="lazy">
</picture>
```

### With CSS Class

```smarty
{webp_picture
    src=$product.cover.large.url
    alt=$product.name
    class="img-fluid product-image"
}
```

### With Dimensions

```smarty
{webp_picture
    src=$image.large.url
    alt=$product.name
    width="800"
    height="800"
    class="product-main-image"
}
```

### With Custom Attributes

```smarty
{webp_picture
    src=$product.cover.large.url
    alt=$product.name
    class="zoomable"
    data-zoom="true"
    data-full-image="{$product.cover.large.url}"
    loading="eager"
}
```

### Eager Loading (Above the Fold)

```smarty
{* For hero images - disable lazy loading *}
{webp_picture
    src=$banner.image
    alt=$banner.title
    loading="eager"
    class="hero-image"
}
```

---

## 2. Smarty Modifier Examples

### Simple URL Conversion

```smarty
{* Original image URL *}
{$product.cover.large.url}
{* Output: /img/p/1/2/3/123.jpg *}

{* WebP version *}
{$product.cover.large.url|webp}
{* Output: /img/p/1/2/3/123.webp *}
```

### In IMG Tag

```smarty
<img src="{$product.cover.large.url|webp}" alt="{$product.name}">
```

### In Background Image

```smarty
<div class="hero-banner" style="background-image: url({$banner.image|webp})">
    {$banner.content}
</div>
```

### In Srcset

```smarty
<img
    srcset="
        {$image.small.url|webp} 300w,
        {$image.medium.url|webp} 600w,
        {$image.large.url|webp} 1200w
    "
    sizes="(max-width: 768px) 300px, (max-width: 1024px) 600px, 1200px"
    src="{$image.large.url|webp}"
    alt="{$product.name}"
>
```

---

## 3. Hummingbird Theme Integration

### Product List (Miniatures)

**File:** `templates/catalog/_partials/miniatures/product.tpl`

**Before:**
```smarty
<img
    src="{$product.cover.bySize.home_default.url}"
    alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
    loading="lazy"
    data-full-size-image-url="{$product.cover.large.url}"
    class="product-thumbnail"
/>
```

**After:**
```smarty
{webp_picture
    src=$product.cover.bySize.home_default.url
    alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
    loading="lazy"
    data-full-size-image-url="{$product.cover.large.url|webp}"
    class="product-thumbnail"
}
```

### Product Page - Main Image

**File:** `templates/catalog/_partials/product-cover-thumbnails.tpl`

**Before:**
```smarty
<img
    class="js-qv-product-cover img-fluid"
    src="{$product.cover.large.url}"
    alt="{$product.cover.legend}"
    title="{$product.cover.legend}"
    width="{$product.cover.large.width}"
    height="{$product.cover.large.height}"
/>
```

**After:**
```smarty
{webp_picture
    src=$product.cover.large.url
    alt=$product.cover.legend
    title=$product.cover.legend
    width=$product.cover.large.width
    height=$product.cover.large.height
    class="js-qv-product-cover img-fluid"
}
```

### Product Thumbnails

**File:** `templates/catalog/_partials/product-cover-thumbnails.tpl`

**Before:**
```smarty
{foreach from=$product.images item=image}
    <img
        src="{$image.bySize.small_default.url}"
        alt="{$image.legend}"
        class="product-thumbnail"
    />
{/foreach}
```

**After:**
```smarty
{foreach from=$product.images item=image}
    {webp_picture
        src=$image.bySize.small_default.url
        alt=$image.legend
        class="product-thumbnail"
    }
{/foreach}
```

### Category Cover

**File:** `templates/catalog/listing/category.tpl`

**Before:**
```smarty
{if $category.image.large.url}
    <img
        src="{$category.image.large.url}"
        alt="{$category.name}"
        class="category-cover img-fluid"
    />
{/if}
```

**After:**
```smarty
{if $category.image.large.url}
    {webp_picture
        src=$category.image.large.url
        alt=$category.name
        class="category-cover img-fluid"
    }
{/if}
```

### Homepage Slider

**File:** `modules/ps_imageslider/views/templates/hook/slider.tpl`

**Before:**
```smarty
<img src="{$slide.image_url}" alt="{$slide.legend}">
```

**After:**
```smarty
{webp_picture
    src=$slide.image_url
    alt=$slide.legend
    loading="eager"
    class="slider-image"
}
```

### Featured Products

**File:** `modules/ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl`

**Before:**
```smarty
<img
    src="{$product.cover.bySize.home_default.url}"
    alt="{$product.cover.legend}"
    class="product-image"
/>
```

**After:**
```smarty
{webp_picture
    src=$product.cover.bySize.home_default.url
    alt=$product.cover.legend
    class="product-image"
}
```

---

## 4. Advanced Use Cases

### Responsive Images with Multiple Sizes

```smarty
<picture>
    <source
        media="(max-width: 576px)"
        srcset="{$image.small.url|webp}"
        type="image/webp"
    >
    <source
        media="(max-width: 992px)"
        srcset="{$image.medium.url|webp}"
        type="image/webp"
    >
    <source
        srcset="{$image.large.url|webp}"
        type="image/webp"
    >
    <img
        src="{$image.large.url}"
        alt="{$product.name}"
        class="img-fluid"
    >
</picture>
```

### Conditional WebP Loading

```smarty
{if Configuration::get('JUSTIDEA_WEBP_ENABLED')}
    {webp_picture src=$image.url alt=$product.name}
{else}
    <img src="{$image.url}" alt="{$product.name}">
{/if}
```

### Dynamic Image Gallery

```smarty
<div class="product-gallery">
    {foreach from=$product.images item=image name=gallery}
        <div class="gallery-item">
            {webp_picture
                src=$image.large.url
                alt="{$product.name} - {$smarty.foreach.gallery.iteration}"
                class="gallery-image"
                data-index="{$smarty.foreach.gallery.iteration}"
            }
        </div>
    {/foreach}
</div>
```

### Logo with WebP

```smarty
{* In header.tpl *}
{webp_picture
    src=$shop.logo
    alt=$shop.name
    width="250"
    height="99"
    loading="eager"
    class="logo"
}
```

### OG Image Meta Tag

```smarty
{* In head.tpl *}
<meta property="og:image" content="{$product.cover.large.url|webp}">
```

---

## 5. PHP Integration

### Manual Conversion in Module

```php
// In your custom module
require_once _PS_MODULE_DIR_ . 'justidea_webpconverter/classes/WebPConverter.php';

$converter = new WebPConverter(
    80,    // Quality
    'auto', // Method
    true   // Keep original
);

$result = $converter->convertToWebP('/path/to/image.jpg');

if ($result['success']) {
    echo "Converted! Saved: " . $result['saved_bytes'] . " bytes";
    echo "WebP path: " . $result['webp_path'];
}
```

### Batch Convert Directory

```php
$converter = new WebPConverter();
$stats = $converter->convertDirectory(
    _PS_PROD_IMG_DIR_,
    80,    // Quality
    true   // Recursive
);

echo "Total: " . $stats['total'];
echo "Converted: " . $stats['converted'];
echo "Failed: " . $stats['failed'];
echo "Saved: " . $stats['saved_bytes'] . " bytes";
```

### Check System Support

```php
$support = WebPConverter::checkSupport();

if ($support['imagick']) {
    echo "ImageMagick is available";
}

if ($support['gd']) {
    echo "GD Library with WebP is available";
}

if (!$support['any']) {
    echo "No WebP conversion method available!";
}
```

### Generate Picture Tag in Controller

```php
// In your controller
$html = WebPConverter::generatePictureTag(
    $product['cover']['large']['url'],
    $product['name'],
    'img-fluid product-image',
    ['data-zoom' => 'true', 'loading' => 'lazy']
);

$this->context->smarty->assign('product_image_html', $html);
```

### Hook Integration in Custom Module

```php
class MyCustomModule extends Module
{
    public function hookDisplayProductAdditionalInfo($params)
    {
        $product = $params['product'];

        require_once _PS_MODULE_DIR_ . 'justidea_webpconverter/classes/WebPConverter.php';

        $imageHtml = WebPConverter::generatePictureTag(
            $product['cover']['large']['url'],
            $product['name'],
            'custom-product-image'
        );

        $this->context->smarty->assign('custom_image', $imageHtml);

        return $this->display(__FILE__, 'views/templates/hook/product-info.tpl');
    }
}
```

---

## 🎯 Best Practices

### 1. Use `{webp_picture}` for Important Images
- Product images
- Category covers
- Hero banners
- Logos

### 2. Use `|webp` Modifier for Dynamic Content
- Background images
- Inline styles
- JavaScript-generated content

### 3. Eager Load Above-the-Fold Images
```smarty
{webp_picture src=$hero.image alt=$hero.title loading="eager"}
```

### 4. Lazy Load Below-the-Fold Images
```smarty
{webp_picture src=$product.image alt=$product.name loading="lazy"}
```

### 5. Always Provide Alt Text
```smarty
{* Good *}
{webp_picture src=$image.url alt=$product.name}

{* Bad *}
{webp_picture src=$image.url}
```

### 6. Keep Original Files
Always set "Keep original images" to **Yes** in module configuration for fallback support.

---

## 🚀 Performance Tips

### Preload Critical Images

In `templates/_partials/head.tpl`:

```html
<link rel="preload" as="image" type="image/webp" href="{$shop.logo|webp}">
```

### Use Proper Image Sizes

Don't load large images for thumbnails:

```smarty
{* Good - use appropriate size *}
{webp_picture src=$product.cover.bySize.home_default.url}

{* Bad - using large image for thumbnail *}
{webp_picture src=$product.cover.large.url}
```

### Combine with Responsive Images

```smarty
{webp_picture
    src=$image.large.url
    srcset="{$image.small.url|webp} 300w, {$image.large.url|webp} 800w"
    sizes="(max-width: 768px) 300px, 800px"
}
```

---

**Need more examples?** Contact Justidea Agency or check the module documentation.
