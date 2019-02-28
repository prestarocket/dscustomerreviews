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
$sql = array();

$sql[1] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customerreviews` (
    `id_customerreviews` int(11) NOT NULL AUTO_INCREMENT,
    `id_order_detail` int(11) NOT NULL,
    `timetowrite` datetime NOT NULL,
    `timeadded` datetime NULL,
    `stars` tinyint(1) NULL,
    `title` varchar(64) NULL,
    `content` text  NULL,
    `visible` tinyint(1) NOT NULL,
    `visibleweight` int(5) NOT NULL,
    `deleted` tinyint(1) NOT NULL,
    `slider` tinyint(1) NOT NULL,
    `sliderweight` int(5) NOT NULL,
    `currentdata` int(1) NOT NULL,
    `reviewlang` int(10) NOT NULL,
    PRIMARY KEY  (`id_customerreviews`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[2] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customerreviews_status` (
    `id_customerreviews_status` int(11) NOT NULL AUTO_INCREMENT,
    `id_status` int(11) NOT NULL,
    `active` tinyint(1) NOT NULL,
    PRIMARY KEY  (`id_customerreviews_status`)
)   ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[3] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customerreviews_users` (
    `id_customerreviews_users` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `if_name` tinyint(1) NOT NULL,
    `customname` varchar(64) NULL,
    PRIMARY KEY  (`id_customerreviews_users`)
)   ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[4] = 'INSERT INTO '._DB_PREFIX_.'customerreviews_users 
(
    `id_customer`,
    `if_name`
)
SELECT
    id_customer,
    1
    FROM
    '._DB_PREFIX_.'customer
';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

/*
'\"id_customerreviews_status\"
int(11) NOT NULL AUTO_INCREMENT,\n \"active\" tinyin\'
at line 2CREATE TABLE IF NOT EXISTS
ps_customerreviews_status (\n \"id_customerreviews_status\"
int(11) NOT NULL AUTO_INCREMENT,\n \"active\" tinyint(1)
NOT NULL,\n PRIMARY KEY (id_customerreviews_status)\n)
ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
