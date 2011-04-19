<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_htmlentities($string)
{
	return htmlentities($string);
}

/* vim: set expandtab: */

?>
