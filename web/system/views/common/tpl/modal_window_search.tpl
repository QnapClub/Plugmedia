<div id="search-modal-content" style="display:none;">

    <form  id="search_form" action="search.php?view=inline" method="post">
		<fieldset>
			<legend>SEARCH{t}SEARCH{/t}</legend>
                <p>
                    <label for="text"> Search string:</label>
                    <input name="text" class="ui-widget-content" id="text" />
                </p>
				<p>
           	 		
			   	    <div id="search_format" style="font-size:10px;text-align:center;">
                        <input disabled="disabled" name="search_tag" type="checkbox" id="check1" /><label for="check1">Tag</label>
                        <input name="search_filename" type="checkbox" id="check2" /><label for="check2">Filename</label>
                        <input name="search_title" type="checkbox" id="check3" /><label for="check3">Title</label>
                        <input name="search_description" type="checkbox" id="check4" /><label for="check4">Description</label>    
    				</div>
            	</p>
		</fieldset>
		<div align="center"><input name="search_submit" type="submit" value="{t}OK{/t}" class="button_form" /><input name="search_reset" type="reset" value="{t}CANCEL{/t}" class="button_form" /></div>		       
	</form> 

<div id="search_loading" style="margin-top:10px;display:none;padding: 8px;position: absolute; top: 180px; left: 1px;"><img src="{$adresse_images}/4-1.gif" /></div>

</div> 