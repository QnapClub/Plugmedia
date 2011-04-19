{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 
            
<script language="javascript">
var plural_string = '{t}GROUP{/t}';
var singular_string = '{t}GROUPS{/t}';
var pre_string = '{t}DELETE{/t}';
</script>  
<script language="javascript" src="{$adresse_js}/jquery/jquery.multiselect.js"></script> 
<script type="text/javascript" language="javascript" src="{$adresse_js}/administration.js"></script>








<div class="page_title">{t}GROUPLIST{/t}</div>
<br />

<form action="secureadmin_.php?act=list_group" method="post" name="qnap_form" id="qnap_form">

{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
        &nbsp;
    </div>

<div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <a href="secureadmin_.php?act=add_group&view=inline" class="small_bt_add custom_target" id="group_create" style="cursor: pointer;">
        <span class="qnapstr">{t}ADDGROUP{/t}</span>
        </a>
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
    <tr>
    <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
    <input onclick="checkAllFieldsandGetSelect(1,'delgrp[]','delgrp','delete_btn');" class="checkgrp" id="delgrp" type="checkbox">
    </td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}GROUPNAME{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}USERS{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}ACCESSPATH{/t}</span></td>
    <td class="box_td_title_end" style="white-space: nowrap;" align="center"><span class="qnapstr">{t}ACTION{/t}</span></td>
    </tr>
    </thead>
    <tbody>
{foreach from=$grouplist item=group key=k name=grplt}
  <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
            <td align="center"  valign="top" ><input onclick="checkAllFieldsandGetSelect(2,'delgrp[]','delgrp','delete_btn');" class="checkuser"  name="delgrp[]" id="delgrp[]" type="checkbox" {if $group.name eq 'Guest' || $k eq 'Administrator'}disabled="disabled"{/if} value="{$group.id}" ></td>
            <td valign="top"><strong>{$group.name}</strong></td>
    		<td valign="top">{foreach from=$group.users item=user}{$user}<br />{/foreach}</td>
   			<td valign="top">{foreach from=$group.access_path item=path}{$path}<br />{/foreach}</td>
            <td valign="top" width="70" align="center" class="box_td_end"><a class="custom_target" href="secureadmin_.php?act=edit_group&grp={$group.id}&view=inline"><img src="{$adresse_images}/pencil.png" title="{t}EDITGROUP{/t} {$group.name}" /></a></td>
      </tr>
{/foreach}  
        </tbody>
    </table>

	{* TAB FOOTER *}
    <div class="box_end_div" style="margin-right: -2px;">
      <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
            <a class="small_bt_del" href="javascript:sendForm();" onclick="this.blur();"><span class="qnapstr" id="delete_btn">{t}DELETE{/t}</span></a>
      </div>
 	 <div id="loading_form" style="display: inline; display:none; float: left; padding-top: 1px; padding-left: 4px;">
        <img width=20 src="{$adresse_images}/large-loading.gif" />
      </div>     
    
      <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <span class="total_tab">{t 1=$smarty.foreach.grplt.total}TOTALGRP{/t}</span>
      </div>
    </div>


</div>
</form>

<script language="javascript">
function sendForm() { 
    $('#loading_form').show();
	$('#qnap_form').ajaxSubmit({ 
	success:   function() { 
		PM.loadingPage('secureadmin_.php?act=list_group&view=inline') }
	});
};
</script>

{include file="admin_innerfooter.tpl"}