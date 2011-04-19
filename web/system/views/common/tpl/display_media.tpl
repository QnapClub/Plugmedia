{* This file is used for a dynamic ajax loading *}

{include file="button_bar.tpl"  display_left="info_file" display_right="no"}


    
<div id="display_media">
	{if isset($error)}<div id="error_message">{$error}</div><br />{/if}
<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="70" align="center" valign="middle">{if $prev_media.short_name_formated neq ""}<a id="left_link" class="custom_target" href="display.php?dir={$current_dir.link_dir}&file={$prev_media.file_id}&ref={$smarty.get.ref}&view=inline" ><img src="{$adresse_images}/detail_left.jpg" alt="" border="0" /></a>{/if}</td>
    <td align="center" valign="top">{include file=$current_media.tpl_media}</td>
    <td width="70" align="center" valign="middle">
    {if isset($next_media.short_name_formated) && $next_media.short_name_formated neq ""}
    <a id="right_link" class="custom_target" href="display.php?dir={$current_dir.link_dir}&file={$next_media.file_id}&ref={$smarty.get.ref}&view=inline">
    <img src="{$adresse_images}/detail_right.jpg" alt="" border="0" /></a>
    {/if}
    </td>
  </tr>
</table>
    
</div>




    <div id="walk_elements">
    	{foreach from=$walk_elements item=elem name=walkelem}
       		<div class="element">
            {if !isset($elem.current) || $elem.current neq 1}
               
                <a class="custom_target" href="display.php?dir={$current_dir.link_dir}&file={$elem.file_id}&ref={$smarty.get.ref}&view=inline" title="{$elem.short_name}" title="{$elem.short_name}">
               
                {if $elem.small_thumbnail neq ""}
                <img src="{$elem.small_thumbnail}"  style="max-height:70px;max-width:70px;" border="0" />
                {else}
                <img src="{$adresse_images}/no_thumb.gif"  border="0" />
                {/if}
                </a>
            {else}
                {if $elem.small_thumbnail neq ""}
                <img src="{$elem.small_thumbnail}" style="max-height:70px;max-width:70px;" border="0" class="element_select" />
                {else}
                <img src="{$adresse_images}/no_thumb.gif" border="0" class="element_select"/>
                {/if}
            {/if}
            </div>
        {/foreach}
    </div>
  
   
<br clear="all" />

{include file='detailandcomment.tpl'}