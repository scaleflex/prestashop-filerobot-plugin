<?php
/**
 *  2022 Scaleflex
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2022 Scaleflex
 *  @author Tung Dang <tung.dang@scaleflex.com>
 *  @copyright Scaleflex
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Filerobot extends Module
{
    public function __construct()
    {
        $this->name = 'filerobot';
        $this->tab = 'other';
        $this->version = '1.0.0';
        $this->author = 'Scaleflex';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
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
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
                $this->initConfigs() &&
                $this->registerHook('displayAdminAfterHeader');
    }

    public function hookDisplayAdminAfterHeader() {

    }

    /**
     * Uninstall the application
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->deleteConfigs();
    }

    /** Get Configuration */
    public function getConfigs($configName = null)
    {
        $configs =  [
            'ciActivation'                 => (bool)   Configuration::get('FR_ACTIVATION'),
            'frToken'                      => (string) Configuration::get('FR_TOKEN'),
            'frActivation'                 => (string) Configuration::get('FR_SEC_TEMPLATE'),
            'frUploadDir'                  => (string) Configuration::get('FR_UPLOAD_DIR'),

        ];

        if ($configName) {
            return $configs[$configName];
        }

        return $configs;
    }



    /**
     * Admin Config Page
     * @return string
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $submitStatus = true;
            $frToken        =  Tools::getValue('FR_TOKEN');
            $frSecTemplate  =  Tools::getValue('FR_SEC_TEMPLATE');

            if (empty($frToken)) {
                Configuration::updateValue('FR_ACTIVATION', false);
                if (Tools::getValue('FR_ACTIVATION')) {
                    $submitStatus = false;
                }
            } else {

            }

            Configuration::updateValue('FR_TOKEN',          Tools::getValue('FR_TOKEN'));
            Configuration::updateValue('FR_SEC_TEMPLATE',   Tools::getValue('FR_SEC_TEMPLATE'));
            Configuration::updateValue('FR_UPLOAD_DIR',     Tools::getValue('FR_UPLOAD_DIR'));

            $output .= $submitStatus ?  $this->displayConfirmation($this->trans('Form submitted successfully')) :
                $this->displayError($this->trans('You can not active Filerobot because your Token and Secutirity template id are empty'));
        }

        return $output . $this->buildForm();
    }

    /**
     * Build Admin Form
     * @return string
     */
    public function buildForm()
    {
        $frActivation                   = (bool)   Configuration::get('FR_ACTIVATION');
        $frToken                        = (string) Configuration::get('FR_TOKEN');
        $frSecTemplate                  = (string) Configuration::get('FR_SEC_TEMPLATE');
        $frUploadDir                    = (string) Configuration::get('FR_UPLOAD_DIR');

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Filerobot Integration'),
                ],
                'input' => [
                    [
                        'type'          => 'switch',
                        'label'         => $this->l('Activation'),
                        'desc'          => $this->l('Enable/Disable the Module'),
                        'name'          => 'FR_ACTIVATION',
                        'size'          => 20,
                        'is_bool'       => true,
                        'values'        => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global')
                            ]
                        ],
                    ],
                    [
                        'type'          => 'text',
                        'label'         => $this->l('Token'),
                        'desc'          => $this->l('Enter token for example fmpsaXXX'),
                        'name'          => 'FR_TOKEN',
                        'size'          => 20
                    ],
                    [
                        'type'          => 'text',
                        'label'         => $this->l('Security template identifier'),
                        'desc'          => $this->l('Enter token for example: SECU_234233_XXXX'),
                        'name'          => 'FR_SEC_TEMPLATE',
                        'size'          => 20
                    ],
                    [
                        'type'          => 'text',
                        'label'         => $this->l('Filerobot upload directory'),
                        'desc'          => $this->l('Default upload directory, the directory where assets are stored, default /prestashop'),
                        'name'          => 'FR_UPLOAD_DIR',
                        'size'          => 20
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

        $helper->fields_value['FR_ACTIVATION']              = $frActivation;
        $helper->fields_value['FR_TOKEN']                   = $frToken;
        $helper->fields_value['FR_SEC_TEMPLATE']            = $frSecTemplate;
        $helper->fields_value['FR_UPLOAD_DIR']              = $frUploadDir;

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
     * @return bool
     */
    private function initConfigs()
    {
        return  Configuration::updateValue('FR_ACTIVATION', "0") &&
                Configuration::updateValue('FR_TOKEN', "") &&
                Configuration::updateValue('FR_SEC_TEMPLATE', "") &&
                Configuration::updateValue('FR_UPLOAD_DIR', "/prestashop");
    }

    /**
     * Delete config if uninstalled
     * @return bool
     */
    private function deleteConfigs()
    {
        return  Configuration::deleteByName('FR_ACTIVATION') &&
                Configuration::deleteByName('FR_TOKEN') &&
                Configuration::deleteByName('FR_SEC_TEMPLATE') &&
                Configuration::deleteByName('FR_UPLOAD_DIR');
    }
}
