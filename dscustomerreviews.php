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
* to license@dark-side.pro so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.dark-side.pro for more information.
*
*  @author    Dark-Side.pro <contact@dark-side.pro>
*  @copyright 2007-2019 Dark-Side.pro
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class Dscustomerreviews extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dscustomerreviews';
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
     * http://doc.dark-side.pro/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        include dirname(__FILE__).'/sql/install.php';

        Configuration::updateValue('CUSTOMERREVIEWS_HOMESLIDER', false);
        Configuration::updateValue('CUSTOMERREVIEWS_TIMEAFTER', 3);
        Configuration::updateValue('CUSTOMERREVIEWS_MUSTAPROVED', true);
        Configuration::updateValue('CUSTOMERREVIEWS_REMINDMAIL', true);
        Configuration::updateValue('CUSTOMERREVIEWS_REMINDAFTER', 3);

        $this->createTab();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('registerGDPRConsent') &&
            $this->registerHook('actionDeleteGDPRCustomer') &&
            $this->registerHook('actionExportGDPRData') &&
            $this->registerHook('actionOrderStatusPostUpdate') &&
            $this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('displayCustomerAccount');
    }

    public function uninstall()
    {
        include dirname(__FILE__).'/sql/uninstall.php';

        Configuration::deleteByName('CUSTOMERREVIEWS_HOMESLIDER');
        Configuration::deleteByName('CUSTOMERREVIEWS_TIMEAFTER');
        Configuration::deleteByName('CUSTOMERREVIEWS_MUSTAPROVED');
        Configuration::deleteByName('CUSTOMERREVIEWS_REMINDMAIL');
        Configuration::deleteByName('CUSTOMERREVIEWS_REMINDAFTER');

        $this->tabRem();

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

        $datas = Tools::getValue('commentId');
        $sliderForm = Tools::getValue('sliderAprrove');
        $values = Tools::getValue('slider');
        $visiblevalues = Tools::getValue('visible');
        $statusForm = Tools::getValue('includedStatuses');
        $statusData = Tools::getValue('status');

        if (isset($sliderForm) && $datas != null && $values != null) {
            foreach ($values as $data => $value) {
                $this->approveSlider($data, $value);
            }
        }

        if (isset($sliderForm) && $datas != null && $visiblevalues != null) {
            foreach ($visiblevalues as $data => $value) {
                $this->approveComment($data, $value);
            }
        }

        if (isset($statusForm) && $statusData != null) {
            foreach ($statusData as $data => $value) {
                $this->updateStatus($data, $value);
            }
        }

        $status = $this->getStatus();
        $comments = $this->getAllComments();
        $slider = $this->getSliderComments();

        $this->context->smarty->assign('statuses', $status);
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('comments', $comments);
        $this->context->smarty->assign('slider', $slider);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        if ($datas != null || $visiblevalues != null) {
            $confirmation = $this->displayConfirmation($this->l('Settings updated'));
            $output = $confirmation .= $output;
        }

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
        $timeafter = Configuration::get('CUSTOMERREVIEWS_TIMEAFTER');
        $mailafter = Configuration::get('CUSTOMERREVIEWS_REMINDAFTER');

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
                        'label' => $this->l('Send remind'),
                        'name' => 'CUSTOMERREVIEWS_REMINDMAIL',
                        'is_bool' => true,
                        'desc' => $this->l('Send remind to customer after few days to write reivews'),
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
                        'type' => 'html',
                        'prefix' => '<i class="icon icon-clock"></i>',
                        'desc' => $this->l('How long after (in days) the purchase can the customer add a comment?'),
                        'name' => 'CUSTOMERREVIEWS_TIMEAFTER',
                        'label' => $this->l('Time after buy'),
                        'html_content' => '<input type="number" class="form-control" name="CUSTOMERREVIEWS_TIMEAFTER" value='.$timeafter.' min="0">',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'html',
                        'prefix' => '<i class="icon icon-clock"></i>',
                        'desc' => $this->l('How many days after purchase, send a reminder to write a comment'),
                        'name' => 'CUSTOMERREVIEWS_REMINDAFTER',
                        'label' => $this->l('Remind time'),
                        'html_content' => '<input type="number" class="form-control" name="CUSTOMERREVIEWS_REMINDAFTER" value='.$mailafter.' min="0">',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /*
EXISTS `'._DB_PREFIX_.'customerreviews_users` (
    `id_customerreviews_users` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `if_name` tinyint(1) NOT NULL,
    `custom_name` varchar(64) NULL,
    */

    protected function insertCustomerName($id_customer)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'customerreviews_users 
        (
            `id_customer`,
            `if_name`
        )
        VALUES
        (
            '.$id_customer.',
            1
        )
        ';
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getIfCustomerWantName($id_customer) //czy user chce mieć prawdziwe imię
    {
        $sql = 'SELECT id_status FROM '._DB_PREFIX_.'customerreviews_users
        WHERE `id_customer` = '.$id_customer;
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function setIfCustomerWantName($id_customer, $value) //update tego wyżej
    {
        $value = (int) $value;
        $id_customer = (int) $id_customer;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews_users
        SET 
        `if_name` = '.$value.'
        WHERE 
        `id_customer` = '.$id_customer;
        $sql = Db::getInstance()->execute($sql);
    }

    protected function getCustomerCustomName($id_customer) //sztuczne imię klienta
    {
        $sql = 'SELECT customname FROM '._DB_PREFIX_.'customerreviews_users
        WHERE `id_customer` = '.$id_customer;
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function setCustomerCustomName($id_customer, $customname)
    {
        $id_customer = (int) $id_customer;
        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews_users
        SET 
        `customname` = "'.$customname.'"
        WHERE 
        `id_customer` = '.$id_customer;
        $sql = Db::getInstance()->execute($sql);
    }

    protected function getStatus()
    {
        $lang = Context::getContext()->language->id;
        $sql = '
        SELECT osl.id_order_state AS id_status, osl.name AS status_name, crs.active AS active 
        FROM '._DB_PREFIX_.'order_state_lang AS osl
        LEFT JOIN '._DB_PREFIX_.'customerreviews_status AS crs
        ON osl.id_order_state = crs.id_status     
        WHERE osl.id_lang ='.$lang;
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getActiveStatus()
    {
        $sql = 'SELECT id_status FROM '._DB_PREFIX_.'customerreviews_status
        WHERE active = 1';
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function updateStatus($data, $value)
    {
        $valueint = (int) $value;
        $dataint = (int) $data;
        $sql[1] = 'DELETE FROM '._DB_PREFIX_.'customerreviews_status WHERE `id_status` = '.$dataint;

        $sql[2] = 'INSERT INTO '._DB_PREFIX_.'customerreviews_status 
        (
            `id_status`,
            `active`
        )
        VALUES
        (
            '.$dataint.',
            '.$valueint.'
        )

        ';

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
    }

    protected function approveComment($commentid, $value)
    {
        $valueint = (int) $value;
        $commentidint = (int) $commentid;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `visible` = '.$value.'
        WHERE 
        `id_customerreviews` = '.$commentidint;

        $sql = Db::getInstance()->execute($sql);
    }

    protected function weightComment($commentid, $value)
    {
        $valueint = (int) $value;
        $commentidint = (int) $commentid;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `visibleweight` = '.$valueint.'
        WHERE 
        `id_customerreviews` = '.$commentidint;

        $sql = Db::getInstance()->execute($sql);
    }

    protected function approveSlider($commentid, $value)
    {
        $valueint = (int) $value;
        $commentidint = (int) $commentid;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `slider` =  '.$valueint.'
        WHERE 
        `id_customerreviews` = '.$commentidint;

        $sql = Db::getInstance()->execute($sql);
    }

    protected function weightSlider($commentid, $value)
    {
        $valueint = (int) $value;
        $commentidint = (int) $commentid;

        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `sliderweight` = '.$valueint.'
        WHERE 
        `id_customerreviews` = '.$commentidint;

        $sql = Db::getInstance()->execute($sql);
    }

    protected function deleteComment($commentid)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
        SET 
        `deleted` = 1
        WHERE 
        `id_customerreviews` = '.$commentid;

        $sql = Db::getInstance()->execute($sql);
    }

    protected function countAllComments()
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT COUNT(*)
        FROM '._DB_PREFIX_.'customerreviews 
        WHERE cr.deleted = 0
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getAllComments()
    {
        $sql = '
        SELECT  cr.id_customerreviews, cus.firstname, cus.lastname, cr.stars, cr.content, pr.id_product, od.product_name, cr.timeadded, cr.visible, cr.visibleweight, cr.slider, cr.reviewlang, cru.customname
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        LEFT JOIN '._DB_PREFIX_.'customerreviews_users AS cru
        ON cru.id_customer = ord.id_customer
        WHERE cr.deleted = 0 AND cr.currentdata = 0 
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function deleteAllCustomerComments($id_user)
    {
        $id_userint = (int) $id_user;

        $sql = '
        DELETE FROM '._DB_PREFIX_.'customerreviews
        WHERE 
        id_order_detail IN
        (
            SELECT odb.id_order_detail
            FROM '._DB_PREFIX_.'order_detail AS oda
            INNER JOIN '._DB_PREFIX_.'orders AS ord
            ON ord.id_order = oda.id_order 
            WHERE
            ord.id_customer = '.$id_userint.'
        )
        '
        ;

        $sql = Db::getInstance()->Execute($sql);
    }

    protected function getAllSlider()
    {
        $sql = 'SELECT cus.firstname, cus.lastname, cr.stars, cr.content, pr.id_product, od.product_name, cr.timeadded, cr.visible, cr.visibleweight, cr.slider, cr.reviewlang, cru.customname
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        LEFT JOIN '._DB_PREFIX_.'customerreviews_users AS cru
        ON cru.id_customer = ord.id_customer
        WHERE cr.deleted = 0 AND cr.currentdata = 0 
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getAllCommentsFromUser($id_user)
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT cr.stars, cr.timeadded, cr.content, od.product_name 
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE cr.reviewlang = '.$currentlang.'
        AND
        ord.id_customer = '.$id_user.'
        AND
        cr.currentdata = 0
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getSliderComments()
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, pr.id_product, od.product_name, cr.sliderweight, cr.id_customerreviews, cru.customname
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        LEFT JOIN '._DB_PREFIX_.'customerreviews_users AS cru
        ON cru.id_customer = ord.id_customer
        WHERE cr.slider = 1 AND cr.reviewlang = '.$currentlang.'  AND cr.currentdata = 0 
        ORDER BY cr.sliderweight
        ';
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getProductComments($productid)
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, od.product_name, cru.customname
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        LEFT JOIN '._DB_PREFIX_.'customerreviews_users AS cru
        ON cru.id_customer = ord.id_customer
        WHERE  pr.id_product = '.$productid.' AND cr.visible = 1 AND cr.reviewlang = '.$currentlang.'  AND cr.currentdata = 0 
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function getProductStars($productid)
    {
        $currentlang = $this->context->language->id;

        $sql = 'SELECT AVG(cr.stars) AS srednia, COUNT(cr.stars) AS liosc
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        WHERE  pr.id_product = '.$productid.' AND cr.visible = 1  AND cr.currentdata = 0 
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function ifProductCommentsIsNeeded($productid)
    {
        $currentlang = $this->context->language->id;

        $days = (int) Configuration::get('CUSTOMERREVIEWS_TIMEAFTER');

        $sql = 'SELECT cr.stars, cr.content, cr.timeadded, cus.firstname, cus.lastname, od.product_name, od.id_order_detail, cru.customname
        FROM '._DB_PREFIX_.'customerreviews AS cr
        LEFT JOIN '._DB_PREFIX_.'order_detail AS od
        ON cr.id_order_detail = od.id_order_detail
        LEFT JOIN '._DB_PREFIX_.'orders AS ord
        ON od.id_order = ord.id_order
        LEFT JOIN '._DB_PREFIX_.'product AS pr
        ON pr.id_product = od.product_id
        LEFT JOIN '._DB_PREFIX_.'customer AS cus
        ON cus.id_customer = ord.id_customer
        LEFT JOIN '._DB_PREFIX_.'customerreviews_users AS cru
        ON cru.id_customer = ord.id_customer
        WHERE  pr.id_product = '.$productid.' AND cr.currentdata = 1 AND cr.reviewlang = '.$currentlang.' AND cr.timetowrite  <= TIMESTAMP(DATE(CURDATE() ) - '.$days.') 
        ';
        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    protected function insertProductComment($id_order_detail, $currentcustomer)
    {
        $currentlang = $this->context->language->id;
        $stars = Tools::getValue('stars');
        $starsint = (int) $stars;
        $content = Tools::getValue('reviews');
        $title = 'dupa';
        $time = 'NOW()';
        $id_order_detailint = (int) $id_order_detail;

        $mustBeAprooved = Configuration::get('CUSTOMERREVIEWS_MUSTAPROVED');

        if ($mustBeAprooved == 1) {
            $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
            SET 
            `timeadded` = '.$time.',
            `stars` = '.$starsint.',
            `title` = "'.$title.'",
            `content` = "'.$content.'",
            `currentdata` = 0
            WHERE 
            `id_order_detail` = '.$id_order_detailint;
        } else {
            $sql = 'UPDATE '._DB_PREFIX_.'customerreviews
            SET 
            `timeadded` = '.$time.',
            `stars` = '.$starsint.',
            `title` = "'.$title.'",
            `content` = "'.$content.'",
            `visible` = 1,
            `currentdata` = 0
            WHERE 
            `id_order_detail` = '.$id_order_detailint;
        }

        $sql = Db::getInstance()->Execute($sql);

        return $sql;
    }

    protected function addProductComment($id_order, $id_user, $timetowrite) //to ma być uruchomione gdy jest opłata
    {
        $currentlang = $this->context->language->id;

        $sql = '
        DELETE FROM '._DB_PREFIX_.'customerreviews
        WHERE 
        id_order_detail IN
        (
            SELECT odb.id_order_detail
            FROM '._DB_PREFIX_.'orders AS ord
            INNER JOIN '._DB_PREFIX_.'order_detail AS oda
            ON ord.id_order = oda.id_order 
            LEFT JOIN '._DB_PREFIX_.'order_detail AS odb
            ON oda.product_id = odb.product_id 
            WHERE
            ord.id_order = '.$id_order.'
            AND
            ord.id_customer = '.$id_user.'
        )
        AND
        currentdata = 1
        '
        ;

        $sql = Db::getInstance()->Execute($sql);

        /*
                $sql .= 'UPDATE '._DB_PREFIX_.'customerreviews AS cr
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
                WHERE  `id_order_detail` = '.$id_order_detailint.'
                )
                AND ord.id_customer = '.$currentcustomer.'
                SET `currentdata` = 0';

                $sql = Db::getInstance()->Execute($sql);

        */

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
        `currentdata`,
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

        $sql = Db::getInstance()->execute($sql);

        return $sql;
    }

    protected function getCustomerId($orderId)
    {
        $orderId = (int) $orderId;

        $sql = 'SELECT  ord.id_customer
        FROM '._DB_PREFIX_.'orders AS ord
        WHERE  ord.id_order = '.$orderId.'
        ';

        $sql = Db::getInstance()->ExecuteS($sql);

        return $sql;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CUSTOMERREVIEWS_HOMESLIDER' => Configuration::get('CUSTOMERREVIEWS_HOMESLIDER', false),
            'CUSTOMERREVIEWS_TIMEAFTER' => Configuration::get('CUSTOMERREVIEWS_TIMEAFTER', 3),
            'CUSTOMERREVIEWS_MUSTAPROVED' => Configuration::get('CUSTOMERREVIEWS_MUSTAPROVED', false),
            'CUSTOMERREVIEWS_REMINDMAIL' => Configuration::get('CUSTOMERREVIEWS_REMINDMAIL', false),
            'CUSTOMERREVIEWS_REMINDAFTER' => Configuration::get('CUSTOMERREVIEWS_REMINDAFTER', 3),
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
            $this->context->controller->addJS($this->_path.'views/js/slider.js');
            $this->context->controller->addCSS($this->_path.'views/css/slider.css');
        } elseif (Tools::getValue('controller') == 'product') {
            $this->context->controller->addJS($this->_path.'views/js/product.js');
            $this->context->controller->addCSS($this->_path.'views/css/product.css');
        } elseif (Tools::getValue('controller') == 'myaccount') {
            $this->context->controller->addCSS($this->_path.('views/css/myaccount.css'));
        }
    }

    public function hookdisplayProductExtraContent($params)
    {
        $title = $this->l('Reviews');
        $productid = (int) Tools::getValue('id_product');
        $reviews = $this->getProductComments($productid);
        $customer = Context::getContext()->customer->isLogged();
        $isneed = $this->ifProductCommentsIsNeeded($productid);
        $addreviews = Tools::isSubmit('addReview');
        $customerid = Context::getContext()->customer->id;
        $stars = $this->getProductStars($productid);

        $this->context->smarty->assign('stars', $stars);
        $this->context->smarty->assign('isneed', $isneed);
        $this->context->smarty->assign('customer', $customer);
        $this->context->smarty->assign('reviews', $reviews);
        $content = $this->display(__FILE__, 'views/templates/hook/hookDisplayProuctTab.tpl');
        $array = array();
        $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
            ->setTitle($title)
            ->setContent($content);

        if ($addreviews) {
            $id_order_detail = Tools::getValue('id_order_detail');
            $this->insertProductComment($id_order_detail, $customerid);
            $this->_clearCache($this->templateFile);
        }

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
        $this->deleteAllCustomerComments($customer['id']);
    }

    public function hookActionExportGDPRData($customer)
    {
        $comments = $this->getAllCommentsFromUser($customer['id']);
        $content = $this->l('Content');
        $stars = $this->l('Stars');
        $productName = $this->l('Product name');
        $timeAded = $this->l('Time added');

        if ($comments != null) {
            foreach ($comments as $comment) {
                $content .= $comment['content'];
                $stars .= $comment['stars'];
                $productName .= $comment['product_name'];
                $timeAded .= $comment['timeadded'];
            }

            if (isset($comments)) {
                return json_encode($comments);
            }
        } else {
            return json_encode($this->l('Customer Reviews: There is no data to export'));
        }

        return json_encode($this->l('Customer Reviews: Unable to export customer data.'));
    }

    public function hookActionPaymentConfirmation($params)
    {
        $orderId = $params['id_order'];
        $customer = Context::getContext()->customer->id;
        $now = 'NOW()';
        $this->addProductComment($orderId, $customer, $now);
    }

    public function hookDisplayProductButtons()
    {
        $productid = (int) Tools::getValue('id_product');
        $stars = $this->getProductStars($productid);
        $output = $this->$output = $this->display(__FILE__, 'views/templates/hook/hookDisplayProductButtons.tpl');

        $this->context->smarty->assign('stars', $stars);

        return $output;
    }

    public function hookDisplayProductAdditionalInfo()
    {
        $productid = (int) Tools::getValue('id_product');
        $stars = $this->getProductStars($productid);
        $output = $this->display(__FILE__, 'views/templates/hook/hookDisplayProductAdditionalInfo.tpl');

        $this->context->smarty->assign('stars', $stars);

        return $output;
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $statusid = $params['newOrderStatus']->id;
        $activeStatus = $this->getActiveStatus();
        $orderId = $params['id_order'];
        $customerId = $this->getCustomerId($orderId);
        $now = 'NOW()';

        foreach ($activeStatus as $status) {
            if ($status == $activeStatus) {
                $this->addProductComment($orderId, $customerId, $now);
            }
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $this->context->smarty->assign('ps_17', (int) $ps_17);
        $output = $this->display(__FILE__, 'views/templates/hook/hookDisplayCustomerAccount.tpl');

        return $output;
    }
}
