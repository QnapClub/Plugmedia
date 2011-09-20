{include file="header.tpl"}
<script type="text/javascript">
		
		/*
		 * IMPORTANT!!!
		 * REMEMBER TO ADD  rel="external"  to your anchor tags. 
		 * If you don't this will mess with how jQuery Mobile works
		 */
		
		$(document).ready(function(){

	var myPhotoSwipe = $("#Gallery li.file_th a").photoSwipe({ enableMouseWheel: false , enableKeyboard: false });

});

$('.ui-btn-back').live('tap',function() {
  history.back(); return false;
}).live('click',function() {
  return false;
});

		
	</script>

	<div data-role="content">
	

	
        <ul data-role="listview" data-inset="true" id="Gallery"> 
        <li data-role="list-divider" data-icon="back"><a href='#' class='ui-btn-back'>{t}DIRECTORIES{/t}</a></li>
{foreach from=$list item=media}
	{if $media.type eq 'dir' || $media.type eq 'link'}
    
			
				<li><a href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}">
				{if $media.thumb neq ""}<img src="{$media.thumb}" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{else}<img src="{$adresse_images}/blank.gif" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{/if}
				<h3>{$media.short_name_displayable|truncate:20:"..."}</h3>
				<p>{$media.short_name_displayable|truncate:20:"..."}</p></a></li>
     {/if}
{foreachelse}
	<li>{t}FOLDEREMPTY{/t}</li>
{/foreach}

<li data-role="list-divider">FILES</li>
{foreach from=$list item=media}
	{if $media.type neq 'dir' && $media.type neq 'link'}
    
			
				<li class='file_th'><a href="api.php?ac=rotatePic&pic={$media.file_id}&percent=1.5"  rel="external">
				{if isset($media.small_thumbnail) && $media.small_thumbnail neq ""}
        <img id="thumb_{$media.file_id}_" src="{$media.small_thumbnail}" title="{$media.short_name}" {$media.small_thumbnail_size} align="middle" />	
        {else}
        <img src="{if isset($media.generate_thumb) && $media.generate_thumb == true}{$adresse_images}/generate.gif{else}{$adresse_images}/no_thumb.gif{/if}" align="middle"  border="0"  title="{$media.short_name}"  width="400" style="width:4em;"  />
        {/if}
				<h3>{$media.short_name_displayable|truncate:20:"..."}</h3>
				<p>{$media.short_name_displayable|truncate:20:"..."}</p></a></li>
     {/if}
{foreachelse}
	<li>No files</li>
{/foreach}

			</ul>
			

	</div><!-- /content -->

</div><!-- /page -->

{include file="footer.tpl"}