<?xml version="1.0" encoding="UTF-8"?>
<gallery>
<album id="QNAP_album" title="QNAP ALBUM" description="" >
{foreach from=$list item=media} 
{if $media.extension eq "picture"}

<img src="../../../../api.php?ac=rotatePic&amp;pic={$media.file_id}&amp;percent=1" tn="../../../../{$media.small_thumbnail}" title="" caption="" link="http://{$PM_LOCATION}/display.php?dir={$current_dir.link_dir}&amp;file={$media.file_id}&amp;ref={$smarty.get.ref}" target="_blank" pause="" vidpreview="" />

{elseif $media.extension eq "movie_displayable"}
<img src="{$media.name}" title="" caption="" link="http://{$PM_LOCATION}/display.php?dir={$current_dir.link_dir}&amp;file={$media.short_name_formated}&amp;ref={$smarty.get.ref}" target="_blank" pause="" vidpreview="" />
{/if}
{/foreach}
</album>
</gallery>