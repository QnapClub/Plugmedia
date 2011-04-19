{foreach from=$comments item=com}
<div class="commentbody white">
	<div class="dashed"> 
    	<table width="98%" border="0" cellpadding="0" cellspacing="0" ><tr><td width="80px" height="55px" align="left" valign="top" class="avatar_im"><img src="{$common_image}/avatar/48/{$user_info.avatar}" alt="" /></td>
<td valign="top"><span class="title">{t}POSTEDBY{/t} <strong>{$com.displayable_name}</strong> {t}AT{/t} {$com.time|date_format:"%d-%m-%Y %H:%M:%S":null:true}</span>
        {$com.comment|decode_utf8|stripslashes|nl2br}</td>      
        </tr></table>
	</div>
</div>
{/foreach}