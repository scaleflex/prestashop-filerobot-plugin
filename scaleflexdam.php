<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

class ScaleflexDam extends Module
{
    public function __construct()
    {
        $this->name = 'scaleflexdam';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Scaleflex';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Scaleflex DAM');
        $this->description = $this->l('Scaleflex DAM normalizes, resizes, optimizes and distributes your images rocket fast around the world.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->initConfigs() &&
            $this->sqlInstall() &&
            $this->registerHook('displayAdminAfterHeader') &&
            $this->registerHook('actionAdminControllerSetMedia');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->deleteConfigs();
    }

    /**
     * Init configs if install
     *
     * @return bool
     */
    private function initConfigs()
    {
        return Configuration::updateValue('SFXDAM_ACTIVATION', '0')
            && Configuration::updateValue('SFXDAM_TOKEN', '')
            && Configuration::updateValue('SFXDAM_SEC_TEMPLATE', '')
            && Configuration::updateValue('SFXDAM_UPLOAD_DIR', '/')
            && Configuration::updateValue('SFXDAM_ALLOW_TRANSFORMATIONS', '1')
            && Configuration::updateValue('SFXDAM_ALLOW_AI_ASSET_SEARCH', '0');
    }

    /**
     * Delete config if uninstalled
     *
     * @return bool
     */
    private function deleteConfigs()
    {
        return Configuration::deleteByName('SFXDAM_ACTIVATION')
            && Configuration::deleteByName('SFXDAM_TOKEN')
            && Configuration::deleteByName('SFXDAM_SEC_TEMPLATE')
            && Configuration::deleteByName('SFXDAM_UPLOAD_DIR')
            && Configuration::deleteByName('SFXDAM_ALLOW_TRANSFORMATIONS')
            && Configuration::deleteByName('SFXDAM_ALLOW_AI_ASSET_SEARCH');
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

    /** Get Configuration */
    private function getConfigs($configName = null)
    {
        $configs = [
            'sfxDamActivation' => (bool) Configuration::get('SFXDAM_ACTIVATION'),
            'sfxDamToken' => (string) Configuration::get('SFXDAM_TOKEN'),
            'sfxDamSecTemplate' => (string) Configuration::get('SFXDAM_SEC_TEMPLATE'),
            'sfxDamUploadDir' => (string) Configuration::get('SFXDAM_UPLOAD_DIR'),
            'sfxDamAllowTransformations' => (bool) Configuration::get('SFXDAM_ALLOW_TRANSFORMATIONS'),
            'sfxDamAllowAiAssetSearch' => (bool) Configuration::get('SFXDAM_ALLOW_AI_ASSET_SEARCH'),
        ];

        if ($configName) {
            return $configs[$configName];
        }

        return $configs;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitScaleflexDamModule')) {
            $sfxDamActivation = (bool) Tools::getValue('SFXDAM_ACTIVATION');
            $sfxDamToken = (string) Tools::getValue('SFXDAM_TOKEN');
            $sfxDamSecTemplate = (string) Tools::getValue('SFXDAM_SEC_TEMPLATE');
            $sfxDamUploadDir = (string) Tools::getValue('SFXDAM_UPLOAD_DIR');
            $sfxDamAllowTransformations = (bool) Tools::getValue('SFXDAM_ALLOW_TRANSFORMATIONS');
            $sfxDamAllowAiAssetSearch = (bool) Tools::getValue('SFXDAM_ALLOW_AI_ASSET_SEARCH');

            if (!$sfxDamToken || empty($sfxDamToken)) {
                $output .= $this->displayError($this->l('Invalid Configuration value for Token.'));
            } else {
                Configuration::updateValue('SFXDAM_ACTIVATION', $sfxDamActivation);
                Configuration::updateValue('SFXDAM_TOKEN', $sfxDamToken);
                Configuration::updateValue('SFXDAM_SEC_TEMPLATE', $sfxDamSecTemplate);
                Configuration::updateValue('SFXDAM_UPLOAD_DIR', $sfxDamUploadDir);
                Configuration::updateValue('SFXDAM_ALLOW_TRANSFORMATIONS', $sfxDamAllowTransformations);
                Configuration::updateValue('SFXDAM_ALLOW_AI_ASSET_SEARCH', $sfxDamAllowAiAssetSearch);

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitScaleflexDamModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activation'),
                        'name' => 'SFXDAM_ACTIVATION',
                        'is_bool' => true,
                        'desc' => $this->l('Enable Scaleflex DAM'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter your Scaleflex DAM Token'),
                        'name' => 'SFXDAM_TOKEN',
                        'label' => $this->l('Token'),
                        'required' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter your Security Template'),
                        'name' => 'SFXDAM_SEC_TEMPLATE',
                        'label' => $this->l('Security Template'),
                        'required' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'SFXDAM_UPLOAD_DIR',
                        'label' => $this->l('Upload Directory'),
                        'desc' => $this->l('The upload directory in your DAM (e.g. /prestashop/)'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Transformations'),
                        'name' => 'SFXDAM_ALLOW_TRANSFORMATIONS',
                        'is_bool' => true,
                        'desc' => $this->l('Allow usage of transformations before inserting'),
                        'values' => [
                            [
                                'id' => 'transformations_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'transformations_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('AI Asset Search'),
                        'name' => 'SFXDAM_ALLOW_AI_ASSET_SEARCH',
                        'is_bool' => true,
                        'desc' => $this->l('This option add in the DAM widget a switch allowing user to perform a AI Search on the DAM library. Attention: This option, once activated, will allow users to run AI searches in the library. It is available on the assumption that a Visual AI package is part of your subscription and that you have previously activated image embedding on your library and for uploads, and that you have enough AI credits to run some specific AI requests (eg. find similar). Please contact your administrator and our support team if you are unsure.'),
                        'values' => [
                            [
                                'id' => 'ai_asset_search_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'ai_asset_search_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        return [
            'SFXDAM_ACTIVATION' => Configuration::get('SFXDAM_ACTIVATION'),
            'SFXDAM_TOKEN' => Configuration::get('SFXDAM_TOKEN'),
            'SFXDAM_SEC_TEMPLATE' => Configuration::get('SFXDAM_SEC_TEMPLATE'),
            'SFXDAM_UPLOAD_DIR' => Configuration::get('SFXDAM_UPLOAD_DIR', '/'),
            'SFXDAM_ALLOW_TRANSFORMATIONS' => Configuration::get('SFXDAM_ALLOW_TRANSFORMATIONS'),
            'SFXDAM_ALLOW_AI_ASSET_SEARCH' => Configuration::get('SFXDAM_ALLOW_AI_ASSET_SEARCH'),
        ];
    }
    
    public function hookActionAdminControllerSetMedia()
    {
        if ($this->getConfigs('sfxDamActivation')) {
            $this->context->controller->addJS($this->_path . 'views/js/admin/scaleflex-dam.js');
            $this->context->controller->addJS($this->_path . 'views/js/tinymce/editor.js');
            $this->context->controller->addJS('https://scaleflex.cloudimg.io/v7/plugins/widget/v4/latest/scaleflex-widget.min.js');

            $this->context->controller->addCSS($this->_path . 'views/css/admin/scaleflex-dam.css');
            $this->context->controller->addCSS('https://scaleflex.cloudimg.io/v7/plugins/widget/v4/latest/scaleflex-widget.min.css');
        }
    }

    public function hookDisplayAdminAfterHeader()
    {
        if ($this->getConfigs('sfxDamActivation')) {
            $configs = $this->getConfigs();
            $configs['ajaxUrl'] = $this->context->link->getAdminLink('AdminScaleflexDam') . '&action=addImage&ajax=1';
            $this->context->smarty->assign($configs);
            
            return $this->display(__FILE__, 'views/templates/admin/modal.tpl');
        }
    }
}
