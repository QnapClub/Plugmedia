{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 



{if $version_check_result.result eq 0}
	<div id="error_message">{$version_check_result.info}{t}NEEDUPDATE{/t} ({$version_check_result.version}) <a href="{$version_check_result.link_update}" target="_blank">{t}DOWNLOAD{/t}</a></div>
{elseif $version_check_result.result eq 1}
	<div id="success_message">{t}UPTODATE{/t}</div>
{else}
	<div id="information_message"><form action="secureadmin_.php?act=summary&view=inline&vcheck=1" id="frm_check" method="post">{t}VERSION_CHECK{/t}  <button id="btn_check" type="button">{t}CHECK{/t}</button></form></div>
{/if}



<script language="javascript">
$("#btn_check").button().click(function() { PM.loadingPage($("#frm_check").attr('action')); return false; });
</script>



<br />

<div class="page_title">{t}PRIVILEGEMGT{/t}</div>
<div class="function_list">
<ul>
<li>
    <a class="custom_target" href="secureadmin_.php?act=list_user&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_privilege_1_L.gif"/><br/>{t}USERS{/t}</a>
</li>
<li>
    <a class="custom_target" href="secureadmin_.php?act=list_group&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_privilege_2_L.gif"/><br/>{t}GROUPS{/t}</a>
</li>
</ul>
</div>

<div class="page_title">{t}COMMENTS{/t}</div>
<div class="function_list">
<ul>
<li>
    {if $total_new_comment.total_new > 0}<div id="comment_number" title="{t}NEWCOMMENTS{/t}">{$total_new_comment.total_new}</div>{/if}
    <a class="custom_target" href="secureadmin_.php?act=list_comment&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_network_6_L.gif"/><br/>{t}COMMENTLIST{/t}</a>
</li>
</ul>
</div>

<div class="page_title">{t}SYSTEMADMIN{/t}</div>
<div class="function_list">
<ul>
<li>
    <a class="custom_target" href="secureadmin_.php?act=config&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_system_1_L.gif"/><br/>{t}CONFIGURATION{/t}</a>
</li>
<li>
    <a class="custom_target" href="secureadmin_.php?act=thumbandtranscode&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/thumb_metadata.gif"/><br/>{t}THUMBANDTRANSCODING{/t}</a>
</li>

<li>
    <a class="custom_target" href="secureadmin_.php?act=indexing&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_system_8_L.gif"/><br/>{t}INDEXING{/t}</a>
</li>

<li>
    <a class="custom_target" href="secureadmin_.php?act=plugins&view=inline" style="text-align: center; cursor: pointer;">    <img border="0" src="{$adresse_images}/admin/icon_system_10_L.gif"/><br/>{t}PLUGINS{/t}</a>
</li>



</ul>
</div>

{include file="admin_innerfooter.tpl"}