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

function getSlideshowRss($dir,$sorting=false, $order=false)
{
	global $SESSION;
	$directory =& load_class('Directory2');
	
	$smarty =& load_class('Smarty');
	$smarty->setRssFeed();
	
			
	if(!$smarty->is_cached('rss_slideshow.tpl', "rss_slideshow_".$dir."_".$SESSION->getData('order')."_".$SESSION->getData('tris')))	// IS THERE A CACHE?
	{
		$directory->setRoot($dir);
		
		$list = $directory->listDirectory(false,true);
		$smarty->assign("list",$list);
		$current_dir = $directory->getInfoFromRoot();
		$smarty->assign("current_dir",$current_dir);
	}
	
	
	$smarty->display('rss_slideshow.tpl', "rss_slideshow_".$dir."_".$SESSION->getData('order')."_".$SESSION->getData('tris'));

}

function getSlideshow_param($dir)
{
	$smarty =& load_class('Smarty');
	$smarty->setRssFeed(false);
	$smarty->assign("dir_slideshow",$dir);
	$smarty->display('rss_slideshow_params.tpl', "rss_slideshow_params");	
}


?>