<li class="directory">
	<div class="content_block">
        <a class="custom_target" href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}&view=inline"> 
        <div id="th_{$media.dir_id}" class="pic_list_top">
        <span></span>
        {if $media.thumb neq ""}
        <img src="{$media.thumb}" title="{$media.short_name}" {$media.thumb_size} align="middle" />	
        {else}
        <img src="{$adresse_images}/blank.gif" title="{$media.short_name}"  height="400" style="height:4em;" align="middle" />
        {/if} 
        </div>       
        </a>
        <div class="title">
        <a class="custom_target short" href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}&view=inline" title="{$media.short_name}">{$media.short_name_displayable|truncate:15:"..."}</a>
        <a class="custom_target long" href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}&view=inline" title="{$media.short_name}">{$media.short_name_displayable}</a>
        <p><br />{$media.smart_description}</p>
        </div>
        
    </div>
</li>