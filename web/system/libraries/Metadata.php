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
 
class CORE_Metadata {

	var $file_id;
	var $filename;
	var $path;
	var $hash;
	var $extension;	
	var $config;
	var $DB;
	
	public function CORE_Metadata()
	{
	
	}

	public function extractMetadata($id_file, $filename, $path, $hash, $extension)
	{
		global $db_config, $DB;
		$this->config = $db_config;
		
		$this->file_id = $id_file;
		$this->filename = $filename;		
		$this->path = $path;				
		$this->hash = $hash;		
		$this->extension = $extension;					
		$this->DB = $DB;

		if (in_array(strtolower($this->extension),$this->config['EXTENSION_EXIF']))
		{
			$this->extractExifInfo();
		}
		else if (in_array(strtolower($this->extension), $this->config['EXTENSION_SONG']))
		{
			$this->extractMetadataSong();
		}
		else if (in_array(strtolower($this->extension), $this->config['EXTENSION_RAW']))
		{
			$this->extractMetadataRawFile();
		}		
		
		$this->DB->query("UPDATE files SET metadata_extracted = 1 WHERE id = '".$this->file_id."'","extractMetadata");
	}

	private function extractMetadataRawFile()
	{
		//dcraw -e -c _DSC0639.NEF > test.jpg
		$target_name = str_ireplace(".".$this->extension, ".jpg", strtolower($this->filename));
		if (!is_file ("/opt/bin/dcraw"))
		{
			return false;
			exit();
		}
		exec ("/opt/bin/dcraw -e -c ".$this->path.$this->filename." > ".BASEPATH."_cache/thumb/".$target_name);
		$exif_data = $this->saveExifData(iconv("UTF-8","UTF-8//IGNORE",BASEPATH."_cache/thumb/".$target_name));	

		if (array_key_exists('str_columns',$exif_data))
		{
			if (in_array('datetimeoriginal',$exif_data))
			{
				$this->DB->query("UPDATE files SET original_date = '$original_date' WHERE id = '".$this->file_id."'","extractExifInfo");
			}
			if (!$this->DB->query ("INSERT into metadata_exif (files_id, ".$exif_data['str_columns'].") VALUES ('".$this->file_id."',".$exif_data['str_value'].")","extractExifInfo"))
			{
				// trying to update
				$this->DB->query("UPDATE metadata_exif SET ".$exif_data['update_string']." WHERE files_id='".$this->file_id."'","extractExifInfo");
			}

		}
		
		loadHelper ('thumbnail');	
		if (substr($this->path, -1) == '/')
			$path = substr($this->path,0,strlen($this->path)-1);
		generateThumbWithFilepath($target_name, BASEPATH."_cache/", "thumb", $exif_data['orientation'], 'jpg', $this->hash, false, 'small',$this->file_id, $path);
		generateThumbWithFilepath($target_name, BASEPATH."_cache/", "thumb", $exif_data['orientation'], 'jpg', $this->hash, false, 'normal',$this->file_id, $path);		
		
		unlink (BASEPATH."_cache/thumb/".$target_name);
		
	}

	private function extractExifInfo()
	{
		
		$exif_data = $this->saveExifData(iconv("UTF-8","UTF-8//IGNORE",$this->path.$this->filename));	
			
		if (array_key_exists('str_columns',$exif_data))
		{
			if (in_array('datetimeoriginal',$exif_data))
			{
				$this->DB->query("UPDATE files SET original_date = '$original_date', metadata_extracted WHERE id = '".$this->file_id."'","extractExifInfo");
			}
			if (!$this->DB->query ("INSERT into metadata_exif (files_id, ".$exif_data['str_columns'].") VALUES ('".$this->file_id."',".$exif_data['str_value'].")","extractExifInfo"))
			{
				// trying to update
				$this->DB->query("UPDATE metadata_exif SET ".$exif_data['update_string']." WHERE files_id='".$this->file_id."'","extractExifInfo");
			}

		}
		return true;	
	}
	

	private function extractMetadataSong()
	{
		log_message('debug', "Try to extract metadataSong");
		$extracted_info = false;
		// Can we extract ID3
		if ((bool) $this->config['ID3_EXTRACT'])
		{
			
			$mp3 =& load_class('ID3');
			$mp3->addFilname($this->path.$this->filename);
			if ($info = $mp3->getInfo())
				$extracted_info = true;
			
		}
		
		// Can we use regex to extract album and song name
		// album name - songname
		// ereg( '^(.*)\-(.*)\-(.*)$', $string, $matches );
		if ((bool) $this->config['FILENAME_EXTRACT'] && !$extracted_info)
		{
			// TODO
		}
				
	
		
		
		
		// INSERT SONG metadata into database
		if (!$this->DB->query("INSERT INTO metadata_id3 VALUES(".$this->file_id.",'".pg_escape_string(trim($info['track']))."','".pg_escape_string(trim($info['title']))."','".pg_escape_string(trim($info['album']))."','".pg_escape_string(trim($info['gender']))."','".pg_escape_string(trim($info['year']))."','".pg_escape_string(trim($info['artist']))."')","extractId3Tag"))
		{
			// trying to update infos
			$this->DB->query("UPDATE metadata_id3 SET track='".pg_escape_string(trim($info['track']))."', title='".pg_escape_string(trim($info['title']))."', album='".pg_escape_string(trim($info['album']))."', gender='".pg_escape_string(trim($info['gender']))."', year='".pg_escape_string(trim($info['year']))."', artist='".pg_escape_string(trim($info['artist']))."'  WHERE files_id='".$this->file_id."'","extractId3Tag");
		}			
	
		
		
		/*
		NEED TO VALIDATE BECAUSE picture cannot be already indexed...
		 
		// First try to get cover in the directory
		if ((bool) $this->config_array['EXTRACT_COVER_FROM_DIRECTORY'])
		{
			print_r ($this->findMp3CoverInDirectory('jpg', array('front','cover','folder'), $path, $false));
			
		}
		*/
		$finded_cover = false;
		// try to get cover in the ID3 tag (if it was extracted)
		if ((bool) $this->config['EXTRACT_COVER_FROM_ID3'] && $extracted_info)
		{
			if ($info['all']['id3v2']['APIC'][0]['data'])
			{
				$cover = $info['all']['id3v2']['APIC'][0]['data'];		
				$finded_cover = true;
				$cover_extension = 'jpg';
			}	
		}
		// try to get cover from Last.fm
		if ((bool) $this->config['EXTRACT_COVER_FROM_LASTFM'] && $extracted_info && !$finded_cover)
		{
			if ($cover_info = $this->lastFMAPISearch($info['artist'], $info['album']))
			{
				$cover = $cover_info['cover_data'];
				$finded_cover = true;
				$cover_extension = $cover_info['cover_extension'];
			}
			else
			{
				// maybe the song is an album...
				if ($cover_info = $this->lastFMAPISearch($info['artist'], $info['title']))
				{
					$cover = $cover_info['cover_data'];
					$finded_cover = true;
					$cover_extension = $cover_info['cover_extension'];
				}
			}
		
					
			
			
		}
		
		if ($finded_cover)
		{
			// sanitize all
			$info['artist'] = $this->filterStringName($info['artist']);
			$info['album'] = $this->filterStringName($info['album']);
			
			file_put_contents(BASEPATH."_cache/thumb/".$info['artist']."-".$info['album'].".".$cover_extension,$cover);
			// try to get filesize..
			$size = @getimagesize(BASEPATH."_cache/thumb/".$info['artist']."-".$info['album'].".".$cover_extension,$cover);
			
			if (filesize(BASEPATH."_cache/thumb/".$info['artist']."-".$info['album'].".".$cover_extension) > 30 && $size['mime'])
			{
				loadHelper ('thumbnail');	
				generateThumbWithFilepath($info['artist']."-".$info['album'].".".$cover_extension, BASEPATH."_cache/", "thumb", '', $cover_extension, $this->hash, false, 'small',$this->file_id, '/cover');
				generateThumbWithFilepath($info['artist']."-".$info['album'].".".$cover_extension, BASEPATH."_cache/", "thumb", '', $cover_extension, $this->hash, false, 'normal',$this->file_id, '/cover');
				
				unlink (BASEPATH."_cache/thumb/".$info['artist']."-".$info['album'].".jpg");
			}
		}
		
			
	
	}

	function filterStringName($str)
	{
		$entree = array('#[áàâäã]#','#[ÁÀÂÄÃ]#','#[éèêë]#','#[ÉÈÊË]#','#[íìîï]#','#[ÍÌÎÏ]#','#[óòôöõ]#','#[ÓÒÔÖÕ]#','#[úùûü]#','#[ÚÙÛÜ]#','#ÿ#','#Ÿ#','#ç#','#Ç#','# #','#[^a-zA-Z0-9_-]#');
		$sortie = array('a','A','e','E','i','I','o','O','u','U','y','Y','c','C','_','');
		return preg_replace($entree,$sortie,$str);
	}

	function lastFMAPISearch($artist, $album_or_song)
	{
		log_message('debug', "Searching in LastFM");
		$xml_request_url = 'http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=db41910f8425ecb71d96094bc18f9221&artist='.urlencode($artist).'&album='.urlencode($album_or_song);
		$xml = @file_get_contents($xml_request_url);
		
		@preg_match("/<image size=\"extralarge\">(.*?)<\/image>/",$xml,$regs);
		
		if (array_key_exists('1',$regs) && $regs[1]!='')
		{	
			$cover_url = $regs[1];
			$cover_info['cover_data'] = file_get_contents($cover_url);
		   	$bouts = explode(".", $cover_url);
    		$extension = array_pop($bouts);
			$cover_info['cover_extension'] = strtolower($extension);
			return $cover_info;
			
		}	
		else
			return false;	
	
	}


 	function saveExifData($filename)
 	{
		require_once (BASEPATH.'libraries/exif_metadata/EXIF.php');
        $exif_data =  get_EXIF_JPEG( $filename );
        $exif_tags = $this->getExifTranslationArray();

		$str_columns ='';
		$str_values ='';
		$update_string = '';
		$array=array();

        // Canon Owner Name append to Artist
        if(isset($exif_data['Makernote_Tag']['Decoded Data'])
         && is_array($exif_data['Makernote_Tag']['Decoded Data']) 
         && isset($exif_data['Makernote_Tag']['Decoded Data'][0][9]['Text Value']))
        {
            $owner = $exif_data['Makernote_Tag']['Decoded Data'][0][9]['Text Value']; 
            if(isset($exif_data[0][315]['Text Value']))
            {
                $exif_data[0][315]['Text Value'] .= $owner;
            }
            else
            {
                $exif_data[0][315]['Text Value'] = $owner;
            }
             
        }
 
        foreach($exif_tags AS $key=>$value)
        {
            if($value != "")
            {

                $array_pieces = explode('/',$value);
                switch(count($array_pieces))
                {
                case 2:
                    if(isset($exif_data[ $array_pieces[0] ][ $array_pieces[1] ]['Text Value']))
                    {
						
						$exif_value = $exif_data[ $array_pieces[0] ][ $array_pieces[1] ]['Text Value'];
                        $array[strtolower($key)] = trim( $exif_value );
						$str_columns .= strtolower($key).', ';
						$str_values .= "'".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $exif_value )))."', ";
						$update_string .= strtolower($key)."='".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $exif_value )))."', ";
                    }
                break;
                case 5:
                    if(isset($exif_data[ $array_pieces[0] ][ $array_pieces[1] ][ $array_pieces[2] ][ $array_pieces[3] ][ $array_pieces[4] ]['Text Value']))
                    {
                        $exif_value = $exif_data[ $array_pieces[0] ][ $array_pieces[1] ][ $array_pieces[2] ][ $array_pieces[3] ][ $array_pieces[4] ]['Text Value'];
						$array[strtolower($key)] = trim( $exif_value );
						$str_columns .= strtolower($key).', ';
					
						$str_values .= "'".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $exif_value )))."', ";
						$update_string .= strtolower($key)."='".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $exif_value )))."', ";
                    }
                break;
                default:
                    echo "Error no valid path for key: ".$key." value: ".$value;
                break;
                }
            }
        }
        
        require_once (BASEPATH.'libraries/exif_metadata/JPEG.php');

		$jpeg_header_data = get_jpeg_header_data( $filename );
		$comment = get_jpeg_Comment( $jpeg_header_data );
		if(!empty($comment))
		{
			$array['jpgcomment'] = trim( $comment );
			$str_columns .= 'jpgcomment, ';
			$str_values .= "'".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $comment )))."', ";
			$update_string .= "jpgcomment='".iconv("UTF-8","UTF-8//IGNORE",pg_escape_string(trim( $comment )))."', ";
		}

    $str_columns = substr($str_columns,0,strlen($str_columns)-2);
    $str_values = substr($str_values,0,strlen($str_values)-2);
	$update_string = substr($str_values,0,strlen($str_values)-2);
        
	if (count($array)!=0)
	{
		$array['str_columns'] = $str_columns;
		$array['str_value'] = $str_values; 
		$array['update_string'] = $update_string; 
	}
	
	return ($array);		
    	
 	}
	

	
        		  	




    
   /**
     * get array with exiftags and path where they are
     * keys are used to create the database table
     * 
     * warning: if making changes in the keys of the array, we will need to
     * update the db!!
     */
    function getExifTranslationArray()
    {
        return Array(
        // TIFF Rev. 6.0 Attribute Information
            // A. Tags relating to image data structure
            //'ImageWidth' => '',
            //'ImageLength' => '',
            //'BitsPerSample' => '',
            //'Compression' => '',
            //'PhotometricInterpretation' => '',
            'Orientation' => '0/274',
            //'SamplesPerPixel' => '',
            //'PlanarConfiguration' => '',
            //'YCbCrSubSampling' => '',
            'YCbCrPositioning' => '0/531',
            'XResolution' => '0/282',
            'YResolution' => '0/283',
            'ResolutionUnit' => '0/296',
            
            // B. Tags relating to recording offset
            //'StripOffsets' => '',
            //'RowsPerStrip' => '',
            //'StripByteCounts' => '',
            //'JPEGInterchangeFormat' => '',
            //'JPEGInterchangeFormatLength' => '',
            
            // C. Tags relating to image data characteristics
            //'TransferFunction' => '',
            //'WhitePoint' => '',
            //'PrimaryChromaticities' => '',
            //'YCbCrCoefficients' => '',
            //'ReferenceBlackWhite' => '',
            
            // D. Other tags
            'DateTime' => '0/306',
            'ImageDescription' => '0/270',
            'Make' => '0/271',
            'Model' => '0/272',
            'Software' => '0/305',
            'Artist' => '0/315',
            'Copyright' => '0/33432',
            
        // Exif IFD Attribute Information
            // A. Tags Relating to Version
            //'ExifVersion' => '0/34665/Data/0/36864',
            //'FlashpixVersion' => '0/34665/Data/0/40960',
            
            // B. Tag Relating to Image Data Characteristics
            'ColorSpace' => '0/34665/Data/0/40961',
            
            // C. Tags Relating to Image Configuration
            'ComponentsConfiguration' => '0/34665/Data/0/37121',
            'CompressedBitsPerPixel' => '0/34665/Data/0/37122',
            'PixelXDimension' => '0/34665/Data/0/40962',
            'PixelYDimension' => '0/34665/Data/0/40963',
            
            // D. Tags Relating to User Information
            //'MakerNote' => '', too big to store!!
            'UserComment' => '0/34665/Data/0/37510',
            
            // E. Tag Relating to Related File Information
            //'RelatedSoundFile' => '',
            
            // F. Tags Relating to Date and Time
            'DateTimeOriginal' => '0/34665/Data/0/36867',
            'DateTimeDigitized' => '0/34665/Data/0/36868',
            //'SubSecTime' => '',
            //'SubSecTimeOriginal' => '',
            //'SubSecTimeDigitized' => '',
            
            // G. Tags Relating to Picture-Taking Conditions
            'ExposureTime' => '0/34665/Data/0/33434',
            'FNumber' => '0/34665/Data/0/33437',
            'ExposureProgram' => '0/34665/Data/0/34850',
            //'SpectralSensitivity' => '',
            'ISOSpeedRatings' => '0/34665/Data/0/34855',
            //'OECF' => '',
            'ShutterSpeedValue' => '0/34665/Data/0/37377',
            'ApertureValue' => '0/34665/Data/0/37378',
            'BrightnessValue' => '0/34665/Data/0/37379',
            'ExposureBiasValue' => '0/34665/Data/0/37380',
            'MaxApertureValue' => '0/34665/Data/0/37381',
            'SubjectDistance' => '0/34665/Data/0/37382',
            'MeteringMode' => '0/34665/Data/0/37383',
            'LightSource' => '0/34665/Data/0/37384',
            'Flash' => '0/34665/Data/0/37385',
            'FocalLength' => '0/34665/Data/0/37386',
            //'SubjectArea' => '',
            //'FlashEnergy' => '',
            //'SpatialFrequencyResponse' => '',
            'FocalPlaneXResolution' => '0/34665/Data/0/41486',
            'FocalPlaneYResolution' => '0/34665/Data/0/41487',
            'FocalPlaneResolutionUnit' => '0/34665/Data/0/41488',
            //'SubjectLocation' => '',
            //'ExposureIndex' => '',
            'SensingMethod' => '0/34665/Data/0/41495',
            'FileSource' => '0/34665/Data/0/41728',
            'SceneType' => '0/34665/Data/0/41729',
            //'CFAPattern' => '',
            'CustomRendered' => '0/34665/Data/0/41985',
            'ExposureMode' => '0/34665/Data/0/41986',
            'WhiteBalance' => '0/34665/Data/0/41987',
            'DigitalZoomRatio' => '0/34665/Data/0/41988',
            //'FocalLengthIn35mmFilm' => '',
            'SceneCaptureType' => '0/34665/Data/0/41990',
            'GainControl' => '0/34665/Data/0/41991',
            'Contrast' => '0/34665/Data/0/41992',
            'Saturation' => '0/34665/Data/0/41993',
            'Sharpness' => '0/34665/Data/0/41994',
            //'DeviceSettingDescription' => '',
            //'SubjectDistanceRange' => '',
            
            // H. Other Tags
            'ImageUniqueID' => '',
            
        // GPS Attribute Information
            // A. Tags Relating to GPS
            /*'GPSVersionID' => '0/34853/Data/0/0',*/
            'GPSLatitudeRef' => '0/34853/Data/0/1',
            'GPSLatitude' => '0/34853/Data/0/2',
            'GPSLongitudeRef' => '0/34853/Data/0/3',
            'GPSLongitude' => '0/34853/Data/0/4',
            /*'GPSAltitudeRef' => '0/34853/Data/0/5',
            'GPSAltitude' => '0/34853/Data/0/6',
            'GPSTimeStamp' => '0/34853/Data/0/7',
            'GPSSatellites' => '0/34853/Data/0/8',
            'GPSStatus' => '0/34853/Data/0/9',
            'GPSMeasureMode' => '0/34853/Data/0/10',
            'GPSDOP' => '0/34853/Data/0/11',
            'GPSSpeedRef' => '0/34853/Data/0/12',
            'GPSSpeed' => '0/34853/Data/0/13',
            'GPSTrackRef' => '0/34853/Data/0/14',
            'GPSTrack' => '0/34853/Data/0/15',
            'GPSImgDirectionRef' => '0/34853/Data/0/16',
            'GPSImgDirection' => '0/34853/Data/0/17',
            'GPSMapDatum' => '0/34853/Data/0/18',
            'GPSDestLatitudeRef' => '0/34853/Data/0/19',
            'GPSDestLatitude' => '0/34853/Data/0/20',
            'GPSDestLongitudeRef' => '0/34853/Data/0/21',
            'GPSDestLongitude' => '0/34853/Data/0/22',
            'GPSDestBearingRef' => '0/34853/Data/0/23',
            'GPSDestBearing' => '0/34853/Data/0/24',
            'GPSDestDistanceRef' => '0/34853/Data/0/25',
            'GPSDestDistance' => '0/34853/Data/0/26',
            'GPSProcessingMethod' => '0/34853/Data/0/27',
            'GPSAreaInformation' => '0/34853/Data/0/28',
            'GPSDateStamp' => '0/34853/Data/0/29',
            'GPSDifferential' => '0/34853/Data/0/30',
			*/
            
        // Custom Makernotes http://www.ozhiker.com/electronics/pjmt/jpeg_info/makernotes.html
            // there are several tags: camera settings etc. might be usefull

            // Canon
            // overwrite Artist => 0/315
            //'OwnerName' => 'Makernote_Tag/Decoded Data/0/9'
            
            // Casio
            
            // Fujifilm
            
            // Konica/Minolta
            
            // Nikon

            // Olympus
            
            // Panasonic
            
            // Ricoh

			//'JpegComment' => ''
        );
    }






}
 
?>
