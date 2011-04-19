


<div id="picture_rendering_l">
<a id="shadow_pic" href="api.php?ac=rotatePic&pic={$current_media.file_id}&percent=1.5" rel="shadowbox;player=img"><img src="api.php?ac=rotatePic&pic={$current_media.file_id}&percent=1" width="{$current_media.size[0]}" height="{$current_media.size[1]}" align="middle" /></a>
</div>


<br />

{if $current_media.readable_type|lower eq 'jpg'}

{* EXIF HIDDEN WINDOW *}

<script language="javascript">
$(function() {
		$.fx.speeds._default = 100;

		$( "#exif_win" ).dialog({
			autoOpen: false,
			show: "drop",
			height: 420,
			width:600,
			modal: true			
		});

		$( "#show-btn_exif" ).click(function() { 
			$("#exif_win").dialog( "open" );  
			 initialize();
			return false; 
		});




		/*$("#exif_map").googleMaps({
        markers: {
            latitude: 	{$current_media.exif_geocode.latitude},
            longitude:  {$current_media.exif_geocode.longitude}
        },
        controls: {
            type: {
                location: 'G_ANCHOR_TOP_LEFT',
            },
            zoom: {
                location: 'G_ANCHOR_TOP_RIGHT',
            }
        }
		
		
		
    }); */
	
function initialize() {
	{if $current_media.exif_geocode.latitude}
		var LatLng = new google.maps.LatLng( {$current_media.exif_geocode.latitude}, {$current_media.exif_geocode.longitude}  );
		var settings = {
		zoom: 16,
		center: LatLng,
		mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
		navigationControl: true,
		navigationControlOptions: { style: google.maps.NavigationControlStyle.DEFAULT  },
		mapTypeId: google.maps.MapTypeId.HYBRID  };
		
		var map = new google.maps.Map(document.getElementById("exif_map"), settings);
		 
		var marker = new google.maps.Marker({
			position: LatLng, 
			map: map
		});   
	{/if}
	
}
	

		
})

</script>



<div id="exif_win" title="{t}EXIFINFO{/t}">

        <div id="ext_info">
        <h4>EXIF</h4>
        <table width="100%" border="0">
  <tr>
    <td>{t}TAILLE{/t}</td>
    <td>{if $current_media.exif.pixelxdimension}{$current_media.exif.pixelxdimension} x {$current_media.exif.pixelydimension}{/if}</td>
  </tr>
  <tr>
    <td>{t}CAMERA{/t}</td>
    <td>{$current_media.exif.make}</td>
  </tr>
  <tr>
    <td>{t}CAMERAMODEL{/t}</td>
    <td>{$current_media.exif.model}</td>
  </tr>
  <tr>
    <td>{t}PHOTODATE{/t}</td>
    <td>{$current_media.exif.datetimeoriginal|date_format:"%d-%m-%Y %H:%M:%S":null:true}</td>
  </tr>
  <tr>
    <td>{t}FILEDATE{/t}</td>
    <td>{$current_media.exif.datetime|date_format:"%d-%m-%Y %H:%M:%S":null:true}</td>
  </tr>
  <tr>
    <td>{t}FLASH{/t}</td>
    <td>{$current_media.exif.flash}</td>
  </tr>
  <tr>
    <td>{t}FOCAL{/t}</td>
    <td>{$current_media.exif.focallength}</td>
  </tr>
  <tr>
    <td>{t}EXPOSURE{/t}</td>
    <td>{$current_media.exif.exposuretime}</td>
  </tr>
  <tr>
    <td>{t}APERTURE{/t}</td>
    <td>{$current_media.exif.aperturevalue}</td>
  </tr>
  <tr>
    <td>{t}ISO{/t}</td>
    <td>{$current_media.exif.isospeedratings}</td>
  </tr>
  <tr>
    <td>{t}WHITEBALANCE{/t}</td>
    <td>{$current_media.exif.whitebalance}</td>
  </tr>
  <tr>
    <td>{t}METERINGBALANCE{/t}</td>
    <td>{$current_media.exif.meteringmode}</td>
  </tr>
  <tr>
    <td>{t}EXPOSURE_TYPE{/t}</td>
    <td>{$current_media.exif.exposuremode}</td>
  </tr>
</table>
        </div>
        {if $current_media.exif_geocode.latitude}<div id="exif_map" style="width:500px;height:500px;margin:auto;margin-top:10px;"></div>{/if}
</div>


{/if}

{literal}
<script language="javascript">
Shadowbox.init();
</script>
{/literal}