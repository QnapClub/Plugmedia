

{if $current_media.mp3_info.title neq ""}
<div id="mp3tag">
<div id="artist">{$current_media.mp3_info.artist}</div>
<img src="{$adresse_images}/separator.gif" />
<div id="title">{$current_media.mp3_info.title}</div>
<img src="{$adresse_images}/separator.gif" />
<div id="album">{$current_media.mp3_info.album}</div>
</div>

{else}

<div id="mp3tag">
<div id="title">{$current_media.short_name}</div>
<img src="{$adresse_images}/separator.gif" />
</div>

{/if}
<br />

<div id="read_next_div"><input type="checkbox" id="read_next" {if $smarty.cookies.pm_repeatnext eq 1}checked="checked"{/if} /><label for="read_next">Read Next</label></div>


<div id='mediaspace'>You Need flash player</div>


<script type="text/javascript">
  jwplayer('mediaspace').setup({
    'flashplayer': 'system/views/common/player/player.swf',
	
    'id': 'playerID',
    'width': '470',
    'height': {if $current_media.normal_thumbnail}'320'{else}'24'{/if} ,
    'file': encodeURI('api.php?ac=getFileContent&file={$current_media.file_id}&dwl=0'),
	'allowscriptaccess' : 'always',
	'wmode':'opaque',
	'stretching':'uniform',
	'provider':'sound',
	'autostart':'true',
	'screencolor ':'ffffff',
	'wmode': 'transparent',
	'controlbar':'bottom',
{if $current_media.normal_thumbnail}
  	'image':'{$current_media.normal_thumbnail}',
{/if}events: {
		onComplete: function(evt) {
			
			if ($.cookie('pm_repeatnext'))
			{
				PM.loadingPage($("#right_link").attr('href'));
				delete jwplayer('mediaspace');
				delete jwplayer;				
				return false;
			}
			return false;
		}
	}	
  });
  
 $(function() {

$( "#read_next" ).button( { icons: { primary: "icon_repeat_next" }, text: false } )
				 .click(function(){
					if (!$(this).is(':checked'))
					{
						$.cookie('pm_repeatnext', '0', { expires: 365});
					}
					else
						$.cookie('pm_repeatnext', '1', { expires: 365});
					}); 
 })
  
</script>
