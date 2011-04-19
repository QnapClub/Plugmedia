{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 
 
 
<script language="javascript">
var plural_string = '{t}COMMENT{/t}';
var singular_string = '{t}COMMENTS{/t}';
var pre_string = '{t}DELETE{/t}';
</script>  
<script type="text/javascript" language="javascript" src="{$adresse_js}/administration.js"></script>

<!--<div id="trailer" style="display:none;"><a href="index.php" title="{t}GOBACKHOME{/t}"><img src="{$adresse_images}/icon_home_12.gif" /> {t}ACCUEIL{/t}</a>
 &raquo; <a href="secureadmin_.php">{t}ADMINISTRATION{/t}</a> &raquo; {t}COMMENTLIST{/t}</div>-->
<div class="page_title">{t}COMMENTLIST{/t}</div>
<br />



<form method="post" name="qnap_form" id="qnap_form">

{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
        &nbsp;
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
    <tr>
        <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
        <input onclick="checkAllFieldsandGetSelect(1,'delcom[]','delcom','delete_btn');" class="checkuser" id="delcom" type="checkbox">
        </td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}AUTHOR{/t}</span></td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}COMMENT{/t}</span></td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}INREPLYON{/t}</span></td>
    </tr>
    </thead>
    <tbody>
{foreach from=$listcomment item=comment key=k name=commentlt}
		<tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
			<td align="center"><input onclick="checkAllFieldsandGetSelect(2,'delcom[]','delcom','delete_btn');" class="checkuser"  name="delcom[]" id="delcom[]" type="checkbox" value="{$comment.comment_id}" ></td>
            <td valign="top">
				<img src="{$adresse_images}/avatar.jpg" alt="" width="32" height="32" class="avatar_admin" />            
				<strong>{$comment.displayable_name}</strong><br />
            	{$comment.email}
            </td>
            <td valign="top">
<div id="submitted_on">{t}SUBMITON{/t} {$comment.time|date_format:"%d-%m-%Y %H:%M:%S":null:true}  </div>          

{$comment.comment|decode_utf8|stripslashes|nl2br}</td>
 			<td valign="top"><a href="display.php?dir={$comment.dir_id}&file={$comment.file_id}">{$comment.filename}</a></td>
		</tr>

{/foreach}  
        </tbody>
    </table>

	{* TAB FOOTER *}
    <div class="box_end_div" style="margin-right: -2px;">
      <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
            <a class="small_bt_del" href="javascript:document.qnap_form.submit();" onclick="this.blur();"><span class="qnapstr" id="delete_btn">{t}DELETE{/t}</span></a>
      </div>
    
      <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <span class="total_tab">{t 1=$smarty.foreach.commentlt.total}TOTALCOMMENT{/t}</span>
      </div>
    </div>


</div>
</form>

{include file="admin_innerfooter.tpl"}