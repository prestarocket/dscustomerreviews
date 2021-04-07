<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <DARK SIDE TEAM> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Poul-Henning Kamp
 * ----------------------------------------------------------------------------
 */
include dirname(__FILE__).'/config/config.inc.php';
include dirname(__FILE__).'/init.php';

$key = Configuration::get('CUSTOMERREVIEWS_PASSWORD');
$dir = Configuration::get('CUSTOMERREVIEWS_ADMINDIR');
$remindAfter = (int) Configuration::get('CUSTOMERREVIEWS_REMIND');

if (!Tools::getValue('k') || Tools::getValue('k') != $key) {
    die('unauthorized');
}

if (!defined('_PS_ADMIN_DIR_')) {
    if ($dir != null) {
        define('_PS_ADMIN_DIR_', getcwd().'/'.$dir);
    } else {
        die('unauthorized');
    }
}
