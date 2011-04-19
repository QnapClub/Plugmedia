<li class="{$media.extension}">
	<div class="content_block">
        <a class="custom_target" href="display.php?dir={$current_dir.link_dir}&file={$media.file_id}&ref={$smarty.get.ref}&view=inline"> 
        <div id="th_{$media.file_id}" class="pic_list_top">
        <span></span>
        {if isset($media.small_thumbnail) && $media.small_thumbnail neq ""}
        
        <img id="thumb_{$media.file_id}_" src="{$media.small_thumbnail}" title="{$media.short_name}" {$media.small_thumbnail_size} align="middle" />	
        
        {else}
        
        <img src="{if isset($media.generate_thumb) && $media.generate_thumb == true}{$adresse_images}/generate.gif{else}{$adresse_images}/no_thumb.gif{/if}" align="middle"  border="0"  title="{$media.short_name}"  width="400" style="width:4em;"  />

        {/if}        
        </div>
        </a>
        <div class="title">
        <a class="custom_target short" href="display.php?dir={$current_dir.link_dir}&file={$media.file_id}&ref={$smarty.get.ref}&view=inline" title="{$media.short_name}">{$media.short_name_displayable|truncate:15:"..."}</a>
        <a class="custom_target long" href="display.php?dir={$current_dir.link_dir}&file={$media.file_id}&ref={$smarty.get.ref}&view=inline" title="{$media.short_name}">{$media.short_name_displayable}</a>
        <p><br />{$media.smart_description}</p>
        </div>
    </div>
</li>