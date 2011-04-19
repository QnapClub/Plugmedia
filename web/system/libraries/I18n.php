<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

class CORE_I18n {

	var $default_lang;
	var $current_lang;
	var $translation_table = array();		
	
	function CORE_I18n()
	{
		$configDB =& load_class('ConfigLoader');

		$this->default_lang = $configDB->getValue('DEFAULT_LANG');
		$this->current_lang = 'en';
		$this->loadTranslationTable($this->current_lang);
	}
	
	function setLanguage($lang)
	{
		// is $lang available in config?
		$this->current_lang = $lang;
		$this->loadTranslationTable($this->current_lang);	
	}
	
	function loadTranslationTable($language)
	{
		$trans_file =BASEPATH.'locale/'.$language.EXT;
		if (is_readable($trans_file)) {
    		require $trans_file;
		} else {
			require ROOTPATH.'system/locale/'.$this->default_lang.EXT;
			
		}
		
		$this->translation_table = $lang;
	}
	
	function translate($text)
	{
		$tr = array();
		$p = 0;
		
		for ($i=1; $i < func_num_args(); $i++) {
		$arg = func_get_arg($i);
				
		if (is_array($arg)) {
			foreach ($arg as $aarg) {
				$tr['%'.++$p] = $aarg;
			}
		} else {
				$tr['%'.++$p] = $arg;
			}
		}
		
		if (array_key_exists($text,$this->translation_table))
		{
			return strtr($this->translation_table[$text],$tr);
		}
		else
		{
			// LOAD THE DEFAULT TranslationTable
			require BASEPATH.'locale/'.$this->default_lang.EXT;
			return utf8_encode(strtr($lang[$text],$tr));
		}
		
	}
	
	
	function getCurrent_lang()
	{
		return $this->current_lang;
	}

}


?>
