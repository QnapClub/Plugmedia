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

set_time_limit(0);

require_once 'system/core/frontcontroller.php';

$SMARTY->assign("directory_id",$_GET['id']);
$SMARTY->assign("reference",$_GET['ref']);

if (ob_get_level() == 0) ob_start();
$value = $SMARTY->fetch('view_waiting_page.tpl');
echo $value;
ob_flush();
flush();

global $i18n;

$index =& load_class('Indexing'); 
$index->updateOutdatedDirectory($_GET['id']);

echo "<div id='waiting_message'><div id='content_a'>".$i18n->translate('OUTDATED_REDIRECTING', '')." ...</div></div>";
echo "<script language='javascript'>window.location.replace('list.php?dir=".$_GET['id']."&ref=".$_GET['ref']."')</script>";
echo "</body></html>";


?>