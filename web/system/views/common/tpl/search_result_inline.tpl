<!-- {* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} -->
    <div id="inner_center_demo" class="ui-layout-center">
			<div class="ui-layout-content" id="ui_content_pm">

  	
            
            
 <div id="search_tabs">
	<ul>
		<li><a href="#tabs_result">Search result</a></li>

	</ul>
	<div id="tabs_result">
		
			{foreach from=$search_result item=media}
            <div class="dir" style="clear:both"> 
                <div class="search_tab_link"><a href="display.php?dir={$media.directory_id}&file={$media.id}&ref={$smarty.get.ref}&view=inline&nojson=true" class="search_inline" name="{$media.filename}" ><img src="{$adresse_images}/search/tab_add.png" alt="Open in new tab" /></a></div>
                <div class="search_link"><a href="display.php?dir={$media.directory_id}&file={$media.id}&ref={$smarty.get.ref}&view=inline" class="custom_target" name="{$media.filename}" target="_blank" ><img src="{$adresse_images}/search/page_link.png" alt="Open in this page" /></a></div>
                <div class="name">
                   {$media.smart_name_highlighted} ({$media.filename_highlighted})<br />{$media.smart_description_highlighted} 
              </div>
            </div>
            {foreachelse}
               
                <div id="no_albums">No result.</div>
               
            {/foreach}           
        
        
	</div>
	
</div>
           
                    
            
            <br clear="all" />


	<script>
	$(function() {
		var $tabs = $( "#search_tabs").tabs({
			 {literal} tabTemplate: "<li><a href='#{href}'>#{label} {/literal} </a> <span class='search_wait'><img src='{$adresse_images}/treeview-loading.gif' /></span><span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
			add: function(event, ui) {
				$tabs.tabs('select', '#' + ui.panel.id);
			},
			 load: function(event, ui) { 
            	
				$('#left_link').remove();
				$('#walk_elements').remove();
				$('#right_link').remove();
				$('#top_info').remove();
        	}			
			
			
		});

		$('.search_inline').live('click', function() {
			$tabs.tabs( "add", $(this).attr("href"), $(this).attr("name") );
			
			return false; // avoid redirect
		});		  
	  
		
	   
		$( "#search_tabs span.ui-icon-close" ).live( "click", function() {
			var index = $( "li", $tabs ).index( $( this ).parent() );
			$tabs.tabs( "remove", index );
		});
	
		
	});
	</script>



		

			</div>
</div>