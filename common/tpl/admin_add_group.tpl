{include file="admin_innerheader.tpl"}

{* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} 


<div class="page_title">{t}ADDGROUP{/t}</div>


{$error}
<form action="api.php?ac=admin_createGroup" class="cmxform" id="profile_form" method="post">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
		<p>
			<label for="groupname">{t}GROUPNAME{/t}</label>
			<input name="groupname" class="ui-widget-content" id="groupname"  />
		</p>
		<p>
			<label for="user_group">{t}USERS{/t}</label>
              <select id="user_group" class="multiselect" multiple="multiple" name="user_group[]" size="5" style="width:400px">
                      {foreach from=$userlist key=k item=user}
                      <option value="{$user.login}">{$user.login}</option>
                      {/foreach}
              </select>
		</p>
		<p>
			<label for="selectX">{t}ACCESSPATH{/t}</label>
            <input name="selectX" id="list_directory" type="hidden" />
             <div id="add_group_tree"></div> 
		</p>

 		<p>
			<button class="submit" type="submit" id="submit_btn">Submit</button><button class="submit" type="reset" value="Cancel">Cancel</button>
		</p>
	</fieldset>
</form>





<br />
<br />
  <br />
  <br />


{literal}    
<script type="text/javascript">





var multi = $("#user_group").multiselect({
   selectedList: 4 // 0-based index
}); 

$("#submit_btn").button();

$(":reset").button().click(function() {
		resetForm();
});

function resetForm()
{
	$('#profile_form').get(0).reset();
	multi.multiselect("update");
	validator.resetForm();
	$("#add_group_tree").jstree("uncheck_all");	
}



	$("#add_group_tree").jstree({ 
			"plugins" : [ "themes", "json_data", "ui", "types" , "checkbox" ],
			"checkbox" : { "two_state" : true },
			"themes" : {
			            "theme" : "classic",
						"url" : PM_config.url_css+"/tree_style.css",
				            "dots" : true
				        },
			"json_data" : { 
				"ajax" : {
					"url" : "./api.php?ac=getdir&disp=json",
					"type": "POST",
					"data" : function (n) { 
						return { 
							"node" : n.attr ? n.attr("id").replace("node_","") : "getroot_node" 
						}; 
					}

				}
			},

			"types" : {
				"max_depth" : -2,
				"max_children" : -2,
				"valid_children" : [ "drive" ],
				"types" : {
					"default" : {
						"valid_children" : "none",
						"icon" : {
							"image" : PM_config.url_img+"/tree/file.png"
						}
					},
					"folder" : {
						"valid_children" : [ "default", "folder" ],
						"icon" : {
							"image" : PM_config.url_img+"/tree/folder.gif"
						}
					},
					"drive" : {
						"valid_children" : [ "default", "folder" ],
						"icon" : {
							"image" : PM_config.url_img+"/tree/server.png"
						},
						"start_drag" : false,
						"move_node" : false,
						"delete_node" : false,
						"remove" : false
					}
				}
			},
			"core" : { 
				"animation" : 200,
				/*"initially_open" : [ "627", "640", "1622" ]*/
			}
		})
		

	var validator = $("#profile_form").validate({
		
		submitHandler: function(form) {
			
			// transform checked elements
			var checked_ids = [];
			$("#add_group_tree").jstree("get_checked").each(function (key,value) {
			   //alert($(value).attr("id"));
				checked_ids.push($(value).attr("id"));
			});  
			$("#list_directory").val(checked_ids.join(",")); 			
			
			
			$('#submit_btn').button( "option", "icons", {primary:'icon_wait'} );
			$('#submit_btn').button( "disable" );
			jQuery(form).ajaxSubmit({
				type:  'POST',
				dataType: 'json',
				success:  function(data) {	
					 if (data && data.success)
					 {
						icon = 'success';
						resetForm();
					 }
					 else
					 	icon='error';
					 $('#submit_btn').button( "enable" );
					 $('#submit_btn').button( "option", "icons", false );
					 PM.growlNotification(icon,data.message, '');
					 
				}
			});
		},
		
		
		rules: {
			name: {
				required: true,
				minlength: 2
			}
			
		},
		messages: {
			name: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters"
			}
		}
		
		
		
		
		
		
		
	});

 

$(document).ready(function()
{

   
 	/*$("#add_group_tree").bind("open_node.jstree", function(e) {
                $('#add_group_tree').jstree("check_node","#1622");
            }); 
*/
	/*$("#submit_btn").button().click(function() {
		
			

		
	});*/

	
})


	

/*var checked_ids = [];
$.tree.plugins.checkbox.get_checked($.tree.reference("#demo_1")).each
(function () {
  checked_ids.push(this.id);
});

$("#my-hidden-input-field").val(checked_ids.join(",")); 
*/
</script>
{/literal}    


	
{include file="admin_innerfooter.tpl"}
