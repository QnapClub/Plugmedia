<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Project:     Plottable Thumbnail Library
 * File:        PThumb.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * @link http://chuayw2000.6x.to
 * @copyright 2004-2006 Chua Yong Wen
 * @author Chua Yong Wen <i.stole.your.precious@gmail.com>
 * @package PThumb
 * @version 1.2.10
 */

/**
 * @package PThumb
 */
class CORE_Pthumb{
    var $use_cache = true;
	var $clear_cache = true;
    var $cache_dir = "";
    var $error_mode = 0;
	var $log_error = true;
    var $remote_check = true;
    var $error_array = array();
    var $data_cache = array();
    var $file_ext = array();
    function CORE_Pthumb(){
		$this->cache_dir = BASEPATH."_cache/thumb/";
		$this -> error_array["fatal"] = array();
		$this -> error_array["warning"] = array();
        if (!function_exists("gd_info")){
            $this -> set_error("GD is not enabled on this server! Unable to generate thumbnails.",0);
        }
        
        //These are currently supported by PHP GD
        $this -> file_ext = array(
                                1 => 'gif', 
                                2 => 'jpg', 
                                3 => 'png', 
                                15 => 'wbmp', 
                                16 => 'xbm'
                            );
    }
    /**
	     * Check whether it is possible to cache
	     * @return boolean
	     * @access private
	     * @scope protected
	     */
     function is_cacheable(){
		if ($this -> use_cache && file_exists($this -> cache_dir) && is_writable($this -> cache_dir)){
            return true;
        }
        else{
            return false;
        }
     }
	 
	 /**
	   * Sets an error to the error_array array. Method always returns false.
	   *
	   * @access private
	   * @param string $msg Error Message
	   * @param boolean $is_fatal Set to true to enable a fatal error that will terminate script
	   * @return boolean
	   */
	   
	 function set_error($msg, $is_fatal=true){

		if ($this -> is_cacheable() && $this -> log_error == true){
			$this -> log_error($msg, $is_fatal);
		}
		if ($is_fatal){
			$msg = "[PThumb Fatal Error] ".$msg;
			if ($this -> error_mode == 0){
				//die($msg);
				
			}
			elseif ($this -> error_mode == 1){
				//Decide on the width 
	            $strlen = strlen($msg);
	            $size_x = $strlen * 10;
	            $im = @imagecreate($size_x, 25)
	                 or die("$msg");
	            $background_color = imagecolorallocate($im, 255, 0, 0);
	            $transparent = imagecolortransparent($im,$background_color);
	            $text_color = imagecolorallocate($im, 255,0,0);
	            imagestring($im, 5, 0, 0,  "$msg", $text_color);
	            header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");        
	            header("Cache-Control: no-store, no-cache, must-revalidate");
	            header("Cache-Control: post-check=0, pre-check=0", false);
	            header("Pragma: no-cache"); 
	            header("Content-type: image/png");
	            imagepng($im);
	            imagedestroy($im);
	            die();
			}
			else{
				if ($is_fatal == true){
					$this -> error_array["fatal"][] = $msg;
				}
				else{
					$this -> error_array["warning"][] = $msg;
				}
			}
		}
		else{
			$msg = "[Warning] ".$msg;
			if ($is_fatal == true){
				$this -> error_array["fatal"][] = $msg;
			}
			else{
				$this -> error_array["warning"][] = $msg;
			}
		}
		return false;
	 }
	 
	 /**
	   * Logs an error to error log
	   *
	   * @access private
	   * @param string $msg Error Message
	   * @param boolean $is_fatal Set to true to enable a fatal error 
	   * @return none
	   */
	 function log_error($msg, $is_fatal=true){
		if ($this -> is_cacheable() && $this -> log_error == true){
			$handle = fopen($this -> cache_dir."error_log","a");
			@flock($handle, LOCK_EX);
			$str = "\r\n[";
			if ($is_fatal){
				$str .= "Fatal Error";
			}
			else{
				$str .= "Warning";
			}
			$str .= " @ ".date("r")."] ".$msg;
			@fwrite($handle,$str);
			fclose($handle);
		}
	 }

    /**
	     * Function to get the error message from the array
	     * 
	     * @access public
	     * @param boolean $fatal_error Set to true to return fatal errors or false for warnings
	     * @return array
	     * 
	     */ 
    function error($fatal_error = true){
		/*
		if ($fatal_error == true){
			return $this -> error_array["fatal"];
		}
		else{
			return $this -> error_array["warning"];
		}
		*/
    }
    
    /**
	      * Returns true if there is an error inside error_array
	      * 
	      * @return boolean
	      * @access public
	      */
	    
    function isError(){
        if (!empty($this -> error_array["fatal"]) || !empty($this -> error_array["warning"])){
            return true;
        }
        else{
            return false;
        }
    }
    
    
    /**
	     * Prints out and cache the thumbnail. Returns verbose errors.
	     * 
	     * @access public
	     * @param string $image The Relative Path to the image
	     * @param integer $width The Width of the new thumbnail
	     * @param integer $height The Height of the thumbnail
	     * @param boolean $return_img Set to true to return the string instead of outputting it. Default to false
	     * @param boolean $display_inline If set to true, heaers sent to browser will instruct it to display the image inline instead of asking the user to download. Defaults to true.
	     * @return string
	     * 
	     */
     
     function print_thumbnail($image,$width,$height,$return_img = false, $display_inline = true){
        //Check parameters
        if (empty($image) || empty($width) ||empty($height)){
            return $this -> set_error("Method print_thumbnail: Missing Parameters");
        }
        //Check whether $image is a remote address
        if ($this -> is_remote($image) == 1){
                $is_remote = true;
                //Check that file exists (Check only enabled in PHP 5 because only PHP 5 supports for checking remote files
                if (phpversion() >= 5 && ini_get("allow_url_fopen") == "1"){
                    if (!file_exists($image)){
                        return $this -> set_error("Method print_thumbnail: Error. The file '$image' you specified does not exists or cannot be accessed.");
                    }   
                }
                $image_data = $this -> retrieve_remote_file($image,true);
        }
        elseif ($this -> is_remote($image) == 0){
            $is_remote = false;
            if (!file_exists($image)){
                return $this -> set_error("Method print_thumbnail: Error. The file '$image' you specified does not exists or cannot be accessed.");
            }
            $image_data = @join(file($image));  
        }
        
        if (!is_string($image_data)){
            return $this -> set_error("Method print_thumbnail: Error, could not read image file '$image'.");
        }
        
        $array = $this -> retrieve_image_data($image);
        if (!$array){
            return $this -> set_error("Method print_thumbnail: Unable to determine Image '$image' type and/or dimensions.");
        }
        list($ori_width, $ori_height, $format) = $array;
        
        //Check whether format is supported
        if (!array_key_exists($format, $this -> file_ext)){
            return $this -> set_error("Method print_thumbnail: Image '$image' format is not supported.");
        }
        //Check that cache is enabled, cache DIR is writable, cache DIR exists
        if ($this -> is_cacheable()){
            //Passed eh? Generate the root dir of request file
            if ($is_remote != true){
                $transformed = realpath($image);
                $hash = sha1_file($image);
            }
            else{
                $transformed = $image;
                $hash = sha1($image_data);
            }
            
            //Check if a version exists
            $cache_file = $this -> cache_dir.sha1($transformed).".".$width.".".$height.".".$hash.".".$this -> file_ext[$format];
            //die($cache_file);
            if (file_exists($cache_file)){
                if ($return_img == false){
					//Prepare redirectional URL
					$redir_url = $_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/".$cache_file;
					//Remove instances of double slashes "//"
					$redir_url = str_replace("//","/",$redir_url);
                    header("Location: http://$redir_url");
                    die();
                }
                else{
                    return join(file($cache_file));
                }
            }
            //Check that an older version does not exists and if it does, delete it
            else{
                if ($handle = @opendir($this -> cache_dir)) {
                     while (false !== ($file = readdir($handle))) { 
                          if (preg_match("/^".preg_quote(sha1($transformed))."\.[0-9]+\.[0-9]+\.([0-9a-z]{40})\.(.+?)$/i",$file,$matches)) { 
                              //Hash is in [1]
                              //Check to see if the file data is the same. If it is, then don't delete it.
                              if ($matches[1] != $hash){
                                $matched[] = $file;
                              }
                           }
                     } 
                     closedir($handle);
                     if (!empty($matched)){
                         for ($i = 0; $i <= count($matched) - 1; $i++){
                            @unlink($this -> cache_dir.$matched[$i]);
                         }
                    }
                }   
            }
        }
        $gd_info = gd_info();
        
        //Special GIF handling
        if ($format == 1 && $gd_info["GIF Create Support"] != true){
            //return $this -> set_error("Method print_thumbnail: Error, GIF support is unavaliable for PHP Version ".phpversion());
            //Image Outputted will be in PNG Format
            $format = 3;
        }
        $handle = @imagecreatefromstring($image_data);
        if ($handle == false){
            return $this -> set_error("Method print_thumbnail: Unsupported Image '$image' type");
        }
		
        //Now lets resize it
        //First lets create a new image handler which will be the thumbnailed image
        $thumbnail = imagecreatetruecolor($width,$height);

        if (!$thumbnail){
            return $this -> set_error("Method print_thumbnail: A thumbnail image '$image' could not be created");
        }
		
		/*  Image Format Special Handlinng */
		//GIF truecolour to palette - preserve transparency
		if ($format == 1){
			imagetruecolortopalette($handle, true, 256);
		}
		//PNG Alpha Channel saving
		if ($format == 3){
			//Set to save alpha channel info in source and destination
			imagealphablending($handle, false);
			imagesavealpha($handle, true);
			imagealphablending($thumbnail, false);
			imagesavealpha($thumbnail, true);
		}
		//Resize it
        if (!imagecopyresampled($thumbnail,$handle,0,0,0,0,$width,$height,ImageSX($handle),ImageSY($handle))){
            return $this -> set_error("Method print_thumbnail: Failed resizing image '$image'.");  
        }
        //Cache it
        if ($this -> is_cacheable()){
            switch ($format){
                case 1:
                    $cached = @imagegif($thumbnail,$cache_file);
                    break;
                case 2:
                    $cached = @imageJPEG($thumbnail,$cache_file,100);
                    break;
                case 3:
                    $cached = @imagepng($thumbnail,$cache_file);
                    break;
                case 15:
                    $cached = @imagewbmp($thumbnail,$cache_file);
                    break;
                case 16:
                    $cached = @imagexbm($thumbnail,$cache_file);
                    break;
                default:
                    $cached = false;
            }
            
            if (!$cached){
                return $this -> set_error("Method print_thumbnail: Error in cache generation of image '$image'.");
            }
        }
        if ($return_img == false){
            header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");        
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Pragma: no-cache"); 
            header("Content-Transfer-Encoding: binary");
            header("Content-type: ".image_type_to_mime_type($format));
			if ($display_inline == true){
				header("Content-Disposition: inline; filename=\"".time().".".$this -> file_ext[$format]."\"");
            }
			else{
				header("Content-Disposition: attachment; filename=\"".time().".".$this -> file_ext[$format]."\"");
			}
			if ($this -> is_cacheable()){
                echo join(file($cache_file));
            }
            else{
                switch ($format){
                    case 1:
                        $outputed = @imagegif($thumbnail);
                        break;
                    case 2:
                        $outputed = @imageJPEG($thumbnail,'',100);
                        break;
                    case 3:
                        $outputed = @imagepng($thumbnail);
                        break;
                    case 15:
                        $outpupted = @imagewbmp($thumbnail);
                        break;
                    case 16:
                        $outputed = @imagexbm($thumbnail);
                        break;
                    default:
                        $outputed = false;
                }
                if (!$outputed){
                    return $this->set_error("Method print_thumbnail: Error outputting Image '$image'");
                }
            }
            
        }
        else{
            if ($this -> is_cacheable()){
                return join(file($cache_file));
            }
            else{
                return $this -> set_error("Method print_thumbnail: Cannot return image '$image'! Cache must be enabled!");
            }
        }
        //Destroy the image
        imagedestroy($handle);
        imagedestroy($thumbnail);
		//Clear any cache; if needed
		$this -> clear_cache();
     }
     /**
	      * Function to turn a HEX Based color into an array of RBG
	      * 
	      * http://sg2.php.net/hexdec User Comment by henrique at recidive dot com
	      * 
	      * @access private
	      * @param string $hex The Color code in HEX
	      * @return array
	      */
    function hex2dec($hex) {
      $color = str_replace('#', '', $hex);
      $ret = array(
       'r' => hexdec(substr($color, 0, 2)),
       'g' => hexdec(substr($color, 2, 2)),
       'b' => hexdec(substr($color, 4, 2))
      );
      return $ret;
    }    
     /**
	     * Generate an image with the specified font and options. Image generated is always PNG. 
	     * 
	     * MAKE SURE TO GIVE COLOUR CODES IN HEX FORM!
	     * 
	     * @access private
	     * @param string $text  The Text to output
	     * @param integer $width The width of the image
	     * @param integer $height The Width of the Image
	     * @param integer $size The Font size of the text
	     * @param string $font The Font of the text to use
	     * @param integer $x The X-co-ordinate of the lower left corner of the text
	     * @param integer $y The Y-Co-ordinate of the lower left of the text
	     * @param string $bg The Background colour of the text. Defaults to #ffffff
	     * @param string $textcolor The Colour of the text Defaults to #000000
	     */
     function output_text($text,$width,$height,$size,$font,$x,$y,$bg="ffffff",$textcolor="000000"){
        /*
        if (!$font || !$size || !$height || !$width || !$text || !$x || !$y){
            return $this -> set_error("Method output_text: Missing Parameters");
        }
        */
        $bg = $this -> hex2dec($bg);
        $textcolor = $this -> hex2dec($textcolor);
            if (!file_exists($font)){
                return $this -> set_error("Method print_thumbnail: Font cannot be found");
            }
            else{
                $im = @imagecreate($width, $height) or die("GD is not enabled.");
                $background_color = imagecolorallocate($im, $bg["r"], $bg["g"], $bg["b"]);
                //$transparent = imagecolortransparent($im,$background_color);
                $text_color = imagecolorallocate($im, $textcolor["r"],$textcolor["g"],$textcolor["b"]);
                //imagestring($im, $font, 0, 0,  "$msg", $text_color);
                 imagettftext($im, $size, 0, $x,$y, $text_color, $font,"$text");
                header("Content-type: image/png");
                imagepng($im);
                imagedestroy($im);
                die();
            }
    }
        /**
	         * Validates whether a Given URL is valid or whether it is a remote URL
	         * 
	         * Returns 0 if it is not remote
	         * Returns 1 if it is remote and is accessible
	         * 
	         * @param string $address URL
	         * @access private
	         * @return integer
	         */
        function is_remote($address){
            //Validate that the address is of correct format
            if (!preg_match("/^(http|https|ftp)\:\/\/([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4})(\:[0-9]+)*(\/[^\/][a-zA-Z0-9\.\,\?\'\\/\+&%\$#\=~_\-]*)*[\/]{0,1}$/",$address)){
                return 0;
            }
            /*
            //Test for connectivity
            $connected = $this -> validate_connectivity($address);
            if (!$connected){
                return 2;
            }
            */
            return 1;
        }

    /**
	     * Scale the image to the magnitude specified and then calls print_thumbnail
	     * Returns the dimensions calculated on request
	     * 
	     * @param string $image The Image
	     * @param number $magnitude The magnitude of scaling Value can be a float of integer
	     * @param boolean $return Whether to return the values or to call print_thumbnail. If set to true, will return an array or Width and Height respectively
	     * @access public
	     * @return mixed
	     */
    function scale_thumbnail($image,$magnitude,$return = false){
        $magnitude = abs(floatval($magnitude));

        $array  = @$this -> retrieve_image_data($image);
        if (!$array){
            return $this -> set_error("Method scale_thumbnail: Unable to retrieve Image '$image' Size.");
        }
        list($width,$height) = $array;
        if ($magnitude == 0){
            return $this -> set_error("Method scale_thumbnail error: Magnitude cannot be zero.");
        }
        elseif ($magnitude == 1){
            //Do nothing... 
        }
        else{
            $width = round($width*$magnitude);
            $height = round($height*$magnitude);
        }
        if ($return == false){
            return $this -> print_thumbnail($image,$width,$height);
        }
        else{
            $array = array($width,$height);
            return $array;
        }
    }
    
    /**
	     * Calculate the best Image dimensions so that they fit within the supplied dimensions.
	     * Returns the dimensions calculated on request
	     * 
	     * i.e. If you call this method with $width and $height of 200 and 100 and the $image provided is 500,250
	     * A calculation will be made so that the width and height of the thumbnail does not alter the image width,height ratio
	     * 
	     * 
	     * @param string $image The Image
	     * @param integer $max_width The Width. Set to -1 for no restrictions
	     * @param integer $max_height The height. Set to -1 for no restrictions
	     * @param integer $behaviour Sometimes, 2 values are obtained. Set this to 1 to return the one with the bigger pixel area or 2 to return the one with smaller pixel area.
	     * @param boolean boolean $return Whether to return the values or to call print_thumbnail. If set to true, will return an array or Width and Height respectively
	     * @return mixed
	     * @access public
	     */
     
     function fit_thumbnail($image,$max_width = -1, $max_height = -1,$behaviour = 1 ,$return = false){

        $array = @$this -> retrieve_image_data($image);
        if (!$array){
            return $this -> set_error("Method scale_thumbnail: Unable to retrieve Image '$image' Size.");  
        } 
        list($width,$height) = $array; 
        $max_height = intval($max_height);
        $max_width = intval($max_width);
        //Calculate the width:height ratio
        if ($height == 0 || $width == 0){
            return $this -> set_error("Method fit_thumbnail: Unknown error. Height/width is zero.");
        }
        $ratio = $height/$width;
        if ($max_height < 0 || $max_width < 0){
            if ($max_height < 0 && $max_width < 0){
                //Do nothing
            }
            elseif ($max_height < 0){
                $width = $max_width;
                $height = round($width*$ratio);
            }
            elseif ($max_width < 0){
                $height = $max_height;
                $width = round($height/$ratio);
            }
        }
        elseif ($ratio == 1){
            //Same Height/Width
            if ($max_height === $max_width){
                $width = $max_width;
                $height = $max_height;
            }
            else{
                $height = min($max_height,$max_width);
                $width = min($max_height,$max_width);
            }
        }
        else{
            $case1_width = $max_width;
            $case1_height = round($case1_width*$ratio);
            $case1_area = round($case1_width*$case1_height);
            
            $case2_height = $max_height;
            $case2_width = round($case2_height/$ratio);
            $case2_area = round($case2_width*$case2_height);
            
            //Check if it is an ambiguous case
            if ($case1_width <= $max_width && $case1_height <= $max_height && $case2_width <= $max_width && $case2_height <= $max_height){
                if ($behaviour == 1){
                    if ($case1_area >= $case2_area){
                        $height = $case1_height;
                        $width = $case1_width;
                    }
                    else{
                        $height = $case2_height;
                        $width = $case2_width;                      
                    }
                }
                else{
                    if ($case1_area <= $case2_area){
                        $height = $case1_height;
                        $width = $case1_width;
                    }
                    else{
                        $height = $case2_height;
                        $width = $case2_width;                      
                    }                   
                }
            }
            else{
                if ($case1_width <= $max_width && $case1_height <= $max_height){
                    $height = $case1_height;
                    $width = $case1_width;
                }
                else{
                    $height = $case2_height;
                    $width = $case2_width;                      
                }
            }
        }
        list($owidth,$oheight) = $this -> retrieve_image_data($image);
        if ($height > $oheight || $width > $owidth){
            $width = $owidth;
            $height = $oheight;
        }
        if ($return == false){
            return $this -> print_thumbnail($image,$width,$height);
        }
        else{
            $array = array($width,$height);
            return $array;
        }
     }
     /**
	     * Writes an image file according to data provided. This method is just here to save scripters some trouble.
	     * Users have to ensure directory can be written to! 
	     *  
	     * @param string $image The Image data
	     * @param string $filename The relative path to the image file to be written
	     * @param boolean $overwrite Specifies whether the file, if it exists, should be overwritten or not.
	     * @return boolean
	     */
     function image_to_file($image,$filename,$overwrite=false){
        if (file_exists ($filename) && $overwrite == false){
            return $this -> set_error("Method image_to_file: File exists.");
        } 
        $handle = @fopen($filename,"wb");
        if (!$handle){
            return $this -> set_error("Method image_to_file: Unknown error. File cannot be opened for writing.");
        }
        @flock($handle,LOCK_EX);
        $write = fwrite($handle, $image);
        if (!$write){
            return $this -> set_error("Method image_to_file: Unknown error. File cannot be written to.");
        }
        fclose ($handle);
        return true;
     }
     
     /**
	     * Attempts to retrieve a remote file data and returns it. Returns false on error.
	     * 
	     * Returns string on success, 0 on failure after repeated attempts using different methods, 1 on invalid URL, 2 on inaccessible URL, 3 on file not exist error
	     * 
	     * @param string $url URL
	     * @param boolean $skip_checks Set to skip URL validating
	     * @param boolean $refresh Set to ignore cache
	     * @param integer $force_method Force a certain method of file retrival. Set to null
	     * @return mixed
	     * @access private
	     */
    function retrieve_remote_file($url,$skip_checks=false,$refresh=false, $force_method = 2){
        if ($refresh == false && !empty($this -> data_cache[sha1($url)])){
            return $this -> data_cache[sha1($url)];
        }
        if ($skip_checks == false){
            if ($this -> is_remote($url) == 1){
                    //Check that file exists (Check only enabled in PHP 5 because only PHP 5 supports for checking remote files
                    if (phpversion() >= 5 && ini_get("allow_url_fopen") == "1"){
                        if (!file_exists($url)){
							$this -> set_error("Method retrieve_remote_file: '$url' does not exists.", false);
                            return 3;
                        }   
                    }
            }
            elseif ($this -> is_remote($url) == 0){
                if (!file_exists($url)){
					$this -> set_error("Method retrieve_remote_file: '$url' is invalid.", false);
                    return 1;
                }
            }
        }
        @ini_set("allow_url_fopen","1");
        
        if (ini_get("allow_url_fopen") == "1" && ($force_method == null || $force_method == 1)){
            //METHOD 1: file()
            $data = @join(file($url));
            if ($data != false){
                $this -> data_cache[sha1($url)] = $data;
                return $data;
            }
        }
        //METHOD 2: HTTP_RETRIEVE + CURL (by class)
		if (class_exists("HTTPRetriever") && ($force_method == null || $force_method == 2)){
			$handle = new HTTPRetriever();
			
			//Set Options
			$handle -> headers["Referer"] = $url;
			$handle -> insecure_ssl = true;
			$handle -> ignore_ssl_hostname = true;
			$handle -> follow_redirects = true;
			
			//Retreieve Data
			if (!$handle -> get($url)){
				//Well,,,
			}
			else{
				$data = $handle -> response;
				$this -> data_cache[sha1($url)] = $data;
				return $data;
			}
		}
        //METHOD 3: CURL Take II
        if (function_exists("curl_version") && ($force_method == null || $force_method == 3)){
            $handle = @curl_init(); 
            if ($handle != false){
                curl_setopt ($handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, 1); 
                curl_setopt ($handle, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt ($handle, CURLOPT_URL, $url);
                $data = @curl_exec($handle);
                curl_close($handle);  
                if ($data != false){
                    $this -> data_cache[sha1($url)] = $data;
                    return $data;
                }
            }
        }
        
        //Well, too bad.
		$this -> set_error("Method retrieve_remote_file: '$url' cannot be retrieved.", false);
        return 0;
    }
    
    /**
	     *
	     * Retrieves image data. Workaround for allow_url_fopen if it is disabled
	     * Does not validate file existence
	     * 
	     * @access private
	     * @return array
	     * @param string $file Filename
	     * 
	     */
    function retrieve_image_data($file){
        //DOESN'T CHECK!!!
        if ($this -> is_remote($file) == 0){
			return getimagesize($file);
        }
        //Try via the direct method first.
        @ini_set("allow_url_fopen","1");
        if (ini_get("allow_url_fopen") == "1" && $this -> remote_check == true){
            $array = getimagesize($file);
            if ($array != false && $this -> remote_check == true){
                return $array;
            }
        }
		
        if ($this -> is_cacheable()){
			$filename = 'remote_'.sha1($file)."_".time();
			//Check for file existence
			if (file_exists($this -> cache_dir.$filename) && $this -> remote_check == false){
				$data = join(file($this -> cache_dir.$filename));
			}
			else{
				$data = $this -> retrieve_remote_file($file);
	        }
			if (!is_string($data)){
				$this -> set_error("Method retrieve_image_data: '$file' cannot be read.", false);
	            return false;
	        }
			if (!file_exists($this -> cache_dir.$filename) || $this -> remote_check == true){
	            $handle = @fopen($this -> cache_dir.$filename,"w+");
	            if ($handle){
	                flock($handle,LOCK_EX);
	                fwrite($handle,$data);
                    fclose($handle);
	                }
	            }
			$array = getimagesize($this -> cache_dir.$filename);
			if ($this -> remote_check == true){
				@unlink($this-> cache_dir.$filename);
			}
			if ($array != false){
				return $array;
			}
        }
        
        //Well, TOO BAD
		$this -> set_error("Method rretrieve_image_data: Unable to retrieve image '$file' data.", false);
        return false;
    }
	
    /**
	     *
	     * Clears Cache 
	     * 
	     * @access private
	     * 
	     */
	function clear_cache(){
		if ($this -> clear_cache == true){
			if ($handle = @opendir($this -> cache_dir)) {
			   while (false !== ($file = readdir($handle))) {
				   if ($file != "." && $file != ".." && $file != ".htaccess") {
					   @unlink($this -> cache_dir.$file) or $this -> set_error("Method clear_cache: Unable to delete '$file' from cache.",false);
				   }
			   }
			   closedir($handle);
			}
			else{
				$this -> set_error("Method clear_cache: Unable to open cache directory.",false);
			}
		}
	}
}
?>