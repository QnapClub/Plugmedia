{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 

<script language="javascript">
var plural_string = '{t}USER{/t}';
var singular_string = '{t}USERS{/t}';
var pre_string = '{t}DELETE{/t}';
</script>
<script language="javascript" src="{$adresse_js}/jquery/jquery.multiselect.js"></script> 
<script type="text/javascript" language="javascript" src="{$adresse_js}/administration.js"></script>



<div class="page_title">{t}USERSLIST{/t}</div>
<br />
<form action="secureadmin_.php?act=list_user" method="post" name="qnap_form" id="qnap_form">

{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
        &nbsp;
    </div>

<div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <a class="small_bt_add custom_target" href="secureadmin_.php?act=add_user&view=inline" id="user_create" style="cursor: pointer;">
        <span class="qnapstr">{t}ADDUSER{/t}</span>
        </a>
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%" style="overflow: hidden; table-layout: fixed;">
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
    <col /> 
     <col />    
    <thead>
    <tr>
    <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
    <input onclick="checkAllFieldsandGetSelect(1,'delusr[]','delusr','delete_btn');" class="checkuser" id="delusr" type="checkbox">
    </td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}USER{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}NAME{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}EMAIL{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}DEFAULTLANG{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}IP{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}GROUPS{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" align="center" title="{t}PLUGMEDIAUSER{/t}"><span class="qnapstr" title="{t}PLUGMEDIAUSER{/t}">{t}EMBEDED{/t}</span></td>
    <td class="box_td_title_end" style="white-space: nowrap;" align="center"><span class="qnapstr">{t}ACTION{/t}</span></td>
    </tr>
    </thead>
    <tbody>
{foreach from=$listuser item=user key=k name=usrlt}
  <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
            <td  valign="top" align="center"><input onclick="checkAllFieldsandGetSelect(2,'delusr[]','delusr','delete_btn');" class="checkuser"  name="delusr[]" id="delusr[]" type="checkbox" {if $k eq 'Guest' || $k eq 'admin'}disabled="disabled"{elseif $user.embeded eq 0}disabled="disabled"{/if} value="{$user.id}" ></td>
            <td valign="top"><strong>{$user.login}</strong></td>
            <td valign="top">{$user.name}</td>
            <td valign="top">{$user.email}</td>
            <td valign="top">{$user.lang}</td>
    		<td valign="top">{$user.last_ip|decode_ip}</td>
   			<td valign="top">{foreach from=$user.groups item=group}{$group}<br />{/foreach}</td>
        <td  valign="top" align="center">{if $user.embeded eq 1}<img border="0" src="{$adresse_images}/accept.png"/>{/if}</td>
            <td width="70" align="center"  valign="top" class="box_td_end"><a class="custom_target" href="secureadmin_.php?act=edit_user&usr={$user.id}&view=inline"><img src="{$adresse_images}/pencil.png" title="{t 1=$user.login}EDITUSER{/t}" /></a></td>
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
        <span class="total_tab">{t 1=$smarty.foreach.usrlt.total}TOTALUSR{/t}</span>
      </div>
    </div>


</div>
</form>

<div id="information_message">{t}INFODELETE{/t}</div>


<script language="javascript">
function sendForm() { 
    $('#loading_form').show();
	$('#qnap_form').ajaxSubmit({ 
	success:   function() { 
		PM.loadingPage('secureadmin_.php?act=list_user&view=inline') }
	});
};



</script>

{include file="admin_innerfooter.tpl"}