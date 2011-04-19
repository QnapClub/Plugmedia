<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_decode_ip($ip)
{
	include_once (BASEPATH.'helper/utility.php');
	if ($ip == "")
		return false;
	else
		return decode_ip($ip);
}

/* vim: set expandtab: */

?>
