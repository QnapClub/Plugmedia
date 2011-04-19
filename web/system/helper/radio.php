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

function generateUserBar($id_user)
{
	global $DB;
	$DB->query("SELECT last_access_date, song FROM radio_listener WHERE id_listener='$id_user' ORDER BY last_access_date DESC LIMIT 1 OFFSET 0","");
	$val = $DB->fetchrow();
	
	$fontsize = 8;
	$fontangle = "0";
	$imagewidth = 320;
	$imageheight = 19;
	$font = "system/helper/fonts/verdana.ttf";
	
	$nom_image = "system/views/common/img/radio/bg.png"; 
	$texte = ucwords(strtolower(stripslashes($val['song'])));  
	
	header ("Content-type: image/png");
	$image = imagecreatefrompng($nom_image);
	$blanc = imagecolorallocate($image, 255, 255, 255);
	
	$box = @imageTTFBbox($fontsize,$fontangle,$font,$texte);
	$textwidth = abs($box[4] - $box[0]);
	$textheight = abs($box[5] - $box[1]);

	$xcord = $imagewidth - ($textwidth)-6; // 2 = some space from right side.
	$ycord = 14;
	ImageTTFText ($image, $fontsize, $fontangle, $xcord, $ycord, $blanc, $font, $texte);
	
	imagepng($image);
}



?>