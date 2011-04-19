<?
/*

 */
function smarty_modifier_transforme_rating($id, $total_votes, $total_value)
{
	require_once '/home/comicway/domains/comicway.net/public_html/system/helper/rating.php';
	echo displayRatingStatic($id, 5, $total_votes, $total_value);
}

?>