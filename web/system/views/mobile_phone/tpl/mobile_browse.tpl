<div id="browse">
             <div class="toolbar">
                <h1>Browsing</h1>
					 			<a class="button back" href="#">Retour</a>
					 			<a class="button slideup" href="mobile.php">Home</a>
            </div>
            <ul class="rounded">

{foreach from=$list item=media}
	{if $media.type eq 'dir' || $media.type eq 'link'}
    	
        
<li class="arrow pop"><a href="mobile.php?page=browse&dir={$media.dir_id}&ref={$smarty.get.ref}">{if $media.thumb neq ""}<img src="{$media.thumb}" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{else}<img src="{$adresse_images}/blank.gif" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{/if} <span>{$media.short_name_displayable|truncate:20:"..."}</span></a></li> 
        
        
        
    {else}
		<li class="arrow pop">  
        	<a href="mobile.php?page=display&dir={$current_dir.link_dir}&file={$media.file_id}&ref={$smarty.get.ref}">     
        {if $media.thumbnail neq ""}
                    <img src="{$media.thumbnail}" align="middle"  border="0" title="{$media.short_name|convert_utf8}"  style="vertical-align:top;" />	
                {else}
                    <img src="{if $media.generate_thumb == true}{$adresse_images}/generate.gif{else}{$adresse_images}/no_thumb.gif{/if}" style="vertical-align:top;"  border="0"  title="{$media.short_name|convert_utf8}" />
        {/if}    	
        	<span>{$media.short_name_displayable|truncate:20:"..."}</span></a></li>
    {/if}
{foreachelse}
	{t}FOLDEREMPTY{/t}
{/foreach}




		</ul>
						<ul class="individual">
						                <li>&nbsp;</li>
						                <li><a href="/videos.php?sort=nouveau&page=2">Suivantes ></a></li>

						</ul>
</div> 