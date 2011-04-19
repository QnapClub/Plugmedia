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

class CORE_StreamRadio {
	private $stream_name; 
	private $stream_genre;
	private $stream_url;
	private $stream_public;
	private $stream_bitrate;
	private $stream_path;
	private $user_id;
	private $shoutcast_framesize;
	private $playlist;
	private $config;
	private $radio_id = 0;	
	
	function CORE_StreamRadio()
	{
		$this->stream_name		= "Plugmedia Radio - hosted on QNAP";
		$this->stream_genre		= "various";
		$this->stream_url		= "";
		$this->stream_public	= "0";
		$this->stream_bitrate	= "128"; 
		$this->shoutcast_framesize = 8192;
		$this->radio_id = 0;
		
		$configDB =& load_class('ConfigLoader');
		
		$this->config = $configDB ;
		return true;
	}
	
	function generatePlaylist($token, $user_id)
	{
		
		global $DB;
		$DB->query("SELECT * FROM radio_token WHERE token = '$token' AND id_creator = '$user_id'","generatePlaylist");
		$return = $DB->fetchrow();
		$path = $return['id_directory'];
		loadHelper ('streamer');
		$this->playlist = GetAllSongInDirectory( $path,true);
		$this->stream_path = $path; 
		$this->user_id = $user_id;
		return true;
	}
	
	// Function that alternates mp3 / data while keeping good sync
	// (example 8192 bytes of mp3 - X bytes of meta data)
	function streamfile($file, $randombegin=false, $offset=0)
	{
		GLOBAL $taille, $DB;
		$getID3 = new getID3();
		
		$fp = fopen($file['link'],"rb");
		if(!$fp)
		die("Access denied for ".$file['link']);
		
		$taillefichier = filesize($file['link']);
		
		if($randombegin)
		{
			
			$random = rand()%$taillefichier;
			$taillefichier -= $random;
			fseek($fp,$random);
		}
		$taillefichier+=$offset; 
		
		$fileInfo = $getID3->analyze($file['link']);
		
		if($fileInfo['id3v2']['comments']['title']) {
			$title = "StreamTitle='".$fileInfo['id3v2']['comments']['title'][0]." - ".$fileInfo['id3v2']['comments']['artist'][0]."';StreamUrl='".$stream['url']."';\n\0";
			$shorttitle = addslashes($fileInfo['id3v2']['comments']['title'][0]." - ".$fileInfo['id3v2']['comments']['artist'][0]);
		}
		elseif($fileInfo['id3v1']['title'] && strlen($fileInfo['id3v1']['title'][0])>1 && strlen($fileInfo['id3v1']['artist'][0])>1)	{
			$title = "StreamTitle='".$fileInfo['id3v1']['title'][0]." - ".$fileInfo['id3v1']['artist'][0]."';StreamUrl='".$stream['url']."';\n\0";
			$shorttitle = addslashes($fileInfo['id3v1']['title'][0]." - ".$fileInfo['id3v1']['artist'][0]);
		}
		else {
			$title = "StreamTitle='".addslashes($file['filename'])."';StreamUrl='".$stream['url']."';\n\0";
			$shorttitle = addslashes($file['filename']);
		}
		
		$tailletitre = strlen($title);
		$headersize = ceil( (float)$tailletitre / 16.0 );
		$headerbyte = chr($headersize);
		
		if ($this->radio_id == 0)		
		{
			$DB->query("INSERT INTO radio_listener (id_listener,song,last_access_date) VALUES ('".$this->user_id."','$shorttitle',CURRENT_TIMESTAMP)","streamfile");
			$this->radio_id = $DB->getLastId();
		}
		else
			$DB->query("UPDATE radio_listener SET song = '$shorttitle' WHERE id_radio='".$this->radio_id."' ","streamfile");	
		
		while(!feof($fp))
		{
			$meuh = fread($fp,$this->shoutcast_framesize-$offset);
			echo $meuh;
			if(feof($fp))
			{
				$offset = $taillefichier;
			}
			else
			{
				$taillefichier -= $this->shoutcast_framesize;
				$offset = 0;
				echo $headerbyte;
				echo $title;
				for($i=$tailletitre;$i<($headersize*16);$i++)echo chr(65);
			}
			
		}
		fclose($fp);
		$offset %= $this->shoutcast_framesize;
		if($offset < 0)$offset += $this->shoutcast_framesize;
			
		
		return $offset;
	}
	
	
	function start()
	{
		@ini_set("max_execution_time", "0");
		require_once BASEPATH.'libraries/getid3/getid3.php';	
		header("icy-notice1:This is a WINAMP ShoutCast Stream");
		header("icy-notice2:You will only see binary data, please press STOP button");
		header("icy-name:".$this->stream_name);
		header("icy-genre:".$this->stream_genre);
		header("icy-url:".$this->stream_url);
		header("Content-Type: audio/x-mp3");
		if ($this->stream_public == "1") {
			header("icy-pub:1");
		}
		else {
			header("icy-pub:0");
		}
		header("icy-br:".$this->stream_bitrate);
		header("icy-metaint:".$this->shoutcast_framesize);
		srand((float) microtime()*1000000);
		$offset = 0;
		$firstfile = true;	// simulate radio
		while(true)
		{
			reset ($this->playlist);
			shuffle($this->playlist);
			foreach($this->playlist as $item)
			{
				if (is_file($item['link']))
					$offset = $this->streamFile($item, $firstfile, $offset);
				$firstfile = false;
			}
		}
	}
	


}
?>