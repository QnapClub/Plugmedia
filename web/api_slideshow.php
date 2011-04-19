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

// This API is build to handle custom formating URL (avoiding & that cause bugs in the slideshow)
// the ac will be formated as:
// api_slideshow.php?params=ac|dir=test|
require_once 'system/core/frontcontroller.php';

$champs = explode ("|", $_GET['params']);
$boucle=1;
while ($boucle < sizeof($champs)) {
	$val = explode ("=", $champs[$boucle]);
	$_GET[$val[0]] = $val[1];
	$boucle++;
}

switch ($champs[0])
{
	case 'slideshow':
		loadHelper ('slideshow');
		getSlideshowRss($_GET['dir']);
	break;	
	case 'slideshow_param':
		loadHelper ('slideshow');
		getSlideshow_param($_GET['dir']);
	break;	

	
}


?>