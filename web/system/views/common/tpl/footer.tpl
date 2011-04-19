{* END mainContent *}
	</div> 
	{* ---------  SOUTH PART OF THE DESIGN (footer, copyright, loadtime)  --------- *}
    <div class="ui-layout-south">
		<p align="right">PlugMedi@ {$version_package} &#8226; hosted on Qnap <img title="{t val=$generated_time}GENERATEDIN{/t}" alt="{t val=$generated_time}GENERATEDIN{/t}" src="{$adresse_images}/time.png" />  <img title="{$number_request} {t}QUERY{/t}" alt="{$number_request} {t}QUERY{/t}" src="{$adresse_images}/database_lightning.png" />&nbsp;</p>
    </div>
    
	
 
{include file="modal_window_loading.tpl"}  
{include file="modal_window_loginform.tpl"} 
{include file="modal_window_search.tpl"}   
{include file="modal_window_help.tpl"}   

{*
<!--
{foreach from=$detail_request item=req}
{$req}\n
{/foreach}

-->     
*}
</body>
</html> 