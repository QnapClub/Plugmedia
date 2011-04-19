{* This file is used for a dynamic ajax loading *}            
            
            
	{include file="button_bar.tpl" display_left="info_subfolder"}


<!--SLIDER-->
<div class="slider_pic">
	<div id="grid_slider">
    	<div class='ui-slider-handle' ></div>
	</div>
</div>
        
<ul id="grid_list_item" class="grid {if $default_view eq 'thumb_list'}thumb_view{/if}">            
    {foreach from=$list item=media}
        {if $media.type eq 'dir' || $media.type eq 'link'}
            {include file="list_media_content_directory.tpl"}
        {else}
            {include file="list_media_content_media.tpl"}
        {/if}
    {foreachelse}
        {t}FOLDEREMPTY{/t}
    {/foreach}
</ul>

           
<br clear="all" />

{* display pagination info - Display only if we have page... *}
{if $paginate.page_total gt 1}
<p class="paging">
{capture name=prev_txt}{t}PREVIOUS{/t}{/capture}
{capture name=next_txt}{t}NEXT{/t}{/capture}
{paginate_prev id="list" text=$smarty.capture.prev_txt}{paginate_middle id="list" format="page" prefix="" suffix=""  page_limit="5"}{paginate_next id="list" text=$smarty.capture.next_txt}
</p>
{/if}
<br />



<script language="javascript">
$(document).ready( function() { 
var marray_ = "{foreach from=$list item=th name=items}{if $th.generate_thumb}{$th.file_id}{if !$smarty.foreach.items.last},{/if}{/if}{/foreach}";
var pictures = new Array();
pictures=marray_.split(',');
for(i=0;i<pictures.length;i++)
{
	if (pictures[i] != "")
	{
	
		$.ajax({
			  mode: 'queue',
			  url: 'api.php?ac=genth',
			  port: 'pm_gen',
			  type:'GET',
			  cache: false,
			  data: "img="+pictures[i],
			  success: function(data) {
				ar = data.split('|');
				value_temp =  "#th_"+ar[1];
				$(value_temp).html('<img id="thumb_'+ ar[1] +'_" src="'+ar[0]+'" '+ar[2]+' align="middle" />');
				preload_image = new Image(); 
				preload_image.src=ar[0]; 
			  },
			  error: function(){  }
		});
	}
}
{if $smarty.get.view neq "inline"}
$("#pm_tree").bind("reopen.jstree", function () {
	$.ajax({ mode: 'dequeue', port: 'pm_gen' });
}); 
{else}
$.ajax({ mode: 'dequeue', port: 'pm_gen' });
{/if}


});
</script>


