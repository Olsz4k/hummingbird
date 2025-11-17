<?php
/**
 * Justidea WebP Converter Module
 *
 * @author    Justidea Agency
 * @copyright 2025 Justidea Agency
 * @license   AFL-3.0
 * @version   1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'justidea_webpconverter/classes/WebPConverter.php';

class Justidea_WebPConverter extends Module
{
    public function __construct()
    {
        $this->name = 'justidea_webpconverter';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Justidea Agency';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Justidea WebP Converter');
        $this->description = $this->l('Automatically converts product images to WebP format for better performance.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    /**
     * Install module
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('actionAfterImageUpload')
            && $this->registerHook('actionWatermark')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionObjectImageAddAfter')
            && $this->registerHook('actionObjectImageUpdateAfter')
            && $this->installConfiguration();
    }

    /**
     * Uninstall module
     */
    public function uninstall()
    {
        return $this->uninstallConfiguration()
            && parent::uninstall();
    }

    /**
     * Install default configuration
     */
    private function installConfiguration()
    {
        return Configuration::updateValue('JUSTIDEA_WEBP_ENABLED', 1)
            && Configuration::updateValue('JUSTIDEA_WEBP_QUALITY', 80)
            && Configuration::updateValue('JUSTIDEA_WEBP_AUTO_CONVERT', 1)
            && Configuration::updateValue('JUSTIDEA_WEBP_KEEP_ORIGINAL', 1)
            && Configuration::updateValue('JUSTIDEA_WEBP_CONVERTER_METHOD', 'auto')
            && Configuration::updateValue('JUSTIDEA_WEBP_STATS_CONVERTED', 0)
            && Configuration::updateValue('JUSTIDEA_WEBP_STATS_SAVED_BYTES', 0);
    }

    /**
     * Uninstall configuration
     */
    private function uninstallConfiguration()
    {
        return Configuration::deleteByName('JUSTIDEA_WEBP_ENABLED')
            && Configuration::deleteByName('JUSTIDEA_WEBP_QUALITY')
            && Configuration::deleteByName('JUSTIDEA_WEBP_AUTO_CONVERT')
            && Configuration::deleteByName('JUSTIDEA_WEBP_KEEP_ORIGINAL')
            && Configuration::deleteByName('JUSTIDEA_WEBP_CONVERTER_METHOD')
            && Configuration::deleteByName('JUSTIDEA_WEBP_STATS_CONVERTED')
            && Configuration::deleteByName('JUSTIDEA_WEBP_STATS_SAVED_BYTES');
    }

    /**
     * Module configuration page
     */
    public function getContent()
    {
        $output = '';

        // Process form submission
        if (Tools::isSubmit('submit' . $this->name)) {
            $output .= $this->processConfiguration();
        }

        // Process batch conversion
        if (Tools::isSubmit('batchConvert')) {
            $output .= $this->processBatchConversion();
        }

        // Display stats
        $output .= $this->displayStats();

        // Display configuration form
        $output .= $this->displayConfigurationForm();

        return $output;
    }

    /**
     * Process configuration form
     */
    private function processConfiguration()
    {
        $enabled = (int) Tools::getValue('JUSTIDEA_WEBP_ENABLED');
        $quality = (int) Tools::getValue('JUSTIDEA_WEBP_QUALITY');
        $autoConvert = (int) Tools::getValue('JUSTIDEA_WEBP_AUTO_CONVERT');
        $keepOriginal = (int) Tools::getValue('JUSTIDEA_WEBP_KEEP_ORIGINAL');
        $method = Tools::getValue('JUSTIDEA_WEBP_CONVERTER_METHOD');

        // Validate quality (60-100)
        if ($quality < 60 || $quality > 100) {
            return $this->displayError($this->l('Quality must be between 60 and 100'));
        }

        Configuration::updateValue('JUSTIDEA_WEBP_ENABLED', $enabled);
        Configuration::updateValue('JUSTIDEA_WEBP_QUALITY', $quality);
        Configuration::updateValue('JUSTIDEA_WEBP_AUTO_CONVERT', $autoConvert);
        Configuration::updateValue('JUSTIDEA_WEBP_KEEP_ORIGINAL', $keepOriginal);
        Configuration::updateValue('JUSTIDEA_WEBP_CONVERTER_METHOD', $method);

        return $this->displayConfirmation($this->l('Settings updated successfully'));
    }

    /**
     * Display stats
     */
    private function displayStats()
    {
        $converted = (int) Configuration::get('JUSTIDEA_WEBP_STATS_CONVERTED');
        $savedBytes = (int) Configuration::get('JUSTIDEA_WEBP_STATS_SAVED_BYTES');
        $savedMB = round($savedBytes / 1024 / 1024, 2);

        $this->context->smarty->assign([
            'total_converted' => $converted,
            'total_saved_mb' => $savedMB,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/stats.tpl');
    }

    /**
     * Display configuration form
     */
    private function displayConfigurationForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('WebP Converter Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable WebP conversion'),
                        'name' => 'JUSTIDEA_WEBP_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('WebP Quality'),
                        'name' => 'JUSTIDEA_WEBP_QUALITY',
                        'desc' => $this->l('Quality level (60-100). Higher = better quality but larger file size.'),
                        'class' => 'fixed-width-sm',
                        'suffix' => '%',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Auto-convert on upload'),
                        'name' => 'JUSTIDEA_WEBP_AUTO_CONVERT',
                        'desc' => $this->l('Automatically convert images to WebP when uploaded.'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'auto_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'auto_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Keep original images'),
                        'name' => 'JUSTIDEA_WEBP_KEEP_ORIGINAL',
                        'desc' => $this->l('Keep original JPG/PNG files as fallback for old browsers.'),
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'keep_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'keep_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Conversion method'),
                        'name' => 'JUSTIDEA_WEBP_CONVERTER_METHOD',
                        'desc' => $this->l('Select conversion library (auto = automatic detection).'),
                        'options' => [
                            'query' => [
                                ['id' => 'auto', 'name' => $this->l('Auto (recommended)')],
                                ['id' => 'gd', 'name' => $this->l('GD Library')],
                                ['id' => 'imagick', 'name' => $this->l('ImageMagick')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save Settings'),
                ],
                'buttons' => [
                    [
                        'type' => 'submit',
                        'name' => 'batchConvert',
                        'title' => $this->l('Convert All Existing Images'),
                        'icon' => 'process-icon-refresh',
                        'class' => 'btn btn-warning pull-right',
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fieldsForm]);
    }

    /**
     * Get config values
     */
    public function getConfigFieldsValues()
    {
        return [
            'JUSTIDEA_WEBP_ENABLED' => Configuration::get('JUSTIDEA_WEBP_ENABLED'),
            'JUSTIDEA_WEBP_QUALITY' => Configuration::get('JUSTIDEA_WEBP_QUALITY'),
            'JUSTIDEA_WEBP_AUTO_CONVERT' => Configuration::get('JUSTIDEA_WEBP_AUTO_CONVERT'),
            'JUSTIDEA_WEBP_KEEP_ORIGINAL' => Configuration::get('JUSTIDEA_WEBP_KEEP_ORIGINAL'),
            'JUSTIDEA_WEBP_CONVERTER_METHOD' => Configuration::get('JUSTIDEA_WEBP_CONVERTER_METHOD'),
        ];
    }

    /**
     * Process batch conversion
     */
    private function processBatchConversion()
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
            return $this->displayError($this->l('WebP conversion is disabled'));
        }

        $converter = new WebPConverter();
        $imageTypes = ['p', 'c', 'm', 's']; // products, categories, manufacturers, suppliers
        $totalConverted = 0;

        foreach ($imageTypes as $type) {
            $path = _PS_IMG_DIR_ . $type . '/';
            if (file_exists($path)) {
                $result = $converter->convertDirectory(
                    $path,
                    (int) Configuration::get('JUSTIDEA_WEBP_QUALITY')
                );
                $totalConverted += $result['converted'];
            }
        }

        // Update stats
        $currentConverted = (int) Configuration::get('JUSTIDEA_WEBP_STATS_CONVERTED');
        Configuration::updateValue('JUSTIDEA_WEBP_STATS_CONVERTED', $currentConverted + $totalConverted);

        return $this->displayConfirmation(
            sprintf($this->l('%d images converted to WebP successfully'), $totalConverted)
        );
    }

    /**
     * Hook: After image upload
     */
    public function hookActionAfterImageUpload($params)
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED') || !Configuration::get('JUSTIDEA_WEBP_AUTO_CONVERT')) {
            return;
        }

        if (isset($params['id_image']) && isset($params['image'])) {
            $this->convertProductImage($params['id_image']);
        }
    }

    /**
     * Hook: After image add
     */
    public function hookActionObjectImageAddAfter($params)
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED') || !Configuration::get('JUSTIDEA_WEBP_AUTO_CONVERT')) {
            return;
        }

        if (isset($params['object'])) {
            $image = $params['object'];
            if ($image instanceof Image) {
                $this->convertProductImage($image->id);
            }
        }
    }

    /**
     * Hook: After image update
     */
    public function hookActionObjectImageUpdateAfter($params)
    {
        $this->hookActionObjectImageAddAfter($params);
    }

    /**
     * Hook: After watermark
     */
    public function hookActionWatermark($params)
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED') || !Configuration::get('JUSTIDEA_WEBP_AUTO_CONVERT')) {
            return;
        }

        if (isset($params['id_image']) && isset($params['id_product'])) {
            $this->convertProductImage($params['id_image']);
        }
    }

    /**
     * Hook: Display header (add preload for WebP)
     */
    public function hookDisplayHeader()
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
            return;
        }

        // Register Smarty plugins
        $this->registerSmartyPlugins();

        // Add WebP detection script
        $this->context->controller->addJS($this->_path . 'views/js/webp-detect.js');
    }

    /**
     * Register Smarty plugins
     */
    private function registerSmartyPlugins()
    {
        $smartyPluginsPath = _PS_MODULE_DIR_ . $this->name . '/smarty/';

        // Register function: webp_picture
        if (file_exists($smartyPluginsPath . 'function.webp_picture.php')) {
            require_once $smartyPluginsPath . 'function.webp_picture.php';
            $this->context->smarty->registerPlugin(
                'function',
                'webp_picture',
                'smarty_function_webp_picture'
            );
        }

        // Register modifier: webp
        if (file_exists($smartyPluginsPath . 'modifier.webp.php')) {
            require_once $smartyPluginsPath . 'modifier.webp.php';
            $this->context->smarty->registerPlugin(
                'modifier',
                'webp',
                'smarty_modifier_webp'
            );
        }
    }

    /**
     * Convert product image to WebP
     */
    private function convertProductImage($idImage)
    {
        $converter = new WebPConverter();
        $image = new Image($idImage);
        $imageTypes = ImageType::getImagesTypes('products');

        foreach ($imageTypes as $imageType) {
            $imagePath = _PS_PROD_IMG_DIR_ . $image->getImgPath() . '-' . $imageType['name'] . '.jpg';

            if (file_exists($imagePath)) {
                $result = $converter->convertToWebP(
                    $imagePath,
                    (int) Configuration::get('JUSTIDEA_WEBP_QUALITY')
                );

                if ($result['success']) {
                    // Update stats
                    $savedBytes = (int) Configuration::get('JUSTIDEA_WEBP_STATS_SAVED_BYTES');
                    Configuration::updateValue('JUSTIDEA_WEBP_STATS_SAVED_BYTES', $savedBytes + $result['saved_bytes']);

                    $converted = (int) Configuration::get('JUSTIDEA_WEBP_STATS_CONVERTED');
                    Configuration::updateValue('JUSTIDEA_WEBP_STATS_CONVERTED', $converted + 1);
                }
            }
        }
    }

    /**
     * Register Smarty modifiers
     */
    public function smartyRegisterModifiers()
    {
        smartyRegisterFunction(
            $this->context->smarty,
            'modifier',
            'webp',
            [$this, 'smartyModifierWebP']
        );
    }

    /**
     * Smarty modifier: Convert image URL to WebP
     */
    public function smartyModifierWebP($imageUrl)
    {
        if (!Configuration::get('JUSTIDEA_WEBP_ENABLED')) {
            return $imageUrl;
        }

        $pathInfo = pathinfo($imageUrl);
        $webpUrl = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        // Check if WebP version exists
        $webpPath = _PS_ROOT_DIR_ . parse_url($webpUrl, PHP_URL_PATH);

        if (file_exists($webpPath)) {
            return $webpUrl;
        }

        return $imageUrl;
    }
}
