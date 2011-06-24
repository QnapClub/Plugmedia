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

class CORE_Movie {
	
	private $infoCompleted;
	private $movie_id = 0;	
	private $ffmpeg = true;
	private $movie_array = array();
	private $flv_conversion = false;
	private $mobile_conversion = false;
	private $thumb_extraction = false;
	
	private $thumb_complete_path;
	private $thumb_short_path;
	private $short_path;	
	private $path_without_filename;
	
	public function CORE_Movie()
	{
		$this->ffmpeg = FFMPEG_LIB;	
		$this->infoCompleted = false;
	}
	
	public function setMovieId($movie_id)
	{
		$this->movie_id = $movie_id;		
	}
	
	public function getAllInformationFromId()
	{
		global $DB;
		$DB->query("SELECT * FROM files, directory WHERE files.id = '".$this->movie_id."' AND directory.id = files.directory_id","getAllInformationFromId");
		$this->movie_array = $DB->fetchrow();	
		
		$this->infoCompleted = true;
		
		$this->setPath();
		
		

		return true;	
	}
	
	
	public function setAllInformation($array_movie)
	{
		$this->movie_array = $array_movie;	
		$this->infoCompleted = true;
		
		
		
		$this->setPath();

		return true;			
	}

	public function performMovieEncoding($flv_conversion = false, $mobile_convert=false, $extract_thumb=false)
	{
		global $DB;
		if ($this->ffmpeg)
		{
			$DB->query("INSERT INTO file_movie VALUES ('".$this->movie_id."', '0','')",'performMovieEncoding');	
			// create directory for Movie if not existing
			loadHelper ('filesys');
			
			if (!mkdir_p($this->path_without_filename,'0777'))
			{
				log_message('debug', 'Error when creating movie directory mkdir_p : '.$this->thumb_complete_path);
				return false;
				exit();
			}	
			$string_complete = "";
			$array = array();
			if ($flv_conversion)
			{
				if ($result_flv = $this->startFlvConversion())
				{
					$string_complete .= " file_thumb_normal = '".$DB->protectString( $this->encodetoUTF8($result_flv))."' ,";	
					$array['flv_path'] = $result_flv;
				}
			}
			if ($mobile_convert)
			{
				if ($result_mobile = $this->startMobileConversion())
				{
					$string_complete .= " mobile_version = '".$DB->protectString( $this->encodetoUTF8($result_mobile))."' ,";	
					$array['mobile_path'] = $result_mobile;
				}
			}
			if ($extract_thumb)
			{
				if ($result_thumb = $this->startThumbGeneration())
				{
					$string_complete .= " file_thumb = '".$DB->protectString( $this->encodetoUTF8($result_thumb))."' ,";	
					$array['thumb_path'] = $result_thumb;
				}
			}
			$string_complete = substr($string_complete,0,strlen($string_complete)-1);
			
			$DB->query("UPDATE files SET $string_complete WHERE id = '".$this->movie_id."' ","");
			
			if (!$result_mobile || !$result_flv)
				$status_error = 2;		// error when converting
			else
				$status_error = 1;
			$DB->query('UPDATE file_movie SET status = '.$status_error.' WHERE file_id = '.$this->movie_id,'performMovieEncoding');		

			
			return $array;
			
		}
		return false;	
		
		
	}


	private function setPath()
	{
		if (array_key_exists('parent',$this->movie_array) && array_key_exists('name',$this->movie_array))
		{
			$this->short_path = $this->movie_array['parent'].$this->movie_array['name']."/".$this->movie_array['filename'] ;
			$this->thumb_short_path = "thumb".$this->short_path;
			$this->thumb_complete_path = ROOTPATH."/".$this->thumb_short_path;
			$this->path_without_filename = ROOTPATH."/thumb".$this->movie_array['parent'].$this->movie_array['name'];
			return true;		
		}
		else
			return false;
	}

	
	private function startThumbGeneration()
	{
		$target_extension = 'jpg';
		$command = FFMPEG." -y -i '".$this->short_path."'  -vcodec mjpeg -vframes 1 -an -f rawvideo -s ".SMALLTHUMB_HEIGHT."x".SMALLTHUMB_WIDTH." '".$this->thumb_complete_path.".".$target_extension."'";
		
		//echo $command;
		$this->sendCommand($command);
		if ($this->checkFile($target_extension))
			return $this->thumb_short_path.".".$target_extension;
		else
			return false;

		
	}
	
	private function startMobileConversion()
	{
		$target_extension = 'mp4';
		$command = FFMPEG." -y -i '".$this->short_path."' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f mp4 '".$this->thumb_complete_path.".".$target_extension."'  &> /tmp/ffmpeg_convert.log";
		
		//echo $command."<br>";
		$this->sendCommand($command);
		if ($this->checkFile($target_extension))
			return $this->thumb_short_path.".".$target_extension;
		else
			return false;					
	}
	

	private function startFlvConversion()
	{
		$target_extension = 'flv';
		$command = FFMPEG." -y -i '".$this->short_path."' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f flv '".$this->thumb_complete_path.".".$target_extension."' &> /tmp/ffmpeg_convert.log";
		
		log_message('debug', "command : ".$command);
		
		//echo $command."<br>";
		$this->sendCommand($command);
		if ($this->checkFile($target_extension))
			return $this->thumb_short_path.".".$target_extension;
		else
			return false;			
	}
	

	

	
	private function checkFile($extension)
	{
		if (filesize($this->thumb_complete_path.'.'.$extension)>0)
			return true;
		else
			return false;		
	}	

	private function sendCommand($command)
	{
		
		$fp=popen($command,"r"); 
		$this->changeNiceStatus('ffmpeg', '+19');
		while (!feof($fp)) {
			//set_time_limit (20);
			$results = fgets($fp, 256);
			if (strlen($results) == 0) {
			   // stop the browser timing out
			   echo " ";
			   flush();
			} else {
			   $tok = strtok($results, "\n");
			   while ($tok !== false) {
					echo htmlentities(sprintf("%s\n",$tok))."<br/>";
					flush();
					$tok = strtok("\n");
			   }
			}
			// avoid a busy wait
			sleep(1);
		} 	
		return true;	
	
	}

	private function changeNiceStatus($process_name, $priority)
	{
		// beter nice priority is +19 (wait until cpu is available)
		// try to change nice status on a specific process
		$pid_list = exec('pidof '.$process_name); 

		$array = explode(' ',$pid_list);
		foreach ($array as $item)
		{
			exec('renice '.$priority.' '.(int)$item);
		}
			
	}
	
	private function encodetoUTF8($string)
	{
		setlocale(LC_CTYPE, 'en_US.utf8');
		return iconv("UTF-8","UTF-8//IGNORE",$string);
	}	
	
}


?>


