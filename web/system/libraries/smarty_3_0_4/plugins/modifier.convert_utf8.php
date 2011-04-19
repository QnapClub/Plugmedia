<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_convert_utf8($string, $respect_config = true)
{
	/*$parsingini =& load_class('ParsingIni');
	$parsingini->load('system/config/plugmedia_admin.php');
	if ($respect_config)
	{
		if ((bool)$parsingini->get('utf8encoding', 'Visualization'))
			return utf8_encode($string);
		else
			return $string;
	}
	else
	*/
		return utf8_encode($string);
}

/* vim: set expandtab: */

?>
