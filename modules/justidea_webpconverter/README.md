# Justidea WebP Converter for PrestaShop

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PrestaShop](https://img.shields.io/badge/PrestaShop-9.0%2B-brightgreen.svg)
![License](https://img.shields.io/badge/license-AFL--3.0-lightgray.svg)

**Professional WebP image conversion module for PrestaShop 9.0+**

Automatically converts all product images (JPG, PNG, GIF) to WebP format for better performance, faster page loads, and reduced bandwidth usage. Fully compatible with Hummingbird theme and all modern PrestaShop themes.

---

## 🚀 Features

### ✅ Automatic Conversion
- **Auto-convert on upload** - Images are automatically converted to WebP when uploaded
- **Batch conversion** - Convert all existing images with one click
- **Multiple image types** - Products, categories, manufacturers, suppliers

### 🎛️ Full Control
- **Configurable quality** - Set WebP quality from 60-100%
- **Keep originals** - Option to keep original files as fallback
- **Conversion method** - Auto-detect or manually choose (GD or ImageMagick)

### 📊 Statistics & Monitoring
- **Total images converted** - Track conversion progress
- **Space saved** - See how much disk space you've saved
- **System information** - Check available conversion libraries

### 🎨 Easy Template Integration
- **Smarty function** - Use `{webp_picture}` tag in templates
- **Smarty modifier** - Use `|webp` modifier for quick conversion
- **Automatic detection** - Browser compatibility detection included

### ⚡ Performance Optimized
- **Lazy loading** - Built-in lazy loading support
- **Picture tags** - Automatic `<picture>` element generation
- **Fallback support** - Works on all browsers (WebP + fallback)

---

## 📋 Requirements

- PrestaShop **9.0.0** or higher
- PHP **7.4** or higher
- **GD Library** with WebP support OR **ImageMagick** extension
- Write permissions on `/img/` directory

---

## 📦 Installation

### Method 1: Manual Installation

1. Download the module ZIP or clone the repository
2. Extract to `/modules/justidea_webpconverter/`
3. Go to **Back Office** → **Modules** → **Module Manager**
4. Search for "Justidea WebP Converter"
5. Click **Install**

### Method 2: Upload ZIP

1. Go to **Back Office** → **Modules** → **Module Manager**
2. Click **Upload a module**
3. Select the module ZIP file
4. Click **Install**

---

## ⚙️ Configuration

After installation, configure the module:

1. Go to **Modules** → **Module Manager**
2. Search for "Justidea WebP Converter"
3. Click **Configure**

### Settings:

| Setting | Description | Default |
|---------|-------------|---------|
| **Enable WebP conversion** | Master switch for WebP conversion | Yes |
| **WebP Quality** | Quality level (60-100) | 80% |
| **Auto-convert on upload** | Convert images automatically when uploaded | Yes |
| **Keep original images** | Keep JPG/PNG files as fallback | Yes |
| **Conversion method** | GD, ImageMagick, or Auto | Auto |

### Batch Conversion:

Click the **"Convert All Existing Images"** button to convert all existing product images to WebP format.

**Note:** This process may take several minutes depending on the number of images.

---

## 🎨 Template Usage

### Method 1: Smarty Function `{webp_picture}`

Use the `{webp_picture}` function to generate a complete `<picture>` tag with WebP source and fallback:

```smarty
{* Basic usage *}
{webp_picture src=$product.cover.large.url alt=$product.name}

{* With CSS class *}
{webp_picture src=$product.cover.large.url alt=$product.name class="img-fluid"}

{* With dimensions *}
{webp_picture
    src=$product.cover.large.url
    alt=$product.name
    class="product-image"
    width="800"
    height="800"
}

{* With custom attributes *}
{webp_picture
    src=$image.large.url
    alt=$product.name
    class="img-responsive"
    loading="lazy"
    data-zoom="true"
}
```

**Output:**
```html
<picture>
    <source srcset="/img/p/1/2/3/123.webp" type="image/webp">
    <img src="/img/p/1/2/3/123.jpg" alt="Product Name" class="img-fluid" loading="lazy">
</picture>
```

### Method 2: Smarty Modifier `|webp`

Use the `|webp` modifier to convert an image URL to WebP:

```smarty
{* Convert URL to WebP *}
<img src="{$product.cover.large.url|webp}" alt="{$product.name}">

{* In background-image *}
<div style="background-image: url({$category.image.large.url|webp})"></div>

{* In srcset *}
<img srcset="{$image.small.url|webp} 300w, {$image.large.url|webp} 800w">
```

### Method 3: Automatic JavaScript Detection

The module automatically detects WebP support in browsers and replaces images on the fly.

**No template changes needed!** Just upload images and they'll be served as WebP to compatible browsers.

---

## 🔧 Advanced Usage

### Check WebP Support in PHP

```php
$support = WebPConverter::checkSupport();

if ($support['imagick']) {
    echo 'ImageMagick available';
}

if ($support['gd']) {
    echo 'GD Library available';
}
```

### Manual Conversion in PHP

```php
require_once _PS_MODULE_DIR_ . 'justidea_webpconverter/classes/WebPConverter.php';

$converter = new WebPConverter(80, 'auto', true);
$result = $converter->convertToWebP('/path/to/image.jpg');

if ($result['success']) {
    echo 'Saved: ' . $result['saved_bytes'] . ' bytes';
}
```

### Generate Picture Tag Programmatically

```php
$html = WebPConverter::generatePictureTag(
    '/img/p/1/2/3/123.jpg',
    'Product Name',
    'img-fluid',
    ['data-zoom' => 'true']
);
```

---

## 📊 How It Works

### Conversion Process:

1. **Image Upload** → PrestaShop uploads JPG/PNG
2. **Hook Trigger** → Module detects upload via `actionAfterImageUpload`
3. **WebP Generation** → Image is converted to WebP (GD or ImageMagick)
4. **Original Kept** → Original file is preserved (if configured)
5. **Frontend Delivery** → Browser receives WebP (if supported) or fallback

### Browser Support:

| Browser | WebP Support |
|---------|--------------|
| Chrome | ✅ Yes (all versions) |
| Firefox | ✅ Yes (65+) |
| Safari | ✅ Yes (14+) |
| Edge | ✅ Yes (all versions) |
| Opera | ✅ Yes (all versions) |
| IE 11 | ❌ No (fallback to JPG/PNG) |

**Fallback:** Older browsers automatically receive original JPG/PNG files.

---

## 🎯 Performance Benefits

### Example Savings:

| Image Type | Original (JPG) | WebP | Savings |
|------------|---------------|------|---------|
| Product photo | 450 KB | 180 KB | **60%** |
| Category banner | 1.2 MB | 420 KB | **65%** |
| Thumbnail | 85 KB | 32 KB | **62%** |

**Typical results:** 50-70% file size reduction with minimal quality loss.

### Page Speed Impact:

- ⚡ Faster page loads (less data to download)
- 📱 Better mobile performance
- 🚀 Improved Google PageSpeed score
- 💰 Reduced bandwidth costs

---

## 🛠️ Troubleshooting

### Images not converting?

**Check:**
1. PHP has GD Library with WebP support or ImageMagick installed
2. Module is enabled in configuration
3. "Auto-convert on upload" is enabled
4. Write permissions on `/img/` directory

**Test conversion support:**
```php
php -r "echo (function_exists('imagewebp') ? 'GD WebP: YES' : 'GD WebP: NO') . PHP_EOL;"
php -r "echo (extension_loaded('imagick') ? 'Imagick: YES' : 'Imagick: NO') . PHP_EOL;"
```

### WebP images not showing?

**Check:**
1. Browser supports WebP (see compatibility table above)
2. WebP files were generated (check `/img/p/` directory)
3. JavaScript is enabled (for automatic detection)
4. Cache is cleared (browser + PrestaShop)

### Batch conversion takes too long?

- Normal for large stores (10,000+ images)
- Process runs in PHP timeout window
- Consider running conversion via CLI if timeout occurs

---

## 📞 Support

**Developed by:** [Justidea Agency](https://justidea.pl)
**License:** AFL-3.0
**Version:** 1.0.0

For support or custom development, contact Justidea Agency.

---

## 📄 License

This module is released under the [Academic Free License 3.0](https://opensource.org/licenses/AFL-3.0).

---

## 🔄 Changelog

### Version 1.0.0 (2025-01-17)
- ✨ Initial release
- ✅ Automatic WebP conversion on upload
- ✅ Batch conversion for existing images
- ✅ Configurable quality settings
- ✅ Smarty function and modifier
- ✅ Browser detection and fallback
- ✅ Statistics and monitoring
- ✅ Full Hummingbird theme compatibility

---

## 🤝 Contributing

Contributions are welcome! This module is part of the Hummingbird theme customization for Justidea Agency.

---

**Made with ❤️ by Justidea Agency**
