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

function getCoolirisRss($dir,$sorting=false, $order=false)
{
	global $SESSION;

	$directory =& load_class('Directory2');
	
	
	
	
	$smarty =& load_class('Smarty');
	$smarty->setRssFeed();
	
	
			
	if(!$smarty->isCached('rss_small.tpl', "rss_small_".$dir."_".$SESSION->getData('order')."_".$SESSION->getData('tris')))	// IS THERE A CACHE?
	{
		$directory->setRoot($dir);
		$smarty->assign("list",$directory->listDirectory(false,true));
		$smarty->assign("current_dir",$directory->getInfoFromRoot());
	}
	

	$smarty->display('rss_small.tpl', "rss_small_".$dir."_".$SESSION->getData('order')."_".$SESSION->getData('tris'));

}


?>