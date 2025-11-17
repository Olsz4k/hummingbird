# Changelog

All notable changes to the Justidea WebP Converter module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.0] - 2025-01-17

### Changed
- 🔧 **PrestaShop 8.0+ Compatibility**: Updated minimum version from 9.0.0 to 8.0.0
- 🔧 **Modern PHP Type Hints**: Added strict type hints and return types (PHP 7.4+)
  - All public and private methods now have proper type declarations
  - Improved IDE autocompletion and type safety
  - Better error detection during development
- 🔧 **Enhanced Documentation**: Improved PHPDoc blocks with detailed parameter and return type annotations
- 🔧 **Code Quality**: Applied PSR-12 coding standards
  - Used `public const` instead of `const` for class constants
  - Proper type declarations on class properties
  - Consistent method signature formatting

### Technical Improvements
- ✅ Backward compatible with PrestaShop 8.0, 8.1, 9.0+
- ✅ PHP 7.4+ typed properties for better performance
- ✅ Nullable types (`?int`, `?string`) for optional parameters
- ✅ Array type annotations (`array<string, mixed>`) for better documentation
- ✅ Void return types for methods that don't return values
- ✅ Strict boolean, integer, and string type enforcement

### Code Examples
```php
// Before (1.0.0)
public function install()
{
    return parent::install() && $this->registerHook('displayHeader');
}

// After (1.1.0)
public function install(): bool
{
    return parent::install() && $this->registerHook('displayHeader');
}
```

---

## [1.0.0] - 2025-01-17

### Added
- ✨ Initial release of Justidea WebP Converter
- ✅ Automatic WebP conversion on image upload
- ✅ Batch conversion for all existing images (products, categories, manufacturers, suppliers)
- ✅ Configurable WebP quality (60-100%)
- ✅ Option to keep original files as fallback
- ✅ Multiple conversion methods (GD Library, ImageMagick, Auto)
- ✅ Smarty function `{webp_picture}` for generating picture tags
- ✅ Smarty modifier `|webp` for URL conversion
- ✅ Automatic browser WebP support detection
- ✅ Statistics dashboard (images converted, space saved)
- ✅ System information panel
- ✅ Admin configuration panel with HelperForm
- ✅ PrestaShop 9.0+ compatibility
- ✅ Full Hummingbird theme compatibility
- ✅ Lazy loading support
- ✅ Comprehensive documentation (README, INSTALLATION, EXAMPLES)
- ✅ AFL-3.0 license

### Hooks Implemented
- `actionAfterImageUpload` - Convert images on upload
- `actionWatermark` - Convert after watermark application
- `displayHeader` - Register Smarty plugins and add detection script
- `actionObjectImageAddAfter` - Convert when Image object is added
- `actionObjectImageUpdateAfter` - Convert when Image object is updated

### Files Structure
```
justidea_webpconverter/
├── justidea_webpconverter.php  - Main module file
├── classes/WebPConverter.php    - Conversion engine
├── smarty/                      - Smarty plugins
│   ├── function.webp_picture.php
│   └── modifier.webp.php
├── views/
│   ├── templates/admin/stats.tpl
│   ├── js/webp-detect.js
│   └── css/admin.css
├── config.xml
├── LICENSE
├── README.md
├── INSTALLATION.md
├── EXAMPLES.md
└── CHANGELOG.md
```

### Performance
- Average file size reduction: 50-70%
- No performance impact on frontend (lazy loading + browser detection)
- Efficient batch processing for large image libraries

### Browser Support
- ✅ Chrome (all versions)
- ✅ Firefox 65+
- ✅ Safari 14+
- ✅ Edge (all versions)
- ✅ Opera (all versions)
- ⚠️ IE 11 (fallback to original images)

---

## [Unreleased]

### Planned Features
- [ ] CLI tool for batch conversion outside PHP timeout limits
- [ ] AVIF format support (next-gen image format)
- [ ] Image compression before WebP conversion
- [ ] CDN integration
- [ ] Advanced statistics (conversion history, performance graphs)
- [ ] Multi-language support for admin panel
- [ ] Scheduled batch conversion (cron job)
- [ ] Image optimization recommendations
- [ ] Integration with popular image optimization services

---

## Support

For bug reports, feature requests, or support:
- Contact: Justidea Agency
- Module Version: 1.0.0
- PrestaShop Compatibility: 9.0.0+

---

**Legend:**
- ✨ New feature
- ✅ Added
- 🐛 Bug fix
- 🔧 Changed
- ⚠️ Deprecated
- ❌ Removed
- 🔒 Security fix
