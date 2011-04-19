<div id="wraper_button">

{if isset($btn_thumb) && $btn_thumb eq "1"}<a href="{$current_file}view=inline&defaultview=thumb" id="button_thumb" ref="{t}DISPTHUMVIEW{/t}" class="custom_target">&nbsp;</a>{/if}
{if isset($btn_thumb_list) && $btn_thumb_list eq "1"}<a href="{$current_file}view=inline&defaultview=thumb_list" id="button_thumb_list" ref="{t}DISPALTTHUMVIEW{/t}" class="custom_target">&nbsp;</a>{/if}
{if isset($btn_list) && $btn_list eq "1"}<a href="{$current_file}view=inline&defaultview=list" id="button_list" ref="{t}DISPLISTVIEW{/t}" class="custom_target">&nbsp;</a>{/if}
{if isset($btn_follow) && $btn_follow eq "1"}<a href="#" id="button_watchdir" ref="{t}WATCHDIR{/t}" value="dir_id={$smarty.get.dir}&ref={$smarty.get.ref}">&nbsp;</a>{/if}
{if isset($btn_follow) && $btn_follow eq "2"}<a href="#" id="button_watchdir_subscribe" ref="{t}WATCHDIR{/t}" value="dir_id={$smarty.get.dir}&ref={$smarty.get.ref}">&nbsp;</a>{/if}
{if isset($btn_radio) && $btn_radio eq "1"}<a href="#" id="button_radio" ref="{t}LAUNCHRADIO{/t}" value="api.php?ac=getPlsFile&dir={$smarty.get.dir}">&nbsp;</a>{/if}
{if isset($btn_slideshow) && $btn_slideshow eq "1"}<a href="#" id="button_slideshow" ref="{t}LAUNCHSLIDESHOW{/t}" value="slideshow.php?dir={$smarty.get.dir}">&nbsp;</a>{/if}
{if isset($btn_cooliris) && $btn_cooliris eq "1"}<a href="#" id="button_cooliris" ref="{t}STARTCO0LIRIS{/t}" value="{$cooliris_value}">&nbsp;</a>{/if}


</div>

<br clear="all" />

<div id="ac_bar">
    <div id="acb_l">
    	{if isset($display_left) && $display_left eq "info_subfolder"}
        	{t}TOTALSOUSALBUM{/t}: {$current_dir.total_dir}, {t}TOTALFICHIER{/t}: {$current_dir.total_file}
        {else if isset($display_left) && $display_left eq "info_file"}
        	{if isset($current_media)} {$current_media.short_name} {/if}
        {/if}
    </div>
    <div id="acb_r">
    	{if isset($display_right) && $display_right eq "no"}
        {else}
    	<form action="" method="post">
        {t}TRIER_PAR{/t}
        <select name="tris">
                    <option value="N" {if $smarty.session.tris eq "N"}selected="selected"{/if}>{t}NOMFICHIER{/t}</option>
                    <option value="D" {if $smarty.session.tris eq "D"}selected="selected"{/if}>{t}DATEPRISEVUE{/t}</option>
                    <option value="S" {if $smarty.session.tris eq "S"}selected="selected"{/if}>{t}TAILLE{/t}</option>
                    <option value="M" {if $smarty.session.tris eq "M"}selected="selected"{/if}>{t}MIX{/t}</option>
          </select> 
                {t}ORDRE_TRIS{/t}
                <select name="order" id="order">
                    <option value="ASC" {if $smarty.session.order eq "ASC"}selected="selected"{/if}>{t}ASCENDANT{/t}</option>
                    <option value="DESC" {if $smarty.session.order eq "DESC"}selected="selected"{/if}>{t}DESCENDANT{/t}</option>
                </select>
                <input type="submit" name="button" id="button" value="{t}APPLIQUER{/t}" />
      	</form>
        {/if}
    </div>

</div>