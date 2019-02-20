<?php
/**
* 2007-2019 PrestaShop.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class Customerreviews extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'customerreviews';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Dark-Side.pro';
        $this->need_instance = 1;

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Customer Reviews');
        $this->description = $this->l('Now customer can add reviews after buy product. ');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    private function createTab()
    {
        $response = true;
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = 'AdminDarkSideMenu';
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = 'Dark-Side.pro';
            }
            $parentTab->id_parent = 0;
            $parentTab->module = '';
            $response &= $parentTab->add();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = 'AdminDarkSideMenuSecond';
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = 'Dark-Side Config';
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = '';
            $response &= $parentTab_2->add();
        }
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdministratorCustomerReviews';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = 'Customer reviews';
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    private function tabRem()
    {
        $id_tab = Tab::getIdFromClassName('AdministratorCustomerReviews');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $tabCount_2 = Tab::getNbTabs($parentTab_2ID);
            if ($tabCount_2 == 0) {
                $parentTab_2 = new Tab($parentTab_2ID);
                $parentTab_2->delete();
            }
        }
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $tabCount = Tab::getNbTabs($parentTabID);
            if ($tabCount == 0) {
                $parentTab = new Tab($parentTabID);
                $parentTab->delete();
            }
        }

        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        Configuration::updateValue('CUSTOMERREVIEWS_HOMESLIDER', false);
        Configuration::updateValue('CUSTOMERREVIEWS_TIMEAFTER', 3);
        Configuration::updateValue('CUSTOMERREVIEWS_MUSTAPROVED', true);

        $this->createTab();

        include dirname(__FILE__).'/sql/install.php';

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('registerGDPRConsent') &&
            $this->registerHook('actionDeleteGDPRCustomer') &&
            $this->registerHook('actionExportGDPRData') &&
            $this->registerHook('actionOrderStatusPostUpdate');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CUSTOMERREVIEWS_HOMESLIDER');
        Configuration::deleteByName('CUSTOMERREVIEWS_TIMEAFTER');
        Configuration::deleteByName('CUSTOMERREVIEWS_MUSTAPROVED');

        $this->tabRem();

        include dirname(__FILE__).'/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitCustomerreviewsModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustomerreviewsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Home slider'),
                        'name' => 'CUSTOMERREVIEWS_HOMESLIDER',
                        'is_bool' => true,
                        'desc' => $this->l('Show carousel with customer reviews on home page'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Comment aproved'),
                        'name' => 'CUSTOMERREVIEWS_MUSTAPROVED',
                        'is_bool' => true,
                        'desc' => $this->l('Do the comments must be approved?'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'number',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('How long after the purchase can the customer add a comment?'),
                        'name' => 'CUSTOMERREVIEWS_TIMEAFTER',
                        'label' => $this->l('Time after buy'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function approveComment($commentid)
    {
    }

    protected function deleteComment($commentid)
    {
        $sql = 'DELETE * FROM '._DB_PREFIX_.'customerreviews WHERE id_comment = '.$commentid;
        $sql = Db::getInstance()->execute($sql);
    }

    protected function getAllComments()
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT * FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE cr.reviewlang = '.$currentlang.'
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getSliderComments()
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, pr.id_product, od.product_name
         FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE cr.slider = 1 AND cr.reviewlang = '.$currentlang.'
        ORDER BY cr.sliderweight
        ';
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getProductComments($productid)
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, od.product_name
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE  pr.id_product = '.$productid.' AND cr.visible = 1 AND cr.reviewlang = '.$currentlang.'
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function ifProductCommentsIsNeeded($productid)
    {
        $currentlang = $this->context->language->id;

        

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, od.product_name, od.id_order_detail
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE  pr.id_product = '.$productid.' AND cr.currentdata = 1 AND cr.reviewlang = '.$currentlang.'
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function insertProductComment($id_order_detail)
    {
        $currentlang = $this->context->language->id;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `timeadded` = now(),
        `stars` = '.$stars.',
        `title` = '.$title.',
        `content` = '.$content.',
        `currentdata` = 0
        WHERE 
        `id_order_detail` = '.$id_order_detail;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        WHERE  
        pr.id_product = 
        (
        SELECT DISTINCT pr.id_product 
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE  `id_order_detail` = '.$id_order_detail.'
        )
        AND ord.id_customer = '.$currentcustomer.'
        SET 
        `currentdata` = 0';
    }

    protected function addProductComment($id_order, $id_user, $timetowrite) //to ma być uruchomione gdy jest opłata
    {

        $currentlang = $this->context->language->id;
        $sql = '
        INSERT INTO '._DB_PREFIX_.'customerreviews 
        (
        `id_order_detail`,
        `timetowrite`,
        `visible`,
        `visibleweight`,
        `deleted`,
        `slider`,
        `sliderweight`,
        `currentdata`
        `reviewlang`
        )
        SELECT
        id_order_detail,
        '.$timetowrite.',
        0,
        0,
        0,
        0,
        0,
        1,
        '.$currentlang.'
        FROM
        '._DB_PREFIX_.'order_detail
        WHERE
        id_order = '.$id_order.'



        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;


        /*
                INSERT INTO '._DB_PREFIX_.'customerreviews 
        (
        `id_order_detail`,
        `timetowrite`,
        `visible`,
        `visibleweight`,
        `deleted`,
        `slider`,
        `sliderweight`,
        `currentdata`
        `reviewlang`
        )
        VALUES
        (
        '.$orderdetail.',
        '.$timetowrite.',
        0,
        0,
        0,
        0,
        0,
        1,
        '.$currentlang.'
        )
        ';*/

    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CUSTOMERREVIEWS_HOMESLIDER' => Configuration::get('CUSTOMERREVIEWS_HOMESLIDER', false),
            'CUSTOMERREVIEWS_TIMEAFTER' => Configuration::get('CUSTOMERREVIEWS_TIMEAFTER', null),
            'CUSTOMERREVIEWS_MUSTAPROVED' => Configuration::get('CUSTOMERREVIEWS_MUSTAPROVED', false),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (Tools::getValue('controller') == 'index') {
            $this->context->controller->addJS($this->_path.'/views/js/slider.js');
            $this->context->controller->addCSS($this->_path.'/views/css/slider.css');
        } elseif (Tools::getValue('controller') == 'product') {
            $this->context->controller->addJS($this->_path.'/views/js/product.js');
            $this->context->controller->addCSS($this->_path.'/views/css/product.css');
        }
    }

    public function hookdisplayProductExtraContent($params)
    {
        $title = $this->l('Opinie o produkcie');
        $productid = (int) Tools::getValue('id_product');
        $reviews = $this->getProductComments($productid);
        $customer = Context::getContext()->customer->isLogged();
        var_dump($productid);
        $isneed = $this->ifProductCommentsIsNeeded($productid);
        var_dump($isneed);

        $this->context->smarty->assign('customer', $customer);
        $this->context->smarty->assign('reviews', $reviews);
        $content = $this->display(__FILE__, 'views/templates/hook/hookDisplayProuctTab.tpl');
        $array = array();
        $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
            ->setTitle($title)
            ->setContent($content);

        return $array;
    }

    public function hookDisplayHome()
    {
        $comments = $this->getSliderComments();
        $slider = Configuration::get('CUSTOMERREVIEWS_HOMESLIDER');

        if ($slider == true) {
            $this->context->smarty->assign('slidercomments', $comments);
            $output = $this->display(__FILE__, 'views/templates/hook/hookDisplayHome.tpl');

            return $output;
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
    }

    public function hookActionExportGDPRData($customer)
    {
    }

    public function hookActionOrderStatusPostUpdate()
    {
    }
}
