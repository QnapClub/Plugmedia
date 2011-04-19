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

class CORE_Config {

	var $config = array();
	var $is_loaded = array();

	function CORE_Config()
	{
		$this->config =& get_config();
		//log_message('debug', "Config Class Initialized");
	}  	
	function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
		
		if (in_array($file, $this->is_loaded, TRUE))
		{
			return TRUE;
		}

		if ( ! file_exists(BASEPATH.'config/'.$file.EXT))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.EXT.' does not exist.');
		}
	
		include(BASEPATH.'config/'.$file.EXT);

		if ( ! isset($config) OR ! is_array($config))
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}		
			show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
		}
		
		if ($use_sections === TRUE)
		{
			if (isset($this->config[$file]))
			{
				$this->config[$file] = array_merge($this->config[$file], $config);
			}
			else
			{
				$this->config[$file] = $config;
			}
		}
		else
		{
			$this->config = array_merge($this->config, $config);
		}

		$this->is_loaded[] = $file;
		unset($config);

		//log_message('debug', 'Config file loaded: config/'.$file.EXT);
		return TRUE;
	}
  	
	function item($item, $index = '')
	{			
		if ($index == '')
		{	
			if ( ! isset($this->config[$item]))
			{
				return FALSE;
			}
		
			$pref = $this->config[$item];
		}
		else
		{
			if ( ! isset($this->config[$index]))
			{
				return FALSE;
			}
		
			if ( ! isset($this->config[$index][$item]))
			{
				return FALSE;
			}
		
			$pref = $this->config[$index][$item];
		}

		return $pref;
	}
  	
	function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}
		
		$pref = $this->config[$item];
		
		if ($pref != '')
		{			
			if (ereg("/$", $pref) === FALSE)
			{
				$pref .= '/';
			}
		}

		return $pref;
	}
  	
	function site_url($uri = '')
	{
		if (is_array($uri))
		{
			$uri = implode('/', $uri);
		}
		
		if ($uri == '')
		{
			return $this->slash_item('base_url').$this->item('index_page');
		}
		else
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');		
			return $this->slash_item('base_url').$this->slash_item('index_page').preg_replace("|^/*(.+?)/*$|", "\\1", $uri).$suffix;
		}
	}
	
	function system_url()
	{
		$x = explode("/", preg_replace("|/*(.+?)/*$|", "\\1", BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}

	function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}

}
?>