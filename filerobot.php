<?php
/**
 * MIT License
 *
 * Copyright (c) 2022 Scaleflex
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author   Scaleflex
 *  @copyright 2022 Scaleflex
 *  @license   LICENSE
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once _PS_MODULE_DIR_ . 'filerobot/classes/FilerobotImage.php';
use Scaleflex\PrestashopFilerobot\Adapter\FilerobotImageRetriever;

class Filerobot extends Module
{
    public function __construct()
    {
        $this->name = 'filerobot';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Scaleflex';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        $this->displayName = $this->l('Filerobot by Scaleflex');
        $this->description = $this->l('Filerobot stores, enrich, normalizes, resizes, optimizes and distributes your images rocket fast around the world.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        parent::__construct();
    }

    /**
     * Install the Module
     *
     * @return bool
     */
    public function install()
    {
        return parent::install()
            && $this->initConfigs()
            && $this->sqlInstall()
            && $this->registerHook('displayAdminAfterHeader')
            && $this->registerHook('actionPresentProduct')
            && $this->registerHook('actionPresentProductListing');
    }

    /**
     * @param $observerData
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    public function hookActionPresentProductListing($observerData)
    {
        $presentedProduct = $observerData['presentedProduct'];
        $this->changeImagesIfFilerobot($presentedProduct);
    }

    /**
     * @param $observerData
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    public function hookActionPresentProduct($observerData)
    {
        $presentedProduct = $observerData['presentedProduct'];
        $this->changeImagesIfFilerobot($presentedProduct);
    }

    /**
     * @param $presentedProduct
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    private function changeImagesIfFilerobot($presentedProduct)
    {
        if ($presentedProduct->cover) {
            $imageRetriever = new FilerobotImageRetriever($this->context->link);
            $image = $imageRetriever->getOneImage($presentedProduct->cover['id_image'], $this->context->language->id);
            if (!empty($image) && null !== $image[0]['url']) {
                $imageCover = $image[0];
                $cover = $imageRetriever->getImage(new Product(), $imageCover['id_image'], $imageCover['url']);
                $presentedProduct->cover = $cover;
            }
        }

        if (!empty($presentedProduct->images)) {
            $idProduct = $presentedProduct->id;
            $imageRetriever = new FilerobotImageRetriever($this->context->link);
            $images = $imageRetriever->getAllProductImages(['id_product' => $idProduct], $this->context->language);
            $presentedProduct->images = $images;
        }
    }

    /**
     * @return false|string
     */
    public function hookDisplayAdminAfterHeader()
    {
        if ($this->getConfigs('frActivation')) {
            $this->context->smarty->assign($this->getConfigs());

            return $this->display(__FILE__, 'views/templates/hook/filerobot.tpl');
        }
    }

    /**
     * Uninstall the application
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->deleteConfigs();
    }

    /** Get Configuration */
    public function getConfigs($configName = null)
    {
        $configs = [
            'frActivation' => (bool) Configuration::get('FR_ACTIVATION'),
            'frToken' => (string) Configuration::get('FR_TOKEN'),
            'frSecTemplate' => (string) Configuration::get('FR_SEC_TEMPLATE'),
            'frUploadDir' => (string) Configuration::get('FR_UPLOAD_DIR'),
        ];

        if ($configName) {
            return $configs[$configName];
        }

        return $configs;
    }

    /**
     * Admin Config Page
     *
     * @return string
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $submitStatus = true;
            $frToken = Tools::getValue('FR_TOKEN');
            $frSecTemplate = Tools::getValue('FR_SEC_TEMPLATE');

            if (empty($frToken) || empty($frSecTemplate)) {
                Configuration::updateValue('FR_ACTIVATION', false);
                if (Tools::getValue('FR_ACTIVATION')) {
                    $submitStatus = false;
                }
            } else {
                Configuration::updateValue('FR_ACTIVATION', Tools::getValue('FR_ACTIVATION'));
            }

            Configuration::updateValue('FR_TOKEN', Tools::getValue('FR_TOKEN'));
            Configuration::updateValue('FR_SEC_TEMPLATE', Tools::getValue('FR_SEC_TEMPLATE'));
            Configuration::updateValue('FR_UPLOAD_DIR', Tools::getValue('FR_UPLOAD_DIR'));

            $output .= $submitStatus ? $this->displayConfirmation($this->trans('Form submitted successfully')) :
                $this->displayError($this->trans('You can not active Filerobot because your Token and Secutirity template id are empty'));
        }

        return $output . $this->buildForm();
    }

    /**
     * Build Admin Form
     *
     * @return string
     */
    public function buildForm()
    {
        $frActivation = (bool) Configuration::get('FR_ACTIVATION');
        $frToken = (string) Configuration::get('FR_TOKEN');
        $frSecTemplate = (string) Configuration::get('FR_SEC_TEMPLATE');
        $frUploadDir = (string) Configuration::get('FR_UPLOAD_DIR');

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Filerobot Integration'),
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activation'),
                        'desc' => $this->l('Enable/Disable the Module'),
                        'name' => 'FR_ACTIVATION',
                        'size' => 20,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Token'),
                        'desc' => $this->l('Enter token for example fmpsaXXX'),
                        'name' => 'FR_TOKEN',
                        'size' => 20,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Security template identifier'),
                        'desc' => $this->l('Enter token for example: SECU_234233_XXXX'),
                        'name' => 'FR_SEC_TEMPLATE',
                        'size' => 20,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Filerobot upload directory'),
                        'desc' => $this->l('Default upload directory, the directory where assets are stored, default /prestashop'),
                        'name' => 'FR_UPLOAD_DIR',
                        'size' => 20,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['FR_ACTIVATION'] = $frActivation;
        $helper->fields_value['FR_TOKEN'] = $frToken;
        $helper->fields_value['FR_SEC_TEMPLATE'] = $frSecTemplate;
        $helper->fields_value['FR_UPLOAD_DIR'] = $frUploadDir;

        return $helper->generateForm([$form]);
    }

    /**
     * Using new translation system
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Init configs if install
     *
     * @return bool
     */
    private function initConfigs()
    {
        return Configuration::updateValue('FR_ACTIVATION', '0')
            && Configuration::updateValue('FR_TOKEN', '')
            && Configuration::updateValue('FR_SEC_TEMPLATE', '')
            && Configuration::updateValue('FR_UPLOAD_DIR', '/prestashop');
    }

    /**
     * Delete config if uninstalled
     *
     * @return bool
     */
    private function deleteConfigs()
    {
        return Configuration::deleteByName('FR_ACTIVATION')
            && Configuration::deleteByName('FR_TOKEN')
            && Configuration::deleteByName('FR_SEC_TEMPLATE')
            && Configuration::deleteByName('FR_UPLOAD_DIR');
    }

    /**
     * Add new column to table images
     */
    private function sqlInstall()
    {
        $sql = 'DESCRIBE ' . _DB_PREFIX_ . 'image';
        $columns = Db::getInstance()->executeS($sql);
        $found = false;
        foreach ($columns as $col) {
            if ('url' == $col['Field']) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'image` ADD `url` text DEFAULT NULL');
        }

        return true;
    }

    // ==== Support Function ==== //

    /**
     * @return FilerobotImage
     */
    public static function getFilerobotImageInstance()
    {
        return new FilerobotImage();
    }
}
