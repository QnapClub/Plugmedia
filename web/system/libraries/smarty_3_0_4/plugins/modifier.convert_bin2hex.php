<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_convert_bin2hex($string)
{
	return bin2hex($string);
}

/* vim: set expandtab: */

?>
