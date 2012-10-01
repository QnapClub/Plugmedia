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


function resizePicture($array_picture)
{
	$configDB = load_class('ConfigLoader');
	
	
	if ($array_picture['extension'] == 'picture' || in_array($array_picture['readable_type'],$configDB->getValue('EXTENSION_RAW')))
	{
		
		// PICTURE TO DISPLAY
		// trying to resize...
		// RAW files can only be viewable with normal picture
		if (in_array($array_picture['readable_type'],$configDB->getValue('EXTENSION_RAW')))
		{	
			$array_picture['size'] = getimagesize ($array_picture['normal_thumbnail']);
			$array_picture['exif_rotate']  = 0; // thumbnail already rotated
		}
		else	
			$array_picture['size'] = getimagesize ($array_picture['name']);
		
	
		
		$dst_w = NORMALTHUMB_WIDTH;
		$dst_h = NORMALTHUMB_HEIGHT;
		
		$src_w = $array_picture['size'][0]; 
		$src_h = $array_picture['size'][1];
		
		if (@$array_picture['exif_rotate'] >0)
		{
			if ($array_picture['exif_rotate'] == 90 || $array_picture['exif_rotate'] == 270)
			{
				$src_w = $array_picture['size'][1]; 
				$src_h = $array_picture['size'][0];			
			}
		} 
		
		if ($src_w <= $dst_w && $src_h <= $dst_h)
		{
			$array_picture['size'] = array($src_w, $src_h);
			return $array_picture;
		}
	   
	   	// Teste les dimensions tenant dans la zone
	   	$test_h = round(($dst_w / $src_w) * $src_h);
	   	$test_w = round(($dst_h / $src_h) * $src_w);
	   
	   	// Si Height final non précisé (0)
	   	if(!$dst_h) $dst_h = $test_h;
	   
	   	// Sinon si Width final non précisé (0)
	   	elseif(!$dst_w) $dst_w = $test_w;
	   
	   	// Sinon teste quel redimensionnement tient dans la zone
	   	elseif($test_h>$dst_h) $dst_w = $test_w;
	   	else $dst_h = $test_h;
	   
		$array_picture['size'] = array($dst_w, $dst_h);
		return $array_picture;
	}
	else
		return $array_picture;	
	
}


function renderPicture($id_picture, $pourcentage=1)
{
	global $DB;
	if ($pourcentage <0 && $pourcentage >2.5)
		$pourcentage=1;
	$DB->query("SELECT file_thumb_normal,orientation, filename, parent, name, extension FROM files fil, directory dir, metadata_exif mex WHERE mex.files_id = fil.id AND fil.directory_id = dir.id AND fil.id=$id_picture","renderPicture");
	$image = $DB->fetchrow();
	
	if (is_array($image))
	{
		$properties = propertyByExtension($image['extension']);
		
		$image['name'] = $image['parent'].$image['name']."/".$image['filename'];
		$image['extension'] = 'picture';
		$image['exif_rotate'] = convertExifOrientationToDegree($image['orientation']);
		$im = $properties['imagecreatefrom']($image['name']);
		
					
		if(!$im)
		{
			$properties = propertyByExtension('png');
			$thumb = generateErrorImg();
		}
		else
		{
			$info = resizePicture($image);
		
			$newwidth = $info['size'][0]*$pourcentage;
			$newheight = $info['size'][1]*$pourcentage;
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			$imwidth =  imagesx($im);
			$imheight =  imagesy($im);
			
			// Redimensionnement
			imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $imwidth, $imheight);				
			
			// NOW Rotate if needed
			$thumb = imagerotate($thumb, $image['exif_rotate'],'0');
			//

		}
		
	}
	else
	{
		$properties = propertyByExtension('png');
		$thumb = generateErrorImg();
	}	
	header($properties['mime_type']);
	$properties['imagedisplay']($thumb);
	imagedestroy($thumb);	


}



function convertExifOrientationToDegree($orientation)
{
	switch ($orientation) 
	{
		case 1:
			$rotate_angle = 0;
		break;
		case 3:
			$rotate_angle = 180;
		break;
		case 6:
			$rotate_angle = 270;
		break;
		case 8:
			$rotate_angle = 90;
		break;
		default:
			$rotate_angle = 0;
		break;
	}

	return $rotate_angle;
}

function propertyByExtension($extension)
{
	$array = array();
	if ($extension == 'jpg' || $extension == 'jpeg')
	{
		$array['imagecreatefrom'] = 'imagecreatefromjpeg'; 
		$array['imagedisplay'] = 'imagejpeg'; 
		$array['mime_type'] = 'Content-Type: image/jpg'; 
	}else if ($extension == 'gif')
	{
		$array['imagecreatefrom'] = 'imagecreatefromgif'; 
		$array['imagedisplay'] = 'imagegif'; 
		$array['mime_type'] = 'Content-Type: image/gif'; 	
	}else if ($extension == 'png')
	{
		$array['imagecreatefrom'] = 'imagecreatefrompng'; 
		$array['imagedisplay'] = 'imagepng'; 
		$array['mime_type'] = 'Content-Type: image/png'; 	
	}else if ($extension == 'bmp')
	{
		$array['imagecreatefrom'] = 'imagecreatefrombmp'; 
		$array['imagedisplay'] = 'imagegd'; 
		$array['mime_type'] = 'Content-Type: image/jpg'; 	
	}
	
	return $array;

}


function generateErrorImg()
{
	
	$im  = imagecreatetruecolor(200, 200);
	
	$bgc = imagecolorallocate($im, 255, 255, 255);
	imagefilledrectangle($im, 0, 0, 200, 200, $bgc);
	
	$watermark = @imagecreatefrompng('shield.png');

	$watermarkwidth =  imagesx($watermark);
	$watermarkheight =  imagesy($watermark);
	$startwidth = ((200 - $watermarkwidth)/2);
	$startheight = ((200 - $watermarkheight)/2);
	imagecopy($im, $watermark,  $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight); 
	imagestring($im, 40, 15, 120, 'Ressource Protected', $tc);
	
	return $im;
}







?>