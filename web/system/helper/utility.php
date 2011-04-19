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

function hex2bin($h)
  {
  if (!is_string($h)) return null;
  $r='';
  for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
  return $r;
  }





// Function to create a new random token
// e.g. createToken('UG8D-', 3, 4)
// Might produce: UG8D-6T8Y-FCK7-09PL
function createToken($tokenprefix, $sections, $sectionlength)
{
	// Declare salt and prefix
	$token = "";
	$token.= $tokenprefix;
	$salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';    

	// Prepare randomizer
	srand((double)microtime() * 1000000);    

	// Create the token
	for($i=0; $i< $sections; $i++)
	{
		for($n=0; $n<$sectionlength; $n++)
		{
			$token.=substr($salt, rand() % strlen($salt), 1);
		}    

		if($i<($sections-1)){ $token.='-'; }
	}    

	// Return the token
	return $token;
}    

function getLanguageJavascript()
{
/*	global $tab_lang;
	global $current_lang;
	global $i18n; 
	$var =  "function reload_wholepage(moreParameters){window.location='./?'+moreParameters;}";
	
	$var .= "var myCycleButton = new Ext.CycleButton({";
	$var .= "tooltip: '".$i18n->translate('LANGUAGE')."',";
	$var .= "items: [";
	end($tab_lang);
	$last = key($tab_lang);
	
	foreach ($tab_lang as $key=>$value)
	{
		$var .= "{";
		$var .= "id: 'lang_".utf8_encode($key)."',";
		$var .= "text: '".utf8_encode($value)."',";
		$var .= "iconCls: 'flag_".utf8_encode($key)."',";
		if ($current_lang == $key)
			$var .= "checked: true";
		else
			$var .= "checked: false";
		$var .= "}";
		if ($key != $last)
			$var .= ",";	
	}
	$var .= "],";
	$var .= "
		changeHandler:function(btn, item){
				reload_wholepage('&'+item.id.replace(/_/,\"=\"));
		}		
	";	
	$var .= "});";
	return $var;*/

}


function generateToolbar()
{
	global $is_loggedin;
	global $user_info;
	global $i18n; 
	$var = '';
	
	
	$var .="var PM_toolbar = { init: function (){";

	if (!$is_loggedin)
	{
		// LOGIN BUTTON
		$var .="	
			$('#alone').button({ 
				icons: {primary: 'icon_key' }
			}).click(function(){ PM.lauch_login_windows();  });	
			";
	}
	else
	{
		// Display Logout AND Dropdown info	
		
		$var .="
				$('#info_mbr').button( {
					text: true,
					icons: {primary: 'avatar_tb', secondary: 'ui-icon-triangle-1-s'	}
				})
				.click( function() {
					var menu = $(this).nextAll('ul.info_mbr_menu').show().position({
						my: 'left top',
						at: 'left bottom',
						of: this
					});
					menu.mouseleave(function(){ $(this).hide() });
				
					$(document).one('click', function() {
						menu.hide();
					});
					return false;
				})
				.nextAll('ul.info_mbr_menu')
				.hide()
				.menu();
				// Catch all click on the language button and redirect to the right page
				/*$('ul.info_mbr_menu > li.ui-menu-item').click(function(){
					window.location = $(this).children().attr('href');
				});*/
		
		
		
		
		
		
			";		
		
		
		
		
		
		
		$var .="		
			$('#logout').button({ 
				text:false,
				icons: {primary: 'icon_logout' }
			}).click(function(){ window.location = 'api.php?ac=logout' });	
		
			";
		
		
		
	}
	// HELP BUTTON
	$var .="		
			$('#help').button({ 
				text:false,
				icons: {primary: 'icon_help' }
			}).click(function(){ //
			
			
					$('#help-modal-content').modal({
						containerCss :{
							height:150,
							width:400
						},
						onOpen: function (dialog) {
							dialog.overlay.fadeIn('fast');
							dialog.container.fadeIn('fast', function () { dialog.data.fadeIn('slow');} );
						}			
					});
					return false;			
			
			
			 });	
		
			";

	// SEARCH BUTTON
	$var .="		
			$('#search_format').buttonset();
			$('#search_btn').button({ 
				text:false,
				icons: {primary: 'icon_search' }
			}).click(function(){ 
			

					$('#search-modal-content').modal({
						containerCss :{
							height:200,
							width:400
						},
						onOpen: function (dialog) {
							dialog.overlay.fadeIn('fast');
							dialog.container.fadeIn('fast', function () { dialog.data.fadeIn('slow');} );
						}			
					});
					return false;			
			
			
			});	
		
			";			
			
	// LANG SPLIT BUTTON
	$var .="			
			$('#select').button( {
				text: true,
				icons: {primary: 'flag_'+PM_config.current_lang, secondary: 'ui-icon-triangle-1-s'	}
			})
			.click( function() {
				var menu = $(this).nextAll('ul.lang_menu').show().position({
					my: 'right top',
					at: 'right bottom',
					of: this
				});
				menu.mouseleave(function(){ $(this).hide() });
			
				$(document).one('click', function() {
					menu.hide();
				});
				return false;
			})
			.nextAll('ul.lang_menu')
			.hide()
			.menu();
			// Catch all click on the language button and redirect to the right page
			$('ul.lang_menu > li.ui-menu-item').click(function(){
				window.location = $(this).children().attr('href');
			});
	";
	$var .= '} }';
	
	
	echo $var;	
	
}


function trimUltime($chaine){
$chaine = trim($chaine);
$chaine = str_replace("\t", " ", $chaine);

$chaine = eregi_replace("[ ]+", " ", $chaine);
return $chaine;
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}


function encode_ip($dotquad_ip)
{
	$ip_sep = explode('.', $dotquad_ip);
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

/**
 *
 * @convert seconds to hours minutes and seconds
 *
 * @param int $seconds The number of seconds
 *
 * @return string
 *
 */
 
function secondsToWords($seconds)
{
    $array = get_time_from_sec($seconds);
	$seconds = "";
	if ($array[0] != 0)
		$seconds .= $array[0]."day ";
	if ($array[1] != 0)
		$seconds .= $array[1]."hour ";
	if ($array[2] != 0)
		$seconds .= $array[2]."min ";
	if ($array[0] == 0 && $array[1] == 0 && $array[2] == 0)
		$seconds .= $array[3]."sec ";	
			
    return $seconds;
}

function get_time_from_sec($number_of_seconds)
{
	// days
	$day_in_sec = 60*60*24;
	for($days = 0; $day_in_sec < $number_of_seconds; $days++)
	{
		$number_of_seconds -= $day_in_sec;
	}
	
	// hours
	$hours_in_sec = 60*60;
	for($hours = 0; $hours_in_sec < $number_of_seconds; $hours++)
	{
		$number_of_seconds -= $hours_in_sec;
	}

	// minutes
	$min_in_sec = 60;
	for($min = 0; $min_in_sec < $number_of_seconds; $min++)
	{
		$number_of_seconds -= $min_in_sec;
	}
	
	return Array($days,$hours,$min,$number_of_seconds);

}


function redirectNonAuthorizedUser()
{
	header("Location: index.php");
	exit();
}



?>
