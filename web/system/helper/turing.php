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

function getCode_turing()
{
	$session =& load_class('Session');
	if (!$code = $session->getData('turing_code'))
	{
		$code=mt_rand(100000,999999);		// Code en chiffre
		$session->setData('turing_code',$code);
	}
	
	return $code;
}


function check_turing($code)
{
	$session =& load_class('Session');
	
	$code_entre=strtoupper($code);
	
	if (!$code = $session->getData('turing_code'))
	{
		return FALSE;		// INCORRECT CODE
	}
	else
	{
		$session->removeData('turing_code');
		if ($code_entre!=$code)						
		{
			return FALSE;		// CODE INCORRECT
		}
		else
		{
			return TRUE;
		}
	}
	
}
//----------------------------------------------------------------------------------------------------------------------------------

function getPicture()
{
	$session =& load_class('Session');

	$code = getCode_turing();	// always start new generation...
	$largeur=107;
	$hauteur=30;
	$img = imagecreate($largeur, $hauteur) or die("Impossible de creer un flux Image GD");
	
	// Les couleurs...
	$bgc = imagecolorallocate($img, 39, 101, 53);		
	$black = imagecolorallocate($img, 0, 0, 0);
	$white = imagecolorallocate($img, 235, 235, 235);
	$gris = imagecolorallocate($img, 150, 150, 150);
	
	// Remplissage du fond
	imagefilledrectangle($img, 0, 0, $largeur, $hauteur, $white);
	
	// barres diagonales
	imageline ($img, 0, 10, 10, 0, $gris);
	imageline ($img, 0, 20, 20, 0, $gris);
	imageline ($img, 0, 30, 30, 0, $gris);
	imageline ($img, 0, 40, 40, 0, $gris);
	imageline ($img, 0, 50, 50, 0, $gris);
	imageline ($img, 0, 60, 60, 0, $gris);	
	imageline ($img, 0, 70, 70, 0, $gris);	
	imageline ($img, 0, 80, 80, 0, $gris);
	imageline ($img, 0, 90, 90, 0, $gris);
	imageline ($img, 0, 100, 100, 0, $gris);
	imageline ($img, 0, 110, 110, 0, $gris);
	imageline ($img, 0, 120, 120, 0, $gris);
				
	imageline ($img, 50, 50, 100, 0, $gris);
	imageline ($img, 100, 50, 150, 0, $gris);
	imageline ($img, 50, 0, 50, 50, $gris);
	imageline ($img, 100, 0, 100, 50, $gris);


	//horizontales
	imageline ($img, 0, 10, 150, 10, $gris);
	imageline ($img, 0, 20, 150, 20, $gris);
	imageline ($img, 0, 30, 150, 30, $gris);
	imageline ($img, 0, 40, 150, 40, $gris);
	
	
	// Ecriture du code (le premier 5 est la taille - de la police par défaut - la plus grande dans GD)
	$hor_pos=7; // position horizontale, à incrémenter à chaque fois ! (au hasard... :) )
	for($i=0;$i<strlen($code);$i++)
	{	
		$posit_vert = 35;
		ImageTTFText($img,15,0,$hor_pos,23,$gris,BASEPATH."helper/fonts/futuram.ttf",substr($code,$i,1));
		$hor_pos+=16;
	}
	
	return $img;
	
}



?>