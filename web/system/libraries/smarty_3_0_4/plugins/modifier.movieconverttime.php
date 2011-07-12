<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 */
function smarty_modifier_movieconverttime($string)
{
   return intval($string/8000000);
} 

?>