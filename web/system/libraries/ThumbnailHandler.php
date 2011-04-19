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

class CORE_ThumbnailHandler {

	private $filename;
	private $directory_name;
	
	private $rotation;	
	private $extension;


	private $small_height;		
	private $small_width;
	private $normal_height;		
	private $normal_width;	
	private $thumb_location;

	private $full_original_path="";
	private $full_thumb_path="";
		
	private $prefix;
	private $silent_mode;	
	
	private $custom_path;
	
	public function CORE_ThumbnailHandler()
	{
		// get information for XX_height and XX_width
		$this->small_height		=SMALLTHUMB_HEIGHT;
		$this->small_width		=SMALLTHUMB_WIDTH;	
		$this->normal_height	=NORMALTHUMB_HEIGHT;
		$this->normal_width		=NORMALTHUMB_WIDTH;
		// get information for thumb location
		$this->thumb_location	= ROOTPATH."/thumb";			
		
		$this->silent_mode = true;
		
	}
	
	public function setSilentMode($silent=true)
	{
		$this->silent_mode = (bool)$silent;
	}

	
	public function setThumbInfo($filename, $directory_name,$rotation, $extension, $custom_thumb_path = '')
	{
		$this->filename = $filename;
		$this->custom_path = $custom_thumb_path;
		$this->directory_name = $directory_name;
		if ($rotation == 1|| $rotation == 3 || $rotation == 6 || $rotation == 8)
			$this->rotation = $rotation;
		else	
			$this->rotation = 0;	
		$this->extension = $extension;
		
		$this->full_original_path = $this->directory_name."/".$this->filename;
		if ($this->custom_path=='')
			$this->full_thumb_path = $this->thumb_location.$this->directory_name;
		else
			$this->full_thumb_path = $this->thumb_location.$custom_thumb_path;
		
	}
	
	
	public function generateThumb($type='small')
	{
		if ($type == 'small')
		{
			$width = $this->small_width;
			$height = $this->small_height;
			$this->prefix = "sm_";
		}
		else
		{
			$width = $this->normal_width;
			$height = $this->normal_height;
			$this->prefix = "lg_";
		}
		
		loadHelper ('filesys');

		if (!mkdir_p($this->full_thumb_path,'0777'))
		{
			log_message('debug', 'Error when creating thumb directory mkdir_p : '.$this->full_thumb_path);
			return false;
			exit();
		}

		
		
		if (($this->extension == 'jpg' || $this->extension == 'jpeg') && IMR_ALL)
		{		
			
			$result = $this->resizePictureWithImrAll($width, $height);
		}
		else
		{
			$result = $this->resizePictureWithPthumb($width, $height);
		}
		
		// NOW rotate the picture
			
		
		
		if ($result)
		{
			log_message('debug', 'Detect Rotation Mode for :'.$this->full_original_path);
			switch ($this->rotation)
			{
				case 3:
					$this->rotateThumb(180);
				break;
				case 6:
					$this->rotateThumb(270);
				break;
				case 8:
					$this->rotateThumb(90);
				break;
			}
				
			if ($this->custom_path=='')
				return 'thumb'.$this->directory_name."/".$this->prefix.$this->filename;
			else
				return 'thumb'.$this->custom_path."/".$this->prefix.$this->filename;
		
		}
		else
		{
			log_message('debug', 'Error when creating thumb!! : thumb'.$this->directory_name."/".$this->prefix.$this->filename);
			return false;
		}
	}
	
	
	private function rotateThumb($degrees)
	{
	
		loadHelper ('picture');
		$properties = propertyByExtension($this->extension);
		$im = $properties['imagecreatefrom']($this->full_thumb_path."/".$this->prefix.$this->filename);
		
		if(!$im)
		{	
			return false;
		}
		else
		{
			$thumb_rotate = imagerotate($im, $degrees, 0) ;
			$properties['imagedisplay']($thumb_rotate, $this->full_thumb_path."/".$this->prefix.$this->filename);
			return true;
		}
	
	}


	
	private function resizePictureWithImrAll($width, $height)
	{
		log_message('debug', 'Generate thumb with IMRAll for :'.$this->full_original_path);
		loadHelper ('filesys');
		clearstatcache();
		if (!is_file($this->full_original_path) || !is_readable($this->full_original_path))
		{
			if (!is_file($this->full_original_path))
				log_message('debug', 'Original file not a file :'.$this->full_original_path);
			else
				log_message('debug', 'Original file not readable :'.$this->full_original_path);
			return false;
			exit();
		}
		
		
		
		
		//$resize_string = '/usr/local/sbin/ImR_all -jpg '.$height.' "'.$this->full_original_path.'" "'.$this->full_thumb_path."/".$this->prefix.$this->filename.'"';
		// skip ImR_all to avoid error log when generating
		list($picture_width, $picture_height, $picture_type, $picture_attr) = @getimagesize($this->full_original_path);
		log_message('debug', 'Original size of thumb :'.$picture_width.' x '.$picture_height);
		// find the correct resize...
		$ratio_width = ceil($picture_width / $width);
		$ratio_height = ceil($picture_height / $height);
		
		$ratio = max($ratio_height, $ratio_width);
		$supp = '';
		$resize_sec = false;
		while ($ratio > 8)
		{
			$ratio = ceil ($ratio / 8);	
			if ($ratio <= 8)
			{		
				$resize_sec = true;
				$supp .= '| /usr/local/sbin/cjpeg -quality 95  | /usr/local/sbin/djpeg -scale 1/'.$ratio.' -bmp';
			}
			else
				$supp .= '| /usr/local/sbin/cjpeg -quality 95  | /usr/local/sbin/djpeg -scale 1/8 -bmp';	
		}
		if ($resize_sec)
			$ratio = 8;
				
		
		
		$resize_string = '/usr/local/sbin/djpeg -scale 1/'.$ratio.' -bmp "'.$this->full_original_path.'"  '.$supp.' | /usr/local/sbin/cjpeg -quality 95 -outfile "'.$this->full_thumb_path."/".$this->prefix.$this->filename.'"';
		
		if ($this->silent_mode)
			$silent = " > /dev/null &"; 
		else
			$silent = " > /dev/null ";
		
		exec($resize_string.$silent);
		
		log_message('debug', 'EXEC: '.$resize_string.$silent);

		
		if (is_file($this->full_thumb_path."/".$this->prefix.$this->filename) || $this->silent_mode)	// we are suppose that all is fine with silent mode
			return true;
		else
		{
			log_message('debug', 'No file detected');
			return false;
		}
	}
	
	
	private function resizePictureWithPthumb($width, $height)
	{
		log_message('debug', 'Generate thumb with Pthumb for :'.$this->full_original_path);
		
		/*$thumbgen =& load_class('ImageManipulation');
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $this->full_original_path;
		$config['new_image'] = $this->full_thumb_path."/".$this->prefix.$this->filename;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;
		$config['thumb_marker'] = "";
		
		$thumbgen->setConfig($config);
		
		if (is_file($this->full_thumb_path."/".$this->prefix.$this->filename))
			@unlink($this->full_thumb_path."/".$this->prefix.$this->filename);
		
		if ($thumbgen->resize())
			return true;
		else
		{
			log_message('debug', 'Error when resizing with Pthumb for :'.$this->full_original_path);
			return false;		
		}
		*/
		
$pthumb =& load_class('Pthumb');
		
		$pthumb =& load_class('Pthumb');
		
		$data = $pthumb -> fit_thumbnail($this->full_original_path,$width,$height,1,true);
		if (!$data)
		{
			return false;
			exit();
		}
		$data = $pthumb -> print_thumbnail($this->full_original_path,$data[0],$data[1],true);
		$pthumb->clear_cache();
		if (!$data)
		{
			return false;
			exit();
		}
		if (!$pthumb -> image_to_file($data,$this->full_thumb_path."/".$this->prefix.$this->filename, true))
		{
			return false;
			exit();
		}
		return true;
		/*
		$pthumb =& load_class('Pthumb');
		
		$data = $pthumb -> fit_thumbnail($this->full_original_path,$width,$height,1,true);
		if (!$data)
		{
			return false;
			exit();
		}
		$data = $pthumb -> print_thumbnail($this->full_original_path,$data[0],$data[1],true);
		$pthumb->clear_cache();
		if (!$data)
		{
			return false;
			exit();
		}
		if (!$pthumb -> image_to_file($data,$this->full_thumb_path."/".$this->prefix.$this->filename, true))
		{
			return false;
			exit();
		}
		*/		
		
	}
	
	

}


?>