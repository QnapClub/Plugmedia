{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 
            
<div class="page_title">{t}INDEXING{/t}</div>

		<table class="tabl_config2" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td class="tab_c" style="width: 50%; padding-right: 35px;"> <strong>{t}INDEXING_TASK{/t}</strong><div class="desctext">{t}INDEXING_EXPLAIN{/t}</div></td>
			  <td valign="top" class="tab_c" style="width: 40%;" >
				<br />
				<button style="font-size:12px;" type="button" id="button_indexing">{t}GENERATE_INDEX{/t}</button>
				<br />
                <div id="message_index" class="message_index_cl">{t}PLEASE_WAIT{/t}</div>  
                
			  </td>
			</tr>
            <tr>
			  <td class="tab_c" style="width: 50%; padding-right: 35px;"> <strong>{t}INDEXING_CACHE{/t}</strong><div class="desctext">{t}INDEXING_CACHE_EXPLAIN{/t}</div></td>
			  <td valign="top" class="tab_c" style="width: 40%;">
				<br />
				<button style="font-size:12px;" type="button" id="button_cache">{t}CLEAR_CACHE{/t}</button>
				<br />
                <div id="message_cache" class="message_index_cl">&nbsp;</div>  
                
			  </td>
			</tr>

	</table>

<div id="dialog-confirm" style="display:none;" title="{t}CLEAR_CACHE{/t}">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>{t}CLEAR_CACHE_CONFIRM{/t}</p>
</div>


 {literal}
<script language="javascript">

$("#message_cache").hide();
var status_button_indexing = '';

$('#button_indexing')
.button({ icons: {primary: 'img_start_index' } })
.bind('click',function () {  })
.button( "disable" );


$('#button_cache')
.button({ icons: {primary: 'clear_cache' } })
.bind('click',function () { openConfirmDeleteCache(); });	









function openConfirmDeleteCache()
{
	$( "#dialog-confirm" ).dialog({
				resizable: false,
				height:170,
				modal: true,
				buttons: {
					"OK": function() {
						$( this ).dialog( "close" );
						confirmClearCache();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
	});
}
function confirmClearCache()
{
	$('#button_cache').button({ icons: {primary: 'img_perform_index' } });
	$('#button_cache > .ui-button-text').html("{/literal}{t}CLEARING{/t}{literal}");
	$('#button_cache').unbind('click');
	
	
	$('#button_indexing').button( "disable" );
	

	$.ajax({
	  url: 'api.php',
	  data: "ac=clearcache",
	  type: "GET",
	  dataType: "json",
	
	  success: function(data) {
		
		
		if (data.success)
		{
			setStartButton();
			$("#message_cache").html("{/literal}{t}CACHE_CLEARED{/t}{literal}").fadeIn('slow');
		}
		else
			$("#message_cache").html("{/literal}{t}ERROR{/t}{literal}" + data.message).fadeIn('slow');
		
		$('#button_indexing').button( "enable" );
		
		$('#button_cache')
			.button({ icons: {primary: 'clear_cache' } })
			.bind('click',function () { openConfirmDeleteCache(); });	
		
		$('#button_cache > .ui-button-text').html("{/literal}{t}CLEAR_CACHE{/t}{literal}");
		
	  }
	});
	
	
		
	
	
	
	
}






function setStartButton()
{
	status_button_indexing = "START";
	
	$('#button_indexing').button( "enable" )
							.unbind('click')
							.bind('click',function () { startindexing() })
							.button({ icons: {primary: 'img_start_index' } });
							
	$('#button_indexing > .ui-button-text').html("{/literal}{t}GENERATE_INDEX{/t}{literal}");
	

	$('#button_cache').button( "enable" );
	//buttonObject.setIconClass('img_start_index');
}

function setStopButton()
{
	status_button_indexing = "STOP";
	
	$('#button_indexing')	.button( "enable" )
							.unbind('click')
							.bind('click',function () { stopIndexing() })
							.button({ icons: {primary: 'img_stop_index' } });

	$('#button_indexing > .ui-button-text').html("{/literal}{t}STOP_INDEXING{/t}{literal}");

	$('#button_cache').button( "disable" );

	
}

var check_indexing = setInterval(function()
{

	$.ajax({
	  url: 'api.php',
	  data: "ac=indexingRunning",
	  type: "GET",
	  dataType: "json",
	
	  success: function(data) {
		
		if (data.running)
		{
			if (status_button_indexing != "STOP")
				setStopButton();
			$("#message_index").html("{/literal}{t}INDEXING_RUNNING{/t}{literal}");
		}
		else
		{
			if (status_button_indexing != "START")
				setStartButton();
			$("#message_index").html("{/literal}{t}INDEXING_TASK_DATE{/t}{literal}" + data.message);
			
		}
		
	  }
	});
}, 2000);


function startindexing() 
{

	$('#button_indexing').button({ icons: {primary: 'img_perform_index' } });
	$('#button_indexing > .ui-button-text').html("{/literal}{t}STARTING{/t}{literal}");

	$.ajax({
	  url: 'api.php',
	  data: "ac=startindexing",
	  type: "GET",
	  dataType: "json",
	
	  success: function(data) {
		
		if (data.success)
		{
			setStopButton();
			$("#message_index").html("{/literal}{t}INDEXING_RUNNING{/t}{literal}");
		}
		else
			$("#message_index").html("{/literal}{t}ERROR{/t}{literal}" + data.message);
		
	  }
	});

}

	
function stopIndexing()
{


	$('#button_indexing').button({ icons: {primary: 'img_perform_index' } });
	$('#button_indexing > .ui-button-text').html("{/literal}{t}STOPPING{/t}{literal}");


	$.ajax({
	  url: 'api.php',
	  data: "ac=stopindexing",
	  type: "GET",
	  dataType: "json",
	
	  success: function(data) {
		
		if (data.success)
		{
			setStartButton();
			$("#message_index").html("{/literal}{t}INDEXING_STOPPED{/t}{literal}");
		}
		else
			$("#message_index").html("{/literal}{t}ERROR{/t}{literal}" + data.message);
		
	  }
	});
		
}


</script>
{/literal} 

{include file="admin_innerfooter.tpl"}