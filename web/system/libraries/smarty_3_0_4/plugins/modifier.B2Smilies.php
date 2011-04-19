<?
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:       modifier
 * Name:       B2Smilies
 * Purpose:    Converts smilies in string to <IMG SRC> references
 * Author:     Gavin Cowie ('Lifted' from b2++ code by 
 *             Donncha O Caoimh (http://blogs.linux.ie/xeer/))
 * Version:    0.1
 * Remarks:    Expects a b2 installation at www.yoursite.com/b2/
 *             as this is where the smiley gifs are got from.
 *             http://cork.linux.ie/filemgmt/viewcat.php?cid=4
 * ------------------------------------------------------------
 */
function smarty_modifier_B2Smilies($message) {

// the smiley IMG directory for IMG SRC tags
$smilies_directory = 'http://www.studenthelp.be/style_images/smileys';

	$b2smiliestrans = array(
		':matrix:'		=> '1.png',
		':-('			=> '10.png',
		':hum:'			=> '12.png',
		':deb:'			=> '13.png',
		':love:'		=> '14.png',
		';-)'			=> '15.png',
		':evil:'		=> '16.png',
		':simpl:'		=> '17.png',
		':('			=> '19.png',
		':cry:'			=> '22.png',
		':mot:'			=> '23.png',
		':brique:'		=> '25.png',
		':pan:'			=> '26.png',
		':bomb:'			=> '27.png',
		':x'			=> '28.png',
		':huh:'			=> '4.png',
		':o'			=> '5.png',
		':dur:'			=> '7.png',
		':-)'			=> '8.png',
		':adore:'		=> 'adore.png',
		':afterboom:'	=> 'after_boom.png',
		':ah:'			=> 'ah.png',
		':amazing:'		=> 'amazing.png',
		':anger:'		=> 'anger.png',
		':bad_egg:'		=> 'bad_egg.png',
		':bad_smile:'	=> 'bad_smile.png',
		':baffle:'		=> 'baffle.png',
		':beated:'		=> 'beated.png',
		':big_smile:'	=> 'big_smile.png',	
		':boss:'		=> 'boss.png',
		':byebye:'		=> 'byebye.png',							
		':confident:'	=> 'confident.png',
		':confus:'		=> 'confuse.png',
		':pleure:'		=> 'cry.png',
		':shock:'		=> 'electric_shock.png',
		':exciting:'	=> 'exciting.png',
		':eyedrop:'		=> 'eyes_droped.png',
		':feelg:'		=> 'feel_good.png',
		':girl:'		=> 'girl.png',	
		':greedy:'		=> 'greedy.png',
		':grimace:'		=> 'grimace.png',
		':dent:'		=> 'haha.png',
		':hell:'		=> 'hell_boy.png',
		':horror:'		=> 'horror.png',
		':money:'		=> 'money.png',	
		':coeur:'		=> 'red_heart.png',
		':rock:'		=> 'rockn_roll.png',		
		':sad:'			=> 'sad.png',
		':scorn:'		=> 'scorn.png',		
		':shame:'		=> 'shame.png',
		':shc:'			=> 'shocked.png',			
		':spiderman:'	=> 'spiderman.png',
		':reve:'		=> 'still_dreaming.png',			
		':superman:'	=> 'super_man.png',
		':surrender:'	=> 'surrender.png',			
		':kiss:'		=> 'sweet_kiss.png',
		':devilr:'		=> 'the_devil.png',					
		':ironman:'		=> 'the_iron_man.png',
		':toosad:'		=> 'too_sad.png',			
		':unhappy:'		=> 'unhappy.png',
		':victory:'		=> 'victory.png',							
		':quoi:'		=> 'what.png',
		':quoiquoi:'	=> 'what_.png',			

	);

	# sorts the smilies' array
	if (!function_exists('smiliescmp')) {
		function smiliescmp ($a, $b) {
	   		if (strlen($a) == strlen($b)) {
			return strcmp($a, $b);
	   	}
		return (strlen($a) > strlen($b)) ? -1 : 1;
		}
	}
	uksort($b2smiliestrans, 'smiliescmp');

	# generates smilies' search & replace arrays
	foreach($b2smiliestrans as $smiley => $img) {
		$b2_smiliessearch[] = $smiley;
		$smiley_masked = '';
		for ($i = 0; $i < strlen($smiley); $i = $i + 1) {
			$smiley_masked .= substr($smiley, $i, 1).chr(160);
		}
		$b2_smiliesreplace[] = "<img src='$smilies_directory/$img' alt='$smiley' />";
	}

	return str_replace($b2_smiliessearch, $b2_smiliesreplace, $message);
}

?>