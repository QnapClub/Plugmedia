{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 
            
<div class="page_title">{t}THUMBANDTRANSCODING{/t}</div>

		<table class="tabl_config2" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td class="tab_c" style="width: 50%; padding-right: 35px;"> <strong>{t}THUMBTRANS_REPO{/t}</strong><div class="desctext">{t}THUMBTRANS_REPO_EXPLAIN{/t}</div></td>
			  <td valign="top" class="tab_c" style="width: 40%;">
				<br />
				<button style="font-size:12px;" type="button" id="button_cache">{t}THUMBNAIL_REPO_DROP{/t}</button>
				<br />
                <div id="message_cache" class="message_index_cl">&nbsp;</div>  
                
			  </td>
			</tr>

	</table>

<div id="dialog-confirm" style="display:none;" title="{t}THUMBNAIL_REPO_DROP{/t}">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>{t}CLEAR_CACHE_CONFIRM{/t}</p>
</div>


 {literal}
<script language="javascript">

$("#message_cache").hide();
var status_button_indexing = '';

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
	
	
	
	

	$.ajax({
	  url: 'api.php',
	  data: "ac=droprepository",
	  type: "GET",
	  dataType: "json",
	
	  success: function(data) {
		
		
		if (data.success)
		{
			
			$("#message_cache").html("{/literal}{t}CACHE_CLEARED{/t}{literal}").fadeIn('slow');
		}
		else
			$("#message_cache").html("{/literal}{t}ERROR{/t}{literal}" + data.message).fadeIn('slow');
		
		
		
		$('#button_cache')
			.button({ icons: {primary: 'clear_cache' } })
			.bind('click',function () { openConfirmDeleteCache(); });	
		
		$('#button_cache > .ui-button-text').html("{/literal}{t}CLEAR_CACHE{/t}{literal}");
		
	  }
	});
	
	
		
	
	
	
	
}







</script>
{/literal} 

{include file="admin_innerfooter.tpl"}