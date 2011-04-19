<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
    <title>QNAP</title>
    <description>QNAP - cooliris</description>
    <atom:icon>{$adresse_images}/logo_rss.jpg</atom:icon>
        {foreach from=$list item=media}
        <item>
            <title>{$media.short_name}</title>
            <link>display.php?dir={$current_dir.link_dir}&amp;file={$media.file_id}&amp;ref={$smarty.get.ref}</link>
            <media:thumbnail url="{if isset($media.small_thumbnail)}{$media.small_thumbnail|htmlentities}{/if}"/>
            <media:content url="api.php?ac=rotatePic&amp;pic={$media.file_id}&amp;percent=1" type="" />
        </item> 
        {/foreach}
    </channel>
</rss>