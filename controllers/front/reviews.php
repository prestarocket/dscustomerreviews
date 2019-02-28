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
class CustomerReviewsReviewsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (!Context::getContext()->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&redirect=module&module='.$this->module->name.'&action=reviews');
        }

        $customer_id = (int) Context::getContext()->customer->id;
        $customerCustomName = $this->getCustomerCustomName($customer_id);
        $ifCustomName = $this->getIfCustomerWantName($customer_id);
        $comments = getAllCommentsFromUser($customer_id);
        $output = $this->setTemplate('module:'.$this->module_name.'views/templates/front/reviews.tpl');

        $this->context->smarty->assign('customerreviews', array(
            'customName' => $customer_name,
            'ifCustomName' => $ifCustomName,
            'comments' => $comments,
        ));
        $this->context->smarty->assign('id_module', $this->module->id);
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
        `custom_name` = "'.$customname.'"
        WHERE 
        `id_customer` = '.$id_customer;
        $sql = Db::getInstance()->execute($sql);
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

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array('title' => $this->l('My account'),
            'url' => $this->context->link->getPageLink('my-account'),
        );
        $breadcrumb['links'][] = array('title' => $this->l('reviews'),
            'url' => $this->context->link->getModuleLink($this->module->name, 'reviews', array(), true),
        );

        return $breadcrumb;
    }
}
