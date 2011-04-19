{include file="header.tpl"}

{* TRAILER *}
{include file="trailer.tpl"}

<!-- {* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} -->
    <div id="inner_center_demo" class="ui-layout-center">
			<div class="ui-layout-content" id="ui_content_pm">
			
        
            
<br />
           
            
            
		

<!--SLIDER-->
<div class="slider_pic">
	<div id="grid_slider">
    	<div class='ui-slider-handle' ></div>
	</div>
</div>
        
<ul id="grid_list_item" class="grid {if $default_view eq 'thumb_list'}thumb_view{/if}">            
    {foreach from=$list item=media}
       
       
     <li class="directory">
        <div class="content_block">
            <a class="custom_target" href="list.php?dir={$media.dir_id}&ref={$media.dir_id}&view=inline"> 
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
            <a class="custom_target short" href="list.php?dir={$media.dir_id}&ref={$media.dir_id}&view=inline" title="{$media.short_name}">{$media.short_name_displayable|truncate:15:"..."}</a>
            <a class="custom_target long" href="list.php?dir={$media.dir_id}&ref={$media.dir_id}&view=inline" title="{$media.short_name}">{$media.short_name_displayable}</a>
            <p><br />{$media.smart_description}</p>
            </div>
            
        </div>
    </li>          
       
       
       
    {foreachelse}
             {if $loggedin}
                <div id="no_albums">{t}NOALBUMYET{/t}.</div>
                {else}
                
				<br /><br /><br /><div id="information_message">{t}CONNECTALBUM{/t}</div>
			
                {/if}
    {/foreach}
</ul>

            
            
            
                    
      

 



	</div>
	</div>

{include file="footer.tpl"}