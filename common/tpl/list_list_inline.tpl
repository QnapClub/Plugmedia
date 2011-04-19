{include file="button_bar.tpl" display_left="info_subfolder"}

       
           


<table width="100%" border="0">
  <tr>
    <th scope="col">{t}NAME{/t}</th>
    <th scope="col">{t}TAILLE{/t}</th>
    <th scope="col">{t}TYPE{/t}</th>
    <th scope="col">{t}MODIFICATIONDATE{/t}</th>
    <th scope="col">{t}CREATIONDATE{/t}</th>
  </tr>

{foreach from=$list item=media}
  <tr>
{if $media.type eq 'dir' || $media.type eq 'link'}
    <td><a class="custom_target" href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}&view=inline"><img src="{$adresse_images}/folder_page_white.png" /> {$media.short_name}</a></td>
{else}
    <td><a class="custom_target" href="display.php?dir={$current_dir.link_dir}&file={$media.file_id}&ref={$smarty.get.ref}&view=inline"><img src="{$adresse_images}/{$media.extension}.png" /> {$media.short_name}</a></td>
{/if}
    <td>{$media.size}</td>
    <td>{$media.readable_type}</td>
    <td>{$media.last_modification_date|date_format:"%d-%m-%Y %H:%M":null:true}</td>
    <td>{$media.original_date|date_format:"%d-%m-%Y %H:%M":null:true}</td>
  </tr>
{foreachelse}
Ce dossier est vide.
{/foreach}
  
</table>
		

           
<br clear="all" />

{* display pagination info - Display only if we have page... *}
{if $paginate.page_total gt 1}
<p class="paging">
{capture name=prev_txt}{t}PREVIOUS{/t}{/capture}
{capture name=next_txt}{t}NEXT{/t}{/capture}
{paginate_prev id="list" text=$smarty.capture.prev_txt}{paginate_middle id="list" format="page" prefix="" suffix=""  page_limit="5"}{paginate_next id="list" text=$smarty.capture.next_txt}
</p>
{/if}

