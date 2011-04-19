<!-- {* ---------  CENTER PART OF THE MAIN CONTENT OF THE DESIGN --------- *} -->
<div class="ui-layout-north">
	<div class="header">
    	<div id="breadCrumb0" class="breadCrumb module">
		{include file="breadcrumb.tpl"}
		</div>
       
       <div id="other_menu" style="position: absolute; right: 2px;">
       
        <span id="toolbar">
    {if $loggedin}
        <button id="info_mbr">{$user_info.name} ({$user_info.user})</button>
           
        <button id="logout" title="{t}LOGOUT{/t}">{t}LOGOUT{/t}</button>
    {else}             
        <button id="alone" titl>{t}CONNEXION{/t}</button>
    {/if}           
        <button id="select">|</button>
        <button id="help" title="{t}HELP{/t}">{t}HELP{/t}</button>
        <button id="search_btn" title="SEARCH">SEARCH</button>
            <ul class="lang_menu">
            	{foreach from=$available_lang key=shortlang item=longlang}
                <li><a href="index.php?lang={$shortlang}"><img class="x-menu-item-icon flag_{$shortlang}" src="s.gif"> {$longlang|convert_utf8} </a></li>
                {/foreach}
            </ul>
	{if $loggedin}
		<ul class="info_mbr_menu">
            <li><a class="custom_target" href="profile.php?view=inline"><img class="x-menu-item-icon icon_profile" src="s.gif"> {t}PROFILE{/t} </a></li>
            {if $user_info.admin}
            <li><a class="custom_target" href="secureadmin_.php?act=summary&view=inline"><img class="x-menu-item-icon icon_administration" src="s.gif"> {t}ADMINISTRATION{/t} </a></li>
            {/if}
        </ul>      
    {/if}
        </span>

		</div>
	</div>
</div>