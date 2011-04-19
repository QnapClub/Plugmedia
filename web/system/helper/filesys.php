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


/**
* create directory recursively - same as "mkdir -p"
*/
function mkdir_p($target,$mode='0777')
{
	if(is_dir($target) || empty($target)) {	// best case check first
		return 1;
	}
	if(file_exists($target) && !is_dir($target)) {	// target exists but isn't a directory..
		return 0;
	}
	if( mkdir_p( substr( $target,0,strrpos( $target,'/') ) , $mode) )
	{
		return mkdir($target,intval($mode,8)); // crawl back up & create dir tree
	}
	return 0;
}

/**
* copy recursive - same as "cp -R"
*/
function copy_r($source,$dest)
{
	if( !is_dir($source) )
	{
		if( is_file($source) )
		{
			@copy($source,$dest);
			return 1;
		}
		else
		{
			return 0;
		}
	}
	if (!is_dir($dest))  {
		mkdir_p($dest);
	}
	$h=@dir($source);
	while (@($entry=$h->read()) !== false)
	{
		if (($entry!=".")&&($entry!=".."))
		{
			if (is_dir("$source/$entry")&&$dest!=="$source/$entry")
			{
				copy_r("$source/$entry","$dest/$entry");
			}
			else
			{
				@copy("$source/$entry","$dest/$entry");
			}
		}
	}
	$h->close();
	return 1;
}


/**
* escape string for use in exec() 
*/
function escape_string($str)
{
	$str = str_replace('"','\"',$str);
	//$str = '"'.$str.'"';
	return $str;
}


function get_footprint($src_file)
{
	$configDB =& load_class('ConfigLoader');

	if ((bool) $configDB->getValue('ENABLE_FOOTPRINT'))
		return @sha1_file($src_file);
	else
		return '';
}


?>