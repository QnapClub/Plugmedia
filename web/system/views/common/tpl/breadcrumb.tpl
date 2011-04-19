<ul>
	<li>
    	<a href="index.php" title="{t}GOBACKHOME{/t}"><img src="{$adresse_images}/icon_home_12.gif" /> {t}ACCUEIL{/t}</a>
	</li>
	{if isset($trail)}
	{foreach from=$trail item=tt name=trailer}
    	{if $tt.link neq ''}
    	<li><a class="custom_target" href="{$tt.link}">{$tt.reference}</a></li>
        {else}
        <li>{$tt.reference|convert_utf8}</li>
        {/if}
	{/foreach}
	{/if}
</ul>