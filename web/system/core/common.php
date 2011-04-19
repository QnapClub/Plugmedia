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

function &load_class($class, $instantiate = TRUE)
{
	static $objects = array();
	if (isset($objects[$class]))
	{
		return $objects[$class];
	}
	require_once(BASEPATH.'libraries/'.$class.EXT);	
	$is_subclass = FALSE;
	if ($instantiate == FALSE)
	{
		$objects[$class] = TRUE;
		return $objects[$class];
	}
	$name = 'CORE_'.$class;
	
	$objects[$class] =& new $name();
	return $objects[$class];
}

function loadHelper ($helper)
{
	if (is_file(BASEPATH.'helper/'.$helper.EXT))
		require_once(BASEPATH.'helper/'.$helper.EXT);	
	else
		show_error('Helper file '.$helper.EXT.' does not exist.');
}

function &get_config()
{
	static $main_conf;
		
	if ( ! isset($main_conf))
	{
		if ( ! file_exists(BASEPATH.'config/config'.EXT))
		{
			exit('The configuration file config'.EXT.' does not exist.');
		}
		
		require(BASEPATH.'config/config'.EXT);
		
		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		$main_conf[0] =& $config;
	}
	return $main_conf[0];
}

function config_item($item)
{
	static $config_item = array();

	if ( ! isset($config_item[$item]))
	{
		$config =& get_config();
		
		if ( ! isset($config[$item]))
		{
			return FALSE;
		}
		$config_item[$item] = $config[$item];
	}

	return $config_item[$item];
}

function show_error($message)
{
	$error =& load_class('Exceptions');
	echo $error->show_error('An Error Was Encountered', $message);
	exit;
}

function show_404($page = '')
{
	$error =& load_class('Exceptions');
	$error->show_404($page);
	exit;
}

function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $LOG;
	
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$LOG =& load_class('Log');	
	$LOG->write_log($level, $message, $php_error);
}

function _exception_handler($severity, $message, $filepath, $line)
{	

	if ($severity == E_STRICT)
	{
		return;
	}

	$error =& load_class('Exceptions');
	if (($severity & error_reporting()) == $severity)
	{
		$error->show_php_error($severity, $message, $filepath, $line);
	}
	
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$error->log_exception($severity, $message, $filepath, $line);
	
}

// Add code for missing functions if needed
if ( !function_exists( 'sqlite_escape_string' ) ) 
{
	function sqlite_escape_string( $stt) 
	{
		return str_replace( "'", "\'", $stt);
	}
} 
	
if ( !function_exists('fnmatch') ) 
{
	function fnmatch($pattern, $string) {
		return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
	}
}


?>