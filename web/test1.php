<?php


require_once 'system/core/frontcontroller.php';
/*$MD =& load_class('Metadata');
$out = $MD->saveExifData('/share/Qmultimedia/pictures/colloseum_geotag.jpg');


$latitude = exifToCoordinate	($out['gpslatituderef'], split(",",$out['gpslatitude']));
$longitude = exifToCoordinate($out['gpslongituderef'], split(",",$out['gpslongitude']));

echo '<a href="http://www.google.com/maps?z=16&ll='.$latitude.','.$longitude.'&t=h"> LINK </a>';
define("MAPS_HOST", "maps.google.com");
define("KEY", "ABQIAAAAiHKKQKLmyQFjoujiK0JnGhSy2SfIjBI9g2M3ZopfOXFjuBbQDRS3oov_TwIyAHpIUxvpZXEHxFESLQ");


echo '<br>';
// Initialize delay in geocode speed
$delay = 0;
$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;

// Iterate through the rows, geocoding each address
$address = "Rue de pontillas, 57C, belgique";

$request_url = $base_url . "&q=" . urlencode($address);
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
		echo $lat;
		echo '<br>';
		echo $lng;
	}else if (strcmp($status, "620") == 0) {
			  // sent geocodes too fast
			  return false;
		} else {
			  // failure to geocode
				echo 'oops';
	
		}
	
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

function getCoordinates($filename) {
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
}

function coordinate2DMS($coordinate, $pos, $neg) {
	$sign = $coordinate >= 0 ? $pos : $neg;
	
	$coordinate = abs($coordinate);
	$degree = intval($coordinate);
	$coordinate = ($coordinate - $degree) * 60;
	$minute = intval($coordinate);
	$second = ($coordinate - $minute) * 60;
	
	return sprintf("%s %d&#xB0; %02d&#x2032; %05.2f&#x2033;", $sign, $degree, $minute, $second);
}




*/



//loadHelper ('search');
//searchItems('word list Temp3"my single phrase"', true,true,true);
/*
$tab_lang = $CONFIGDB->getValue('EXTENSION_MOV');

print_r ($tab_lang);




var_dump ($tab_lang);

	



//Transcode: AVI, MP4, M4V, MPG, MPEG, RM, RMVB, WMV
$val = base64_encode(serialize(
array
(
    0 => utf8_decode("3gp"),
    1 => utf8_decode("avchd"),
    2 => utf8_decode("mkv"),
    3 => utf8_decode("wrap")	
)

));

echo "<br>";
echo $val;
echo "<br> YTozOntpOjA7czo0OiJtcGVnIjtpOjE7czozOiJtb3YiO2k6MjtzOjM6ImF2aSI7fQ==";

echo "<br><br>";

var_dump  (unserialize(base64_decode($val))); 

*/

/*
$queue =& load_class('Queue');
//$queue->performQueue('video');
$random = rand(1,20);
if ($queue->putItem('movie_convert', serialize(array('id_movie'=>'442601','flv_convert'=>true, 'mobile_convert'=>false,'extract_thumb'=>true )), '180116'))
		echo "inserted";
	else
		echo "something wrong";


$queue->performQueue('movie_convert',10);

//echo '<br><br>'.$queue->getProcessed();

//$queue->changeNiceStatus('apache1', 10);


//$queue->performMovieEncoding();


//sleep(30);
*/
//loadHelper ('administration');
//dropRepository();
$queue =& load_class('Queue');

$random = rand(1,20);
if ($queue->putItem('movie_convert', serialize(array('id_movie'=>'442592','flv_convert'=>true, 'mobile_convert'=>false,'extract_thumb'=>true )), '180116'))
		echo "inserted";
	else
		echo "something wrong";


$queue->performQueue('movie_convert',10);
?>
