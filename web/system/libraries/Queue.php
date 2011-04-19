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

class CORE_Queue {

	var $filename = array();
	var $already_running = array();
	var $processed = 0;
		
	function CORE_Queue()
	{
		
	}
	
	function getItem($name_queue, $number_item)
	{
		global $DB;
		$DB->query("SELECT * FROM queue WHERE queue_name = '$name_queue' ORDER BY created_time OFFSET 0 LIMIT ".$number_item,"getItem");
		$array = $DB->fetcharray();
		if (count($array)>=1)
			return $array;
		else
			return false;
	}
	
	
	
	function putItem($queue_name, $action, $ref_id)
	{
		global $DB;
		if ($DB->query("INSERT INTO queue (ref_id, action, queue_name, created_time) VALUES ('$ref_id', '$action', '$queue_name',NOW())","putItem"))
			return true;
		else
			return false;
	}
	
	function removeItem($queue_name, $created_time)
	{
		global $DB;
		if ($DB->query("DELETE FROM queue where created_time='$created_time' AND queue_name= '$queue_name' ","removeItem"))
			return true;
		else
			return false;
	}	
	
	function clearQueue($queue_name)
	{
		global $DB;
		$DB->query("DELETE FROM queue WHERE queue_name = '$queue_name'","clearQueue");	
		$this->releaseLockQueue($queue_name);
		return true;
	}
		
	public function performQueue($name_queue, $number_per_job)
	{
		$this->filename[$name_queue] = ROOTPATH."/system/_cache/".$name_queue.'.pid'; 
		
		// perform queue name until END
		if($this->queueIsRunning($name_queue)) {
			echo "Already running.\n";
			exit;
		}
		else {
			$this->lockQueue($name_queue);
			$this->queueAction($name_queue, $number_per_job);
			// again to see if there is no new items before exit;
			$this->queueAction($name_queue, $number_per_job);
			$this->releaseLockQueue($name_queue);
		}

		
		
	}
	
	private function queueAction($name_queue, $number_per_job)
	{


		while ($result = $this->getItem($name_queue, $number_per_job))
		{
			$size_arr = count($result);
			for ($i=0;$i<$size_arr;$i++)
			{
				
				$result[$i]['action'] = unserialize($result[$i]['action']);
				
				switch ($result[$i]['queue_name'])
				{
					case 'movie_convert': $this->performMovieEncoding($result[$i]['action']['id_movie'], $result[$i]['action']['flv_convert'], $result[$i]['action']['mobile_convert'],$result[$i]['action']['extract_thumb']);	 break;
				}
					
				$this->removeItem($name_queue, $result[$i]['created_time']);
				$this->processed ++;
			}
		}


		
	}
	
	public function queueIsRunning($name_queue)
	{
		$this->filename[$name_queue] = ROOTPATH."/system/_cache/".$name_queue.'.pid'; 
 
          
            if(file_exists($this->filename[$name_queue])) {
                $pid = (int)trim(file_get_contents($this->filename[$name_queue]));
                if(posix_kill($pid, 0)) {
                    $this->already_running[$name_queue] = true;
					return true;
					exit();
                }
				else
					return false;
            }
			else
				return false;
           
      
		
		
		
	}
	
	private function releaseLockQueue($name_queue)
	{
		
		@unlink ($this->filename[$name_queue]);
		return true;
	}
	private function lockQueue($name_queue)
	{


        if(!$this->queueIsRunning($name_queue)) {
            $pid = getmypid();
            file_put_contents($this->filename[$name_queue], $pid);
			return true;
        }
		else
		{
			// Already locked	
			return true;
		}



		
	}

	public function getProcessed()
	{
		return $this->processed;	
	}
	

	
	
	
	public function performMovieEncoding($id_movie, $flv_convert, $mobile_convert, $extract_thumb)
	{
		
		/*if (!FFMPEG_LIB)
		{
			exit();	
		}
		
		
		// grab all informations on the movie
		global $DB;
		$DB->query("SELECT * FROM files, directory WHERE files.id = '$id_movie' AND directory.id = files.directory_id","performMovieEncoding");
		$movie = $DB->fetchrow();
		
		
		$short_thumb_path = "thumb".$movie['parent'].$movie['name'];
		$full_thumb_path = ROOTPATH."/".$short_thumb_path;

		loadHelper ('filesys');
		if (!mkdir_p($full_thumb_path,'0777'))
		{
			log_message('debug', 'Error when creating movie directory mkdir_p : '.$full_thumb_path);
			return false;
			exit();
		}		
		

		
		if ($extract_thumb)
		{
			// extract thumb in movie	
			//$command = "/usr/bin/ffmpeg -i '/share/HDA_DATA/Qmultimedia/video/Prison break.avi' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f flv '/share/Qweb/plugmedia/thumb/Prison break.flv'";
			$extract_filename = $movie['filename'].".jpg";
			$complete_path = $full_thumb_path."/".$extract_filename;
			$short_path = $short_thumb_path."/".$extract_filename;
			
			$command = FFMPEG." -y -i '".$movie['parent'].$movie['name']."/".$movie['filename']."'  -vcodec mjpeg -vframes 1 -an -f rawvideo -s ".SMALLTHUMB_HEIGHT."x".SMALLTHUMB_WIDTH." '".$complete_path."'";
			$this->sendCommand($command);
			$thumb_version = " , file_thumb = '".$DB->protectString( $this->encodetoUTF8($short_path))."'";			
		}
		
		if ($mobile_convert)
		{
			// extract thumb in movie	
			//$command = "/usr/bin/ffmpeg -i '/share/HDA_DATA/Qmultimedia/video/Prison break.avi' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f flv '/share/Qweb/plugmedia/thumb/Prison break.flv'";
			$extract_filename = $movie['filename'].".mp4";
			$complete_path = $full_thumb_path."/".$extract_filename;
			$short_path = $short_thumb_path."/".$extract_filename;
			
			$command = FFMPEG." -y -i '".$movie['parent'].$movie['name']."/".$movie['filename']."' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f mp4 '".$complete_path."'";
			$this->sendCommand($command);
			$mobile_version = " , mobile_version= '".$DB->protectString( $this->encodetoUTF8($short_path))."'";			
						
		}
		
		$extract_filename = $movie['filename'].".flv";
		$complete_path = $full_thumb_path."/".$extract_filename;
		$short_path = $short_thumb_path."/".$extract_filename;
		
		$command = FFMPEG." -y -i '".$movie['parent'].$movie['name']."/".$movie['filename']."' -b 300k -ar 22050 -ab 56k -qmin 6 -qmax 12 -f flv '".$complete_path."'";
		echo $command;
		$this->sendCommand($command);
		
		$DB->query("UPDATE files SET file_thumb_normal = '".$DB->protectString( $this->encodetoUTF8($short_path))."' $thumb_version  $mobile_version WHERE id = '$id_movie' ","performMovieEncoding");
		*/
			
		
		$movie =& load_class('Movie');
		$movie->setMovieId($id_movie);
		$movie->getAllInformationFromId();
		
		$movie->performMovieEncoding($flv_convert, $mobile_convert, $extract_thumb);
		
		return true;
		
	
		
		
		
		
		
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
	
	private function encodetoUTF8($string)
	{
		setlocale(LC_CTYPE, 'en_US.utf8');
		return iconv("UTF-8","UTF-8//IGNORE",$string);
	}
	
	
}