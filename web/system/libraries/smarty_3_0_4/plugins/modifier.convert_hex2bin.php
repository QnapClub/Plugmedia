<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_convert_hex2bin($string)
{
	include_once (BASEPATH.'helper/utility.php');
	return hex2bin($string);
}

/* vim: set expandtab: */

?>
