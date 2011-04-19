<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/***********************************************************
* Class:       ID3
* Version:     1.0
* Date:        Janeiro 2004
* Author:      Tadeu F. Oliveira
* Contact:     tadeu_fo@yahoo.com.br
* Use:         Extract ID3 Tag information from mp3 files
***********************************************************
Exemple

    $nome_arq  = 'Blind Guardian - Bright Eyes.mp3';
     $myId3 = new ID3($nome_arq);
     if ($myId3->getInfo()){
         echo('<HTML>');
         echo('<a href= "'.$nome_arq.'">Clique para baixar: </a><br>');
         echo('<table border=1>
               <tr>
                  <td><strong>Artista</strong></td>
                  <td><strong>Titulo</strong></font></div></td>
                  <td><strong>Trilha</strong></font></div></td>
                  <td><strong>Album/Ano</strong></font></div></td>
                  <td><strong>G&ecirc;nero</strong></font></div></td>
                  <td><strong>Coment&aacute;rios</strong></font></div></td>
               </tr>
               <tr>
                  <td>'. $myId3->getArtist() . '&nbsp</td>
                  <td>'. $myId3->getTitle()  . '&nbsp</td>
                  <td>'. $myId3->getTrack()  . '&nbsp</td>
                  <td>'. $myId3->getAlbum()  . '/'.$myId3->getYear().'&nbsp</td>
                  <td>'. $myId3->getGender() . '&nbsp</td>
                  <td>'. $myId3->tags['COMM']. '&nbsp</td>
               </tr>
            </table>');
         echo('</HTML>');
       }else{
        echo($errors[$myId3->last_error_num]);
   }

*/


class CORE_ID3{

   var $file_name=''; //full path to the file
                         //the sugestion is that this path should be a
                      //relative path
   var $getID3;

   function CORE_ID3(){
      require_once BASEPATH.'libraries/getid3/getid3.php';	
	  $this->getID3 = new getID3();
   }
   function addFilname($file_name){
      
	  $this->file_info = $file_name;
      
   }
   
   /**Read the file and put the TAGS
   content on $this->tags array**/
	function getInfo()
	{
		if ($this->file_info != '')
		{
			$fileInfo = $this->getID3->analyze($this->file_info);  
			
			if($fileInfo['tags_html']['id3v2']['title'][0] && $fileInfo['tags_html']['id3v2']['track'][0]  && $fileInfo['tags_html']['id3v2']['album'][0]) 
			{
				$array['track'] = $fileInfo['tags_html']['id3v2']['track_number'][0];
				$array['title'] = $fileInfo['tags_html']['id3v2']['title'][0];
				$array['album'] = $fileInfo['tags_html']['id3v2']['album'][0];
				$array['gender'] = $fileInfo['tags_html']['id3v2']['genre'][0];
				$array['year'] = $fileInfo['tags_html']['id3v2']['year'][0];
				$array['artist'] = $fileInfo['tags_html']['id3v2']['artist'][0];
				$array['all'] = $fileInfo;
			}
			elseif($fileInfo['tags_html']['id3v1']['title'][0] && $fileInfo['tags_html']['id3v1']['track'][0]  && $fileInfo['tags_html']['id3v1']['album'][0])
			{
				$array['track'] = $fileInfo['tags_html']['id3v1']['track'][0];
				$array['title'] = $fileInfo['tags_html']['id3v1']['title'][0];
				$array['album'] = $fileInfo['tags_html']['id3v1']['album'][0];
				$array['gender'] = $fileInfo['tags_html']['id3v1']['genre'][0];
				$array['year'] = $fileInfo['tags_html']['id3v1']['year'][0];
				$array['artist'] = $fileInfo['tags_html']['id3v1']['artist'][0];
				$array['all'] = $fileInfo;			
			}
			else
			{
				return false;
				die();			
			}
			
			return $array;
		}
		else{
			return false;
			die();
		}
	  
      	
   }
}
?>