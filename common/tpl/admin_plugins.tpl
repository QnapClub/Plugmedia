{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 

<div class="page_title">{t}PLUGINS{/t}</div>
<br />





{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
        {t}ENABLEDPLUGIN{/t}
    </div>

<div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        &nbsp;
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%" style="overflow: hidden; table-layout: fixed;">
 
    <thead>
    <tr>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}PLUGINNAME{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="200px"><span class="qnapstr">{t}AUTHOR{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}INSTALL_PATH{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="50px"><span class="qnapstr">{t}VERSION{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="50px"><span class="qnapstr">{t}URL{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}LICENSE{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="400px"><span class="qnapstr">{t}DESCRIPTION{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="20px"></td>
    
    </tr>
    </thead>
    <tbody>
{foreach from=$listplugin item=plugin key=k name=plug_l}
  <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
            <td><strong>{$plugin.name}</strong></td>
            <td>{$plugin.author} <a href="{$plugin.authorurl}" target="_blank"><img src="{$adresse_images}/link.png" title="{t}AUTHORWEBSITE{/t}" /></a></td>
            <td>plugins/{$plugin.filename}/</td>
            <td>{$plugin.version}</td>
    		<td><a href="{$plugin.url}" target="_blank"><img src="{$adresse_images}/house.png" /></a></td>
   			<td>{$plugin.license}</td>
             <td>{$plugin.description}</td>    
             <td align="center"><a class="custom_target" href="secureadmin_.php?act=plugins&pl=disable&id_p={$plugin.id}&view=inline" ><img src="{$adresse_images}/package_delete.png" width="16" height="16" title="{t}DISABLEPLUGIN{/t}" /></a></td>        
             </tr>
{/foreach}  
        </tbody>
    </table>

	{* TAB FOOTER *}
    <div class="box_end_div" style="margin-right: -2px;">
      <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
           &nbsp;
      </div>
    
      <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <span class="total_tab">{t 1=$smarty.foreach.plug_l.total}TOTALUSR{/t}</span>
      </div>
    </div>


</div>

<br />


{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
        {t}DISABLEDPLUGIN{/t}
    </div>

<div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        &nbsp;
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%" style="overflow: hidden; table-layout: fixed;">
 
    <thead>
    <tr>
    
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}PLUGINNAME{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="200px"><span class="qnapstr">{t}AUTHOR{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}INSTALL_PATH{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="50px"><span class="qnapstr">{t}VERSION{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="50px"><span class="qnapstr">{t}URL{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}LICENSE{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="400px"><span class="qnapstr">{t}DESCRIPTION{/t}</span></td>
    <td class="box_td_title" style="white-space: nowrap;" width="20px"></td>
    
    </tr>
    </thead>
    <tbody>
{foreach from=$listplugindisabled item=plugin_d key=k name=plug_d}
  <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
            <td><strong>{$plugin_d.name}</strong></td>
			<td>{$plugin_d.author} <a href="{$plugin_d.authorurl}" target="_blank"><img src="{$adresse_images}/link.png" title="{t}AUTHORWEBSITE{/t}" /></a></td>            
            <td>plugins/{$plugin_d.filename}/</td>
            <td>{$plugin_d.version}</td>
    		<td><a href="{$plugin_d.url}" target="_blank"><img src="{$adresse_images}/house.png" /></a></td>
   			<td>{$plugin_d.license}</td>
             <td>{$plugin_d.description}</td>
             <td align="center"><a class="custom_target" href="secureadmin_.php?act=plugins&pl=enable&id_p={$plugin_d.id}&view=inline" ><img src="{$adresse_images}/package_add.png" width="16" height="16" title="{t}ENABLEPLUGIN{/t}" /></a></td>           
             </tr>
{/foreach}  
        </tbody>
    </table>

	{* TAB FOOTER *}
    <div class="box_end_div" style="margin-right: -2px;">
      <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
           &nbsp;
      </div>
    
      <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <span class="total_tab">{t 1=$smarty.foreach.plug_d.total}TOTALUSR{/t}</span>
      </div>
    </div>


</div>



<div id="information_message">{t}PLUGINSINSTALLINFO{/t}</div>

{include file="admin_innerfooter.tpl"}