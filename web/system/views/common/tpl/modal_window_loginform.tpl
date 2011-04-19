<div id="basic-modal-content">
    <form  id="login_form" action="api.php?ac=login&type=json" method="post">
		<fieldset>
			<legend>{t}LOGIN{/t}</legend>
                <p>
                    <label for="login">{t}LOGIN{/t} *</label>
                    <input name="login" class="ui-widget-content" id="login" />
                </p>
				<p>
           	 		<label for="password">{t}PASSWORD{/t}</label>
			   	<input name="password" type="password" class="ui-widget-content" id="password" />
            	</p>
		</fieldset>
		<div align="center"><input name="login_submit" type="submit" value="{t}OK{/t}" class="button_form" /><input name="login_reset" type="reset" value="{t}CANCEL{/t}" class="button_form" /></div>		       
	</form> 
    {* LOADING PICTURE WHEN SUBMIT THE FORM*}
    <div id="login_loading" style="margin-top:10px;display:none;padding: 8px;position: absolute; top: 180px; left: 1px;"><img src="{$adresse_images}/4-1.gif" /></div>
    {* DISPLAY THE RESULT ON SUBMIT (ERROR OR SUCCESS) *}
    <div id="result" class="ui-state-error  ui-corner-all" style="margin-top:10px;display:none;padding: 8px"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><p></p></div>
</div>