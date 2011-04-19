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



// TODO SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
//Here's the SQL statement that will find the closest 20 locations that are within a radius of 25 miles to the 37, -122 coordinate. It calculates the distance based on the latitude/longitude of that row and the target latitude/longitude, and then asks for only rows where the distance value is less than 25, orders the whole query by distance, and limits it to 20 results. To search by kilometers instead of miles, replace 3959 with 6371. 
// find nearly location to 37 and -122



// transform exif coordinates to latitude and longitude
// IN: coordinate from EXIF
// OUT: Array(Latitude, Longitude)
// $MD =& load_class('Metadata');
// $out = $MD->saveExifData(filename);
// $latitude = exifToCoordinate	($out['gpslatituderef'], split(",",$out['gpslatitude']));
// $longitude = exifToCoordinate($out['gpslongituderef'], split(",",$out['gpslongitude']));
//
function getCoordinate($GPSLatitudeRef, $GPSLatitude, $GPSLongitudeRef, $GPSLongitude)
{
	
return array ("latitude"=>exifToCoordinate($GPSLatitudeRef, $GPSLatitude), "longitude"=>exifToCoordinate($GPSLongitudeRef, $GPSLongitude));	
	
} 





function exifToNumber($value, $format) {
	$spos = strpos($value, '/');
	if ($spos === false) {
		return sprintf($format, $value);
	} else {
		list($base,$divider) = split("/", $value, 2);
		if ($divider == 0) 
			return sprintf($format, 0);
		else
			return sprintf($format, ($base / $divider));
	}
}

function exifToCoordinate($reference, $coordinate) {
	if ($reference == 'S' || $reference == 'W')
		$prefix = '-';
	else
		$prefix = '';
		
	return $prefix . sprintf('%.6F', exifToNumber($coordinate[0], '%.6F') +
		(((exifToNumber($coordinate[1], '%.6F') * 60) +	
		(exifToNumber($coordinate[2], '%.6F'))) / 3600));
}


/*function getCoordinates($filename) {
	if (extension_loaded('exif')) {
		$exif = exif_read_data($filename, 'EXIF');
		
		if (isset($exif['GPSLatitudeRef']) && isset($exif['GPSLatitude']) && 
			isset($exif['GPSLongitudeRef']) && isset($exif['GPSLongitude'])) {
			return array (
				exifToCoordinate($exif['GPSLatitudeRef'], $exif['GPSLatitude']), 
				exifToCoordinate($exif['GPSLongitudeRef'], $exif['GPSLongitude'])
			);
		}
	}
}*/




function coordinate2DMS($coordinate, $pos, $neg) {
	$sign = $coordinate >= 0 ? $pos : $neg;
	
	$coordinate = abs($coordinate);
	$degree = intval($coordinate);
	$coordinate = ($coordinate - $degree) * 60;
	$minute = intval($coordinate);
	$second = ($coordinate - $minute) * 60;
	
	return sprintf("%s %d&#xB0; %02d&#x2032; %05.2f&#x2033;", $sign, $degree, $minute, $second);
}


function convertAdressToCoordinates($string_adress)
{
	

	define("MAPS_HOST", "maps.google.com");
	define("KEY", "ABQIAAAAiHKKQKLmyQFjoujiK0JnGhSy2SfIjBI9g2M3ZopfOXFjuBbQDRS3oov_TwIyAHpIUxvpZXEHxFESLQ");
	
	
	echo '<br>';
	// Initialize delay in geocode speed
	$delay = 0;
	$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;
	

	
	$request_url = $base_url . "&q=" . urlencode($string_adress);
	$xml = simplexml_load_file($request_url);
	if($xml ===  FALSE)
	{
	   echo 'error';
	   return false;
	}
	else {
	
	
		$status = $xml->Response->Status->code;
		if (strcmp($status, "200") == 0) {
			  // Successful geocode
			  $geocode_pending = false;
			  $coordinates = $xml->Response->Placemark->Point->coordinates;
			  $coordinatesSplit = split(",", $coordinates);
			  // Format: Longitude, Latitude, Altitude
			  $lat = $coordinatesSplit[1];
			  $lng = $coordinatesSplit[0];
	
		}else if (strcmp($status, "620") == 0) {
			  // sent geocodes too fast
			  return false;
		} else {
			  // failure to geocode
				return false;
	
		}
	}

	
}


?>