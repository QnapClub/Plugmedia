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


function generateThumbnail( $id_picture, $silentmode = false, $thumb_type='small', $echo_output = false)
{
	global $DB;
	global $db_config;
	
	$DB->query("SELECT distinct(fil.id) as fileid, mime.mimetype as mimetype , file_thumb, file_thumb_normal, orientation, dir.parent as parent, dir.name as name, fil.filename as filename, fil.extension as extension, fil.file_hash as file_hash FROM files fil JOIN directory dir ON dir.id=fil.directory_id LEFT JOIN mimetype mime ON mime.extension = fil.extension LEFT JOIN metadata_exif ON fil.id = files_id WHERE fil.id='".$id_picture."'","generateThumbnail");		
	
	$recup = $DB->fetchrow();

	
	if (in_array(strtolower($recup['extension']),$db_config['EXTENSION_MOV_DISPLAYABLE']))	
	{
		//generate thumb for video
		$return = extractThumb($recup);
			
	}
	else if ($thumb_type == 'small' && $recup['file_thumb'] != '' && is_file(ROOTPATH.'/thumb'.$recup['parent'].$recup['name'].'/'.$recup['filename']) )
	{
		$return =  $recup['file_thumb'];
	
	}	
	else if ($thumb_type != 'small' && $recup['file_thumb_normal'] != '' && is_file($recup['file_thumb_normal'] ) )
	{
		$return =  $recup['file_thumb_normal'];
	}
	else
		$return =  generateThumbWithFilepath($recup['filename'], $recup['parent'], $recup['name'], $recup['orientation'], $recup['extension'], $recup['file_hash'], $silentmode, $thumb_type, $recup['fileid']);

	if (!$echo_output)
		return $return;
	else
	{
		header("Content-Type: ".$recup['mimetype'].";");
		echo file_get_contents($return);
	}
	
	
}

function generateThumbWithFilepath($filename, $parent, $name, $orientation, $extension, $file_hash, $silentmode = false, $thumb_type='small', $file_id='', $custom_thumb_path='')
{
	global $DB;
	global $db_config;
	
	$configDB = load_class('ConfigLoader');

	if (in_array(strtolower($extension),$db_config['EXTENSION_MOV_DISPLAYABLE']))	
	{
		if ($thumb_type!='small')
		{
			// skip other format for movie
			return false;
			exit();
		}
		//generate thumb for video
		$return = extractThumbWithoutInfo($file_id);
	}
	else
	{
		$thumb = load_class('ThumbnailHandler');	
		$thumb->setThumbInfo(iconv("UTF-8","UTF-8//IGNORE",$filename), iconv("UTF-8","UTF-8//IGNORE",$parent).iconv("UTF-8","UTF-8//IGNORE",$name) ,$orientation, $extension, $custom_thumb_path);
		$thumb->setSilentMode($silentmode);	
		$return = $thumb->generateThumb($thumb_type);
		$adress_thumb = pg_escape_string(iconv("UTF-8","UTF-8//IGNORE",$return));
		
		if ($thumb_type== 'small')
			$column_name = 'file_thumb';
		else
			$column_name = 'file_thumb_normal';
		
		if ((bool) $configDB->getValue('ENABLE_FOOTPRINT'))
			$DB->query("UPDATE files SET ".$column_name." = '".$adress_thumb."' WHERE file_hash= '".$file_hash."'","");
		else
			$DB->query("UPDATE files SET ".$column_name." = '".$adress_thumb."' WHERE id='".$file_id."'","");
	}
	return $return;
}


function extractThumb($recup)
{
	$movie = load_class('Movie');
	$movie->setMovieId($recup['fileid']);
	$movie->setAllInformation($recup);
	
		
	$return = $movie->performMovieEncoding(false, false, true);
	return $return['thumb_path'];
	
}
function extractThumbWithoutInfo($id_movie)
{
	
	$movie = load_class('Movie');
	$movie->setMovieId($id_movie);
	$movie->getAllInformationFromId();
	
		
	$return = $movie->performMovieEncoding(false, false, true);
	return $return['thumb_path'];
	
}




?>