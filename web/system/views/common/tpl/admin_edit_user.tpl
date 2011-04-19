{include file="admin_innerheader.tpl"}

<!-- {* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} -->

<script language="javascript" src="{$adresse_js}/jquery/jquery.multiselect.js"></script>           


<div class="page_title">{t val=$user_info.login}EDITUSER{/t}</div>
<br />


<form class="cmxform" id="profile_form" method="post" action="api.php?ac=admin_editUser">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
		
        
        <p>
			<label for="login">{t}LOGIN{/t} *</label>
            <input name="id" type="hidden" value="{$user_info.id}" />
			<input name="login" class="ui-widget-content" id="login" value="{$user_info.login}" disabled="disabled"  />
		</p>
        <p>
			<label for="name">{t}NAME{/t} *</label>
			<input name="name" class="ui-widget-content" id="name" value="{$user_info.name}"  {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
		</p>
		<p>
			<label for="email">{t}EMAIL{/t}</label>
			<input id="email" name="email" class="ui-widget-content" value="{$user_info.email}" {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
		</p>
		<p>
			<label for="email">{t}LANGUAGE{/t} *</label>
			<select name="lang_value">
			  {foreach from=$available_lang key=shortlang item=longlang}
              <option value="{$shortlang}" {if $user_info.lang eq $shortlang}selected="selected"{/if}>{$longlang|convert_utf8}</option>
              {/foreach}
		    </select>
		</p>
		
		<p>
			<label for="change_pass_chk">{t}CHANGEPASSWORD{/t}</label>
			<input type="checkbox" class="checkbox" id="change_pass_chk" name="change_pass_chk" {if $user_info.embeded eq 0}disabled="disabled"{/if} />
		</p>
        <div id="change_pass_subform">
        	<p>
           	 <label for="pass">{t}PASSWORD{/t}</label>
			   <input name="pass" type="password" class="ui-widget-content" id="pass" />
            </p>
            <p>
            	<label for="change_pass">{t}CHECKPASSWD{/t}</label>
				<input id="change_pass" type="password" name="change_pass" class="ui-widget-content" />
            </p>           
        </div>         
        
        
  
        <p>
            <label for="can_read_com">{t}CANREADCOMMENT{/t}</label>
			<input id="can_read_com" type="checkbox" name="can_read_com" {if $user_info.can_read_comment eq 1}checked="checked"{/if}  class="ui-widget-content" />
         </p>  
        <p>
            <label for="can_post_com">{t}CANADDCOMMENT{/t}</label>
			<input id="can_post_com" type="checkbox" name="can_post_com" {if $user_info.can_add_comment eq 1}checked="checked"{/if} class="ui-widget-content" />
         </p>  
        <p>
            <label for="can_manage_mtd">{t}CANMANAGEMETADATA{/t}</label>
			<input id="can_manage_mtd" type="checkbox" name="can_manage_mtd" {if $user_info.can_manage_metadata eq 1}checked="checked"{/if} class="ui-widget-content" />
         </p>



        <p>
              <label for="change_pass">{t}GROUPMEMBERSHIP{/t}</label>
              <select id="group_member" class="multiselect" multiple="multiple" name="group_member[]" size="5" style="width:400px">
                      {foreach from=$list_grp item=grp}
                      <option value="{$grp.id}" {if $grp.checked}selected="selected"{/if}>{$grp.name|convert_utf8}</option>
                      {/foreach}
              </select>
        
        </p>

	  <p>
			<button class="submit" type="submit" id="submit_btn">Submit</button><button class="submit" type="reset" value="Cancel">Cancel</button>
		</p>
	</fieldset>
</form>
{literal}    
<script type="text/javascript">

$().ready(function() {

$(":submit").button();




$(":reset").button().click(function() {
	resetForm();	
});
$("#generate_pass").button().click(function() {
	keylist="abcdefghijklmnopqrstuvwxyz123456789";
	temp='';
	for (i=0;i<8;i++)
		temp+=keylist.charAt(Math.floor(Math.random()*keylist.length));
	$('#pass').val(temp);
	$('#pass_cfrm').val(temp);
});

var multi = $("#group_member").multiselect({
   selectedList: 4 // 0-based index
}); 

var chgt_pass = $("#change_pass_chk");
	
var inital = chgt_pass.is(":checked");
var topics = $("#change_pass_subform")[inital ? "removeClass" : "addClass"]("hide_form");
var topicInputs = topics.find("input").attr("disabled", !inital);
// show when chgt_pass is checked
chgt_pass.click(function() {
	topics[this.checked ? "removeClass" : "addClass"]("hide_form");
	topicInputs.attr("disabled", !this.checked);
});

function resetForm()
{
	$('#profile_form').get(0).reset();
	multi.multiselect("update");
	$('#change_pass_chk').attr('checked', false);
	$("#change_pass_subform").addClass("hide_form");
	$("#change_pass_subform").find("input").attr("disabled",true);
	validator.resetForm();	
}








	



	var validator = $("#profile_form").validate({
		
		submitHandler: function(form) {
			$('#submit_btn').button( "option", "icons", {primary:'icon_wait'} );
			$('#submit_btn').button( "disable" );
			jQuery(form).ajaxSubmit({
				type:  'POST',
				dataType: 'json',
				success:  function(data) {	
					 if (data && data.success)
					 {
						icon = 'success';
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
			login: {
				required: true,
				minlength: 2
			},
			name: {
				required: true,
				minlength: 2
			},
			email: {
				required: false,
				email: true
			},
			pass: {
				required: true,
				minlength: 5
			},
			change_pass: {
				required: "#change_pass_chk:checked",
				minlength: 5,
				equalTo: "#pass"
			},
			
		},
		messages: {
			name: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters"
			},
			pass: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			change_pass: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			email: "Please enter a valid email address"
		}
		
		
		
		
		
		
		
	});

	



	
	$("#profile_form input:not(:submit)").addClass("ui-widget-content");
	

	





});




</script>
{/literal}

{include file="admin_innerfooter.tpl"}