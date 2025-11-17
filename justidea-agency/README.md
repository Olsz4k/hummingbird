# Justidea Agency - Child Theme for Hummingbird

## Description

Justidea Agency is a child theme for the Hummingbird theme in PrestaShop 9.0+. This theme allows you to customize the appearance and functionality of your PrestaShop store while maintaining compatibility with the parent Hummingbird theme.

## Features

- ✅ Based on Hummingbird theme (PrestaShop 9.0+)
- ✅ Bootstrap 5.3.3 framework
- ✅ Fully customizable CSS
- ✅ Ready-to-use template structure
- ✅ Easy to extend and maintain
- ✅ Preserves parent theme updates

## Requirements

- PrestaShop 9.0.0 or higher
- Hummingbird parent theme installed
- PHP 8.1 or higher

## Installation

### Step 1: Install Parent Theme (Hummingbird)

1. If not already installed, download and install the Hummingbird theme
2. Place it in your PrestaShop `/themes/` directory
3. Ensure the folder is named `hummingbird`

### Step 2: Install Child Theme

1. Copy the `justidea-agency` folder to your PrestaShop `/themes/` directory
2. Your structure should look like:
   ```
   /themes/
   ├── hummingbird/          (parent theme)
   └── justidea-agency/      (child theme)
   ```

### Step 3: Activate Theme

1. Log in to your PrestaShop Back Office
2. Go to **Design > Theme & Logo**
3. Find "Justidea Agency" in the list
4. Click **Use this theme**

## Directory Structure

```
justidea-agency/
├── assets/
│   ├── css/
│   │   └── custom.css       # Your custom styles
│   └── js/                  # Custom JavaScript files
├── config/
│   └── theme.yml            # Theme configuration
├── modules/                 # Module overrides
├── templates/               # Template overrides
│   ├── catalog/            # Category, product pages
│   ├── checkout/           # Cart, checkout pages
│   ├── cms/                # CMS pages
│   ├── customer/           # Customer account pages
│   └── errors/             # Error pages
├── preview.png             # Theme preview image
└── README.md               # This file
```

## Customization Guide

### CSS Customization

Edit `/assets/css/custom.css` to add your custom styles. The file includes:

- CSS variables for easy color management
- Organized sections (header, navigation, products, footer, buttons)
- Responsive breakpoints
- Commented examples to get you started

### Template Overrides

To override a template from the parent theme:

1. Copy the template from `/themes/hummingbird/templates/`
2. Paste it in `/themes/justidea-agency/templates/` maintaining the same folder structure
3. Modify the template as needed

Example:
```bash
# Override product page template
cp themes/hummingbird/templates/catalog/product.tpl \
   themes/justidea-agency/templates/catalog/product.tpl
```

### Module Overrides

To override module templates:

1. Create folder: `/themes/justidea-agency/modules/{module-name}/views/templates/`
2. Copy template from the module
3. Modify as needed

### Theme Configuration

Edit `/config/theme.yml` to customize:

- Theme metadata (name, version, author)
- Product display settings
- Image sizes
- Module hooks
- Layout options

## Development Workflow

### 1. Make Changes

- Edit CSS in `assets/css/custom.css`
- Override templates in `templates/` folder
- Modify `config/theme.yml` for configuration changes

### 2. Clear Cache

After making changes, clear PrestaShop cache:

```bash
# From PrestaShop root directory
rm -rf var/cache/*
```

Or use the Back Office: **Advanced Parameters > Performance > Clear cache**

### 3. Test Changes

- Check your changes in the frontend
- Test on different devices (mobile, tablet, desktop)
- Verify compatibility with parent theme updates

## Best Practices

1. **Keep Parent Theme Updated**: Regular updates to Hummingbird won't affect your customizations
2. **Use Custom CSS**: Prefer `custom.css` over inline styles
3. **Override Only What You Need**: Don't copy templates unnecessarily
4. **Document Changes**: Comment your code to explain customizations
5. **Version Control**: Use Git to track your changes
6. **Backup Regularly**: Keep backups before major changes

## Common Customizations

### Change Primary Color

Edit `assets/css/custom.css`:

```css
:root {
  --primary-color: #your-color-here;
}
```

### Customize Header

Uncomment and modify the header section in `custom.css`:

```css
#header {
  background-color: var(--background-color);
  border-bottom: 1px solid var(--border-color);
}
```

### Override Homepage

Copy and modify:
```bash
cp themes/hummingbird/templates/index.tpl \
   themes/justidea-agency/templates/index.tpl
```

## Troubleshooting

### Theme Not Appearing

- Ensure `theme.yml` exists in `/config/`
- Verify parent theme name is correct: `parent: hummingbird`
- Check folder permissions

### Styles Not Applying

- Clear PrestaShop cache
- Check if `custom.css` is properly loaded
- Verify CSS syntax

### Templates Not Overriding

- Ensure folder structure matches parent theme exactly
- Clear cache after adding new templates
- Check file permissions

## Support

For issues and questions:

- Check PrestaShop documentation: https://docs.prestashop-project.org/
- Review Hummingbird theme documentation
- PrestaShop forums: https://www.prestashop.com/forums/

## License

This child theme follows the same license as the parent Hummingbird theme.

## Credits

- **Parent Theme**: Hummingbird by PrestaShop Team
- **Child Theme**: Justidea Agency
- **Framework**: Bootstrap 5.3.3

## Changelog

### Version 1.0.0
- Initial release
- Basic child theme structure
- Custom CSS framework
- Template override structure
- Documentation

---

**Note**: This is a child theme. Always keep the parent Hummingbird theme installed and updated.
