<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_decode_utf8($string)
{
	return utf8_decode($string);
}

/* vim: set expandtab: */

?>
