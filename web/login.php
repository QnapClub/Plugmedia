<?php
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/


require_once 'system/core/frontcontroller.php';

if (isset ($_POST['user']))
{
	$SESSION->login($_POST['user'], $_POST['password'], 'index.php');
	header("Location:index.php");
}else
{

$SMARTY->display_('login.tpl','login');
}
?>