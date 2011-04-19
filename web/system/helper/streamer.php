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

function GetAllSongInDirectory($folder,$recursif=true) {
		global $DB;
		
	$DB->query("select dr.parent||dr.name||'/'||fl.filename as link , fl.filename as filename from files fl, directory dr where 
					dr.id = fl.directory_id 
					AND extension = 'mp3' 
					AND directory_id IN (select id from directory where 
											parent||name like((select parent||name from directory where id= ".$folder.")||'%'))","GetAllSongInDirectory");
	$files = $DB->fetcharray();	
	/*	//sanitize
		//$folder = urldecode($folder);
		$files = array();
		$dir=opendir($folder);

		while ($file = readdir($dir)) {
			
			if ($file == '.' || $file == '..') continue;
			if (is_dir($folder.'/'.$file) && $file !='.@__thumb') {
				if ($recursif==true)
				{
					$files=array_merge($files, GetAllSongInDirectory($config,$folder.'/'.$file,true));
				}
			}
			else
			{
				$bouts = explode(".", $file);
    			$extension = array_pop($bouts);
	 			if (in_array(strtolower($extension), $config->item('extension_song')))
				{	
					$ar['link'] = $folder.'/'.$file;
					$ar['filename'] = $file;
					$files[] = $ar;
				}
			}
			
		}
		closedir($dir);*/
		return $files;
}

?>