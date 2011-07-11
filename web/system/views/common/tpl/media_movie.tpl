{if $current_media.normal_thumbnail || $current_media.readable_type eq 'flv'}

<div id="read_next_div"><input type="checkbox" id="read_next" {if $smarty.cookies.pm_repeatnext eq 1}checked="checked"{/if} /><label for="read_next">Read Next</label></div>

<div id='mediaspace'>You Need flash player</div>


<script type="text/javascript">
  jwplayer('mediaspace').setup({
    'flashplayer': 'system/views/common/player/player.swf',
	'skin':'system/views/common/player/glow.zip',
    'id': 'playerID',
    'width': '470',
    'height': '400',
    'file': encodeURI('api.php?file={$current_media.file_id}&dwl=0&ac=getFileContent'),
	'allowfullscreen': 'true',
	'allowscriptaccess' : 'always',
	'wmode':'opaque',
	'stretching':'fill',
	'provider':'http',
	'autostart':'true'/*,
	/*events: {
		onComplete: function(evt) {
			
			if ($.cookie('pm_repeatnext'))
				PM.loadingPage($("#right_link").attr('href'));
			
		}
	}*/	
  });
  
	jwplayer().onComplete(function(evt) { 
  		if ($.cookie('pm_repeatnext'))
			PM.loadingPage($("#right_link").attr('href')); 
	});

</script>



{else}
    {if $user_info.can_convert_movie eq 1 && $ffmpeg_lib_support}
        <div id="information_message">This movie is not ready for browser playback, you can convert it. <br />This operation can takes up to XX mins<br /><br /><button class="convert" id="convert_btn">Please Wait ...</button><br /></div><br /><br /><br />
        
<div id="dialog-confirm" title="Convert Movie?" style="display:none">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Converting movie can slow the system. <br />Are you sure?</p>
</div>

<div id="dialog_ok_convert" title="Movie convertion" style="display:none">
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
		Your movie have added to the queue.
	</p>
	<p>
		You will get a message when your movie is ready to play.</b>.
	</p>
</div>

        
        
    {else}
        <div id="information_message">This movie is not ready for browser playback, ask the administrator to convert it</div>
    {/if}
{/if}<br />

<div id="movie_reference" style="display:none">{$current_media.file_id}</div>



<script type="text/javascript">

jQuery(document).ready(function($) {

PM.readNextSong();


	$( "#dialog_ok_convert" ).dialog({
			modal: true,
			autoOpen: false,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
	});

	$( "#dialog-confirm" ).dialog({
			resizable: true,
			autoOpen: false,
			width:400,
			height:200,
			modal:true,
			buttons: {
				"Convert": function () { convertFile() },
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
		


$("#convert_btn").button().click(function() {
	$("#dialog-confirm").dialog('open');

}).button( "disable" );


$.ajax({
		  url: 'api.php',
		  data: "ac=get_message_queue&id="+$('#movie_reference').text(),
		  type: "GET",
		  dataType: "json",
		
		  success: function(data) {
				if (data.converted)
					transformButtonToInProgress();
				else 
				{
					if (data.emptyqueue)
					{
						$('#convert_btn').button().button('option', 'label', 'Convert movie');
						$("#convert_btn").button().button("enable");	
					}
				}
		  }
		});


function convertFile()
{
	$("#dialog_ok_convert").dialog('open');
					
	$.ajax({
	  url: 'api.php',
	  data: "ac=add_movie_to_queue&id="+$('#movie_reference').text(),
	  type: "GET",
	  dataType: "json"
	});
				
	//check_queue_d = startCheck( {$current_media.file_id} );
	transformButtonToInProgress();
	$( this ).dialog( "close" );
	
}

function transformButtonToInProgress()
{
	$('#convert_btn').button().button('option', 'label', 'Generate in progress');
	$("#convert_btn").button().button("disable");	
}


function transformButtonToFinished()
{
	$('#convert_btn').button().button('option', 'label', 'Finished, please wait');
	$("#convert_btn").button().button("disable");
	alert("Need refreshing this page!!");		
}


function startCheck(id_file)
{
	var check_queue_done = setInterval(function()
	{
	
		$.ajax({
		  url: 'api.php',
		  data: "ac=get_message_queue&id="+$('#movie_reference').text(),
		  type: "GET",
		  dataType: "json",
		
		  success: function(data) {
			
				if (data.converted)
					transformButtonToInProgress();
				else 
				{
					if (data.emptyqueue);
					{
						clearInterval(check_queue_done);
						transformButtonToFinished();
					}
				}
		  }
		});
	}, 2000);	
	return check_queue_done;

}


})
</script>

