{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 
  

<script language="javascript">
var plural_string = '{t}MASK{/t}';
var singular_string = '{t}MASKS{/t}';
var pre_string = '{t}DELETE{/t}';
</script>  
<script type="text/javascript" language="javascript" src="{$adresse_js}/administration.js"></script>

<!--
<div id="trailer" style="display:none;"><a href="index.php" title="{t}GOBACKHOME{/t}"><img src="{$adresse_images}/icon_home_12.gif" /> {t}ACCUEIL{/t}</a>
 &raquo; <a href="secureadmin_.php">{t}ADMINISTRATION{/t}</a> &raquo; {t}CONFIGURATION{/t}</div>-->
<div class="page_title">{t}CONFIGURATION{/t}</div>

<div id="tabs" class="ui-tabs">
	<ul>
		<li><a href="#tabs-1">{t}AUTHORIZATION{/t}</a></li>
		<li><a href="#tabs-2">{t}MASKS{/t}</a></li>
		<li><a href="#tabs-3">{t}VISUALIZATION{/t}</a></li>
        <li><a href="#tabs-4">{t}PROCESSING{/t}</a></li>
        <li><a href="#tabs-5">Plugmedi@</a></li>
        <li><a href="#tabs-6">{t}THUMBNAIL{/t}</a></li>

	</ul>
    
{* *************************** TAB AUTHORISATION **************************************}
    
	<div id="tabs-1"  class="ui-tabs-hide">

		<fieldset class="setting_fieldset">
        <legend>{t}AUTHORIZATION{/t}</legend>
        <form action="secureadmin_.php?act=config&view=inline" method="post" id="form_authorisation">
		  <table class="tabl_config" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}ALLOWDOWNL{/t}</strong><div class="desctext">{t}ALLOWDOWNL_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $allow_download} checked="checked"{/if} value="1" name="allow_dwl" id="allow_dwl_yes">
                        	<label for="allow_dwl_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$allow_download} checked="checked"{/if}  value="0" name="allow_dwl" id="allow_dwl_no">
                        	<label for="allow_dwl_no">{t}NO{/t}</label>
                    </span>
                    <input name="tab_a" type="hidden" value="0" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $allow_download_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_dwnl=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>
		  </table>

        
         
          
         
		<div class="settings_button">
			<input type="submit" class="realbutton" name="change_auth" id="button" value="{t}CHANGE_SETTINGS{/t}" />
		</div>
        
          
        <br />
        </form>
        </fieldset> 
        
	</div>

{* *************************** TAB MASK ************************************* *}

	<div id="tabs-2" class="ui-tabs-hide">
		
	<fieldset class="setting_fieldset">
    <legend>{t}MASKS{/t}</legend>
    
    
    {* TAB HEADER *}
    <div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
        <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
            {t}HIDDEDIRFILENAME{/t}
        </div>
    
        <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
            <a href="javascript:addMask_name();" class="small_bt_add" id="group_create" style="cursor: pointer;">
            <span class="qnapstr">{t}ADDMASK{/t}</span>
            </a>
        </div>
        <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <form id="mask_n_frm" name="mask_n_frm" method="post"  action="secureadmin_.php?act=config&view=inline">
            <input name="add_mask_n" type="hidden" value="1" />
            <input name="tab_a" type="hidden" value="1" />
            <input type="text" name="mask_n" id="mask_n" />
        </form>
         </div>
		 <div style="float: right; padding-top: 1px; display:none; padding-left: 4px;" id="loading_form_add_name">
            <img width="20" src="system/views/common/img/large-loading.gif">
         </div> 
        
    </div>
    <form method="post" name="mask_n_glb_form" id="mask_n_glb_form"  action="secureadmin_.php?act=config&view=inline">
    {* TAB CONTENT *}
    <div class="box_content">
        <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
        <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
        <input onclick="checkAllFieldsandGetSelect(1,'del_mask_n[]','del_mask_n','delete_btn');" class="checkgrp" id="del_mask_n" type="checkbox">
        </td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}{/t}</span></td>
        </tr>
        </thead>
            <tbody>
        {foreach from=$mask_name item=mask_n name=for_mask_n}
          <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
                    <td align="center"><input onclick="checkAllFieldsandGetSelect(2,'del_mask_n[]','del_mask_n','delete_btn');" class="checkuser"  name="del_mask_n[]" id="del_mask_n[]" type="checkbox" {if $mask_n eq '.' ||  $mask_n eq '..' ||  $mask_n eq '.@__comments' ||  $mask_n eq '.@__desc' ||  $mask_n eq '.@__thumb'}disabled="disabled"{/if} value="{$mask_n|convert_bin2hex}" ></td>
                    
                    <td>{$mask_n}</td>
                    
              </tr>
        {/foreach}  
            </tbody>
        </table>
    
        {* TAB FOOTER *}
        <div class="box_end_div" style="margin-right: -2px;">
          <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
                <a class="small_bt_del" href="javascript:deleteMask_name();" onclick="this.blur();"><span class="qnapstr" id="delete_btn">{t}DELETE{/t}</span></a>
                
                {if $mask_name_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_mask_filn=1" class="small_bt_neutral custom_target"><span class="qnapstr" id="delete_btn"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></span></a>{/if}
          </div>
         <div id="loading_form_delete_name" style="display: inline; display:none; float: left; padding-top: 1px; padding-left: 4px;">
            <img width=20 src="{$adresse_images}/large-loading.gif" />
          </div>     
        
          <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
            <span class="total_tab">{t 1=$smarty.foreach.for_mask_n.total}TOTALMASK{/t}</span>
          </div>
        </div>
    </div>
    </form>
    
    <br />
    
    
    
    {* TAB HEADER *}
    <div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
        <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
            {t}HIDDEEXT{/t}
        </div>
    
        <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
            <a href="javascript:addMask_ext();" class="small_bt_add" id="group_create" style="cursor: pointer;">
            <span class="qnapstr">{t}ADDMASK{/t}</span>
            </a>
        </div>
        <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <form id="mask_e_frm" name="mask_e_frm" method="post"  action="secureadmin_.php?act=config&view=inline">
            <input name="add_mask_e" type="hidden" value="1" />
            <input name="tab_a" type="hidden" value="1" />
            <input type="text" name="mask_e" id="mask_e" />
        </form>
         </div>
		 <div style="float: right; padding-top: 1px; display:none; padding-left: 4px;" id="loading_form_add_ext">
            <img width="20" src="system/views/common/img/large-loading.gif">
         </div>         
    </div>
    <form method="post" name="mask_e_glb_form" id="mask_e_glb_form"  action="secureadmin_.php?act=config&view=inline">
    {* TAB CONTENT *}
    <div class="box_content">
        <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
        <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
        <input onclick="checkAllFieldsandGetSelect(1,'del_mask_e[]','del_mask_e','delete_btn_e' );" class="checkgrp" id="del_mask_e" type="checkbox">
        </td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}{/t}</span></td>
        </tr>
        </thead>
        <tbody>
    {foreach from=$mask_extension item=mask_e name=for_mask_e}
      <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
                <td align="center"><input onclick="checkAllFieldsandGetSelect(2,'del_mask_e[]','del_mask_e','delete_btn_e');" class="checkuser"  name="del_mask_e[]" id="del_mask_e[]" type="checkbox" value="{$mask_e|convert_bin2hex}" ></td>
                
                <td>{$mask_e}</td>
                
          </tr>
    {/foreach}  
            </tbody>
        </table>
    
        {* TAB FOOTER *}
        <div class="box_end_div" style="margin-right: -2px;">
          <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
                <a class="small_bt_del" href="javascript:deleteMask_ext();" onclick="this.blur();"><span class="qnapstr" id="delete_btn_e">{t}DELETE{/t}</span></a>
                 {if $mask_extension_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_mask_ext=1" class="small_bt_neutral custom_target"><span class="qnapstr" id="delete_btn"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></span></a>{/if}
          </div>
         <div id="loading_form_delete_ext" style="display: inline; display:none; float: left; padding-top: 1px; padding-left: 4px;">
            <img width=20 src="{$adresse_images}/large-loading.gif" />
          </div>     
        
          <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
            <span class="total_tab">{t 1=$smarty.foreach.for_mask_e.total}TOTALMASK{/t}</span>
          </div>
        </div>
    </div>
    </form>
              
</fieldset>


	</div>

{* *************************** TAB VISUALISATION **************************************}

	<div id="tabs-3" class="ui-tabs-hide">
		
        <fieldset class="setting_fieldset">
        <legend>{t}VISUALIZATION{/t}</legend>
        
        <form id="form_visualisation" name="form2" method="post"  action="secureadmin_.php?act=config&view=inline">

		  <table class="tabl_config" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}ITEMPERPAGE{/t}</strong><div class="desctext">{t}ITEMPERPAGE_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<input name="item_pp" type="text" id="item_pp" value="{$item_per_page}" size="5" maxlength="4" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $item_per_page_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_itempp=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>
            <tr style="background-color:#F3F3F3">
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}GETFIRSTPIC{/t}</strong><div class="desctext">{t}GETFIRSTPIC_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $get_first_picture} checked="checked"{/if} value="1" name="get_first" id="get_first_yes">
                        	<label for="get_first_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$get_first_picture} checked="checked"{/if}  value="0" name="get_first" id="get_first_no">
                        	<label for="get_first_no">{t}NO{/t}</label>
                    </span>
                
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $get_first_picture_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_firstpic=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}EXIFAUTOROT{/t}</strong><div class="desctext">{t}EXIFAUTOROT_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $exif_autorotate} checked="checked"{/if} value="1" name="exif_autorotate" id="exif_autorotate_yes">
                        	<label for="exif_autorotate_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$exif_autorotate} checked="checked"{/if}  value="0" name="exif_autorotate" id="exif_autorotate_no">
                        	<label for="exif_autorotate_no">{t}NO{/t}</label>
                    </span>
                
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $exif_autorotate_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_autor=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>
		  </table>



			<input name="tab_a" type="hidden" value="2" />





	<div class="settings_button">
			<input type="submit" class="realbutton" name="modify_visu" id="button" value="{t}CHANGE_SETTINGS{/t}" />
	</div>

        </form>
        
        
        </fieldset>


	</div>

{* *************************** TAB PROCESSING **************************************}

	<div id="tabs-4" class="ui-tabs-hide">

        <fieldset class="setting_fieldset">
        <legend>{t}PROCESSING{/t}</legend>  

        <form  action="secureadmin_.php?act=config&view=inline" method="post" id="form_processing">
		  <table class="tabl_config" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}REVOKEOUTDATE{/t}</strong><div class="desctext">{t}REVOKEOUTDATE_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $revoke_outdated} checked="checked"{/if} value="1" name="revokeoutdated" id="revokeoutdated_yes">
                        	<label for="revokeoutdated_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$revoke_outdated} checked="checked"{/if}  value="0" name="revokeoutdated" id="revokeoutdated_no">
                        	<label for="revokeoutdated_no">{t}NO{/t}</label>
                    </span>
  
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $revoke_outdated_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_revoke=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>

            <tr style="background-color:#F3F3F3">
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}EXTRACT_ID3{/t}</strong><div class="desctext">{t}EXTRACT_ID3_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $id3_extract} checked="checked"{/if} value="1" name="extract_id3" id="extract_id3_yes">
                        	<label for="extract_id3_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$id3_extract} checked="checked"{/if}  value="0" name="extract_id3" id="extract_id3_no">
                        	<label for="extract_id3_no">{t}NO{/t}</label>
                    </span>
                    <input name="tab_a" type="hidden" value="3" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $id3_extract_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_id3extr=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>

<tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}EXTRACT_COVER_ID3{/t}</strong><div class="desctext">{t}EXTRACT_COVER_ID3_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $id3_cover} checked="checked"{/if} value="1" name="id3_extract" id="id3_extract_yes">
                        	<label for="id3_extract_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$id3_cover} checked="checked"{/if}  value="0" name="id3_extract" id="id3_extract_no">
                        	<label for="id3_extract_no">{t}NO{/t}</label>
                    </span>
          
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $id3_cover_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_id3_cover=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>              

    <tr>

            <tr style="background-color:#F3F3F3">
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}LASTFMCOVER{/t}</strong><div class="desctext">{t}LASTFMCOVER_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $cover_lastfm} checked="checked"{/if} value="1" name="cover_lastfm" id="cover_lastfm_yes">
                        	<label for="cover_lastfm_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$cover_lastfm} checked="checked"{/if}  value="0" name="cover_lastfm" id="cover_lastfm_no">
                        	<label for="cover_lastfm_no">{t}NO{/t}</label>
                    </span>
                    <input name="tab_a" type="hidden" value="3" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $cover_lastfm_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&resetlastfmcover=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>





           


			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}PHPMEMORYSIZE{/t}</strong><div class="desctext"></div></td>
			  <td style="width: 40%;">
				<input name="textfield3" type="text" id="textfield3" disabled="disabled" value="{$memory_limit}" size="4" maxlength="4" />	
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;"></td>
  			  </tr>                         
		  </table>

        
         
          
         
<div class="settings_button">
			<input type="submit" class="realbutton" name="change_processing" id="button" value="{t}CHANGE_SETTINGS{/t}" />
		</div>
        
  </form>
        
        
        
        </fieldset> 
		
       
	</div>

{* *************************** TAB PLUGMEDIA **************************************}

	<div id="tabs-5" class="ui-tabs-hide">

		<fieldset class="setting_fieldset">
        <legend>Plugmedi@</legend>
        <form  id="form_plugmedia_conf"  action="secureadmin_.php?act=config&view=inline" method="post">
		  <table class="tabl_config" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}ALLOW_AUTOMATIC_CHECK{/t}</strong><div class="desctext">{t}ALLOW_AUTOMATIC_CHECK_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<span class="yesno_yes ">
                    	<input type="radio" {if $auto_check} checked="checked"{/if} value="1" name="allow_auto_check" id="allow_auto_check_yes">
                        	<label for="allow_auto_check_yes">{t}YES{/t}</label></span><span class="yesno_no ">
                        <input type="radio" {if !$auto_check} checked="checked"{/if}  value="0" name="allow_auto_check" id="allow_auto_check_no">
                        	<label for="allow_auto_check_no">{t}NO{/t}</label>
                    </span>
                    <input name="tab_a" type="hidden" value="4" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $auto_check_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_auto_check=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>

 			<tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}PLUGMEDIA_URL{/t}</strong><div class="desctext">{t}PLUGMEDIA_URL_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<input name="pm_url" type="text" id="pm_url" value="{$pm_url|default:''}" size="50" /> (ie: {$pm_default})	
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $pm_url_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_pm_url=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>              

 			<tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}PLUGMEDIA_ADMIN_MAIL{/t}</strong><div class="desctext">{t}PLUGMEDIA_ADMIN_MAIL_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<input name="pm_admin_mail" type="text" id="pm_admin_mail" value="{$pm_admin_mail}" size="50" />	
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $pm_admin_mail_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_pm_admin_mail=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>
                            
		  </table>

        
         
          
         
<div class="settings_button">
			<input type="submit" class="realbutton" name="change_auto_check" id="button" value="{t}CHANGE_SETTINGS{/t}" />
		</div>
        
          
          <br />
        </form>
        </fieldset>            
		
      
	</div>

{* *************************** TAB THUMBNAILS **************************************}

	<div id="tabs-6" class="ui-tabs-hide">

		<fieldset class="setting_fieldset">
        <legend>{t}THUMBNAIL{/t}</legend>
        <form id="form_thumbnail_conf"  action="secureadmin_.php?act=config&view=inline" method="post">
		  <table class="tabl_config" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}MINIATURETHUMB{/t}</strong><div class="desctext">{t}MINIATURETHUMB_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<input name="thumb_small_height" type="text" id="thumb_small_height" value="{$thumb_small_h}" size="3" /> X <input name="thumb_small_width" type="text" id="thumb_small_width" value="{$thumb_small_w}" size="3" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $thumb_small_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_thumbsmall=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			  </tr>

 			<tr>
			  <td style="width: 50%; padding-right: 35px;"> <strong>{t}NORMALTHUMB{/t}</strong><div class="desctext">{t}NORMALTHUMB_EXPLAIN{/t}</div></td>
			  <td style="width: 40%;">
					<input name="normal_small_height" type="text" id="normal_small_height" value="{$thumb_normal_h}" size="3" /> X <input name="normal_small_width" type="text" id="normal_small_width" value="{$thumb_normal_w}" size="3" />
			  </td>
                
                <td nowrap="true" style="width: 7%; text-align: right;">
                {if $thumb_normal_reset}<a title="{t}RESET_CONF{/t}" href="secureadmin_.php?act=config&view=inline&reset_thumb_normal=1" class="dropdown-button custom_target"><img  title="{t}RESET_CONF{/t}" src="{$adresse_images}/revert.png" width="16" height="16" /></a>{/if}</td>
  			 </tr>              

 			
                            
		  </table>

        
         
          
         
		<div class="settings_button">
			<input type="submit" class="realbutton" name="change_thumbnail" id="button" value="{t}CHANGE_SETTINGS{/t}" />
		</div>
        
          
          <br />
        </form>
        </fieldset>            
		
      
	</div>
    
    
</div>


	

{literal}
<script language="javascript">
	
	$(function() {
		$("#tabs").tabs({
			 select: function(event, ui) {  $.cookie("selected_tab", ui.index); },
			 selected: $.cookie("selected_tab")
		});
	});
	
	$('#form_authorisation').ajaxForm({ 
	success:   function() { 
		PM.loadingPage(url_config) }
	});

	function addMask_name() { 
		$('#loading_form_add_name').show();
		$('#mask_n_frm').ajaxSubmit({ 
		success:   function() { 
			PM.loadingPage(url_config) }
		});
	};
	function deleteMask_name() { 
		$('#loading_form_delete_name').show();
		$('#mask_n_glb_form').ajaxSubmit({ 
		success:   function() { 
			PM.loadingPage(url_config) }
		});
	};

	function addMask_ext() { 
		$('#loading_form_add_ext').show();
		$('#mask_e_frm').ajaxSubmit({ 
		success:   function() { 
			PM.loadingPage(url_config) }
		});
	};
	function deleteMask_ext() { 
		$('#loading_form_delete_ext').show();
		$('#mask_e_glb_form').ajaxSubmit({ 
		success:   function() { 
			PM.loadingPage(url_config) }
		});
	};


	
	$('#form_visualisation').ajaxForm({ 
	success:   function() { 
		PM.loadingPage(url_config) }
	});	
	$('#form_processing').ajaxForm({ 
	success:   function() { 
		PM.loadingPage(url_config) }
	});	

	$('#form_plugmedia_conf').ajaxForm({ 
	success:   function() { 
		PM.loadingPage(url_config) }
	});	

	$('#form_thumbnail_conf').ajaxForm({ 
	success:   function() { 
		PM.loadingPage(url_config) }
	});	
					
	url_config = "secureadmin_.php?act=config&view=inline";	
	
</script>

{/literal}




{include file="admin_innerfooter.tpl"}
