{include file="admin_innerheader.tpl"}
<!-- {* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} -->


<script language="javascript" src="{$adresse_js}/jquery/jquery.multiselect.js"></script>           



<div class="page_title">{t}ADDUSER{/t}</div>
<br />

<form class="cmxform" id="profile_form" method="post" action="api.php?ac=admin_createUser">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
		<p>
			<label for="login">{t}LOGIN{/t} *</label>
			<input name="login" class="ui-widget-content" id="login"  />
		</p>
        <p>
			<label for="name">{t}NAME{/t} *</label>
			<input name="name" class="ui-widget-content" id="name"  {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
		</p>
		<p>
			<label for="email">{t}EMAIL{/t}</label>
			<input id="email" name="email" class="ui-widget-content" {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
		</p>
		<p>
			<label for="email">{t}LANGUAGE{/t} *</label>
			<select name="lang_value">
			  {foreach from=$available_lang key=shortlang item=longlang}
              <option value="{$shortlang}">{$longlang|convert_utf8}</option>
              {/foreach}
		    </select>
		</p>
		<p>
			<label for="pass">{t}PASSWORD{/t} *</label>
			<input type="password" class="ui-widget-content" id="pass" name="pass" /> <button class="submit" type="button" id="generate_pass">{t}GENERATE{/t}</button>
		</p>
        <p>
           	 <label for="pass_cfrm">{t}CHECKPASSWD{/t} *</label>
			 <input name="pass_cfrm" type="password" class="ui-widget-content" id="pass_cfrm" /> 
        </p>
        <p>
            <label for="send_email">{t}SEND_WELCOME_EMAIL{/t}</label>
			<input id="send_email" type="checkbox" name="send_email" class="ui-widget-content" />
         </p>           
        <p>
            <label for="can_read_com">{t}CANREADCOMMENT{/t}</label>
			<input id="can_read_com" type="checkbox" name="can_read_com" class="ui-widget-content" />
         </p>  
        <p>
            <label for="can_post_com">{t}CANADDCOMMENT{/t}</label>
			<input id="can_post_com" type="checkbox" name="can_post_com" class="ui-widget-content" />
         </p>  
        <p>
            <label for="can_manage_mtd">{t}CANMANAGEMETADATA{/t}</label>
			<input id="can_manage_mtd" type="checkbox" name="can_manage_mtd" class="ui-widget-content" />
         </p>



        <p>
              <label for="change_pass">{t}GROUPMEMBERSHIP{/t}</label>
              <select id="group_member" class="multiselect" multiple="multiple" name="group_member[]" size="5" style="width:400px">
                      {foreach from=$list_grp item=grp}
                      <option value="{$grp.id}">{$grp.name|convert_utf8}</option>
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




function resetForm()
{
	$('#profile_form').get(0).reset();
	multi.multiselect("update");
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
			pass_cfrm: {
				required: true,
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