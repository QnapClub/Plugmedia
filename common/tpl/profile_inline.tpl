
<script language="javascript">
var plural_string = '{t}DIRECTORY{/t}';
var singular_string = '{t}DIRECTORIES{/t}';
var pre_string = '{t}DELETE{/t}';
</script>  
<script type="text/javascript" language="javascript" src="{$adresse_js}/administration.js"></script>



<!-- {* ---------  CENTER PART OF THE CONTENT OF THE DESIGN --------- *} -->
    <div id="inner_center_demo" class="ui-layout-center">
			<div class="ui-layout-content" id="ui_content_pm">


<div id="result"></div>

<div class="page_title">{t}PROFILE{/t}</div>
<br />
<form class="cmxform" id="profile_form" method="post" action="api.php?ac=editUser">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
		<p>
			<label for="login">{t}LOGIN{/t}</label>
			<input name="login" class="ui-widget-content" id="login" value="{$user_info.login}" disabled="disabled" />
		</p>
        <p>
			<label for="name">{t}NAME{/t}</label>
			<input name="name" class="ui-widget-content" id="name" value="{$user_info.name}" {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
		</p>
		<p>
			<label for="email">{t}EMAIL{/t}</label>
			<input id="email" name="email" class="ui-widget-content" value="{$user_info.email}" {if $user_info.login eq 'Guest'}disabled="disabled"{/if} />
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
			<button class="submit" type="submit">Submit</button> <button class="submit" type="reset" >Cancel</button>
		</p>
	</fieldset>
</form>




<div class="page_title">{t}AVATARS{/t}</div>
<br />
{foreach from=$avatar_list item=avatar key=k name=avatarls}<a href="profile.php?avatar={$avatar}" class="avatar_sel"><img src="system/common_style/avatar/48/{$avatar}" class="selectable {if $user_info.avatar eq $avatar}selected{/if}" /></a>{/foreach}
<br /><br /><br />
<div class="page_title">{t}WATCHEDDIR{/t}</div>
<br />



<form method="post" name="qnap_form" id="qnap_form">

{* TAB HEADER *}
<div class="box_title_div" style="border-right: 1px solid rgb(218, 218, 218);">
    <div style="display: inline; float: left; padding-top: 4px; padding-left: 4px;">
       &nbsp;
    </div>
</div>

{* TAB CONTENT *}
<div class="box_content">
    <table id="user_list_info" border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
    <tr>
        <td class="box_td_title" style="white-space: nowrap;" align="center" width="30">
        <input onclick="checkAllFieldsandGetSelect(1,'delfollow[]','delfollow','delete_btn');" class="checkuser" id="delfollow" type="checkbox">
        </td>
        <td class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}DIRECTORY{/t}</span></td>
        <td width="100" class="box_td_title" style="white-space: nowrap;"><span class="qnapstr">{t}NOTIFICATIONTYPE{/t}</span></td>
    </tr>
    </thead>
    <tbody>
{foreach from=$follower_list item=follow key=k name=followls}
	  <tr class="{cycle name="cycle" values="box_content_tr1,box_content_tr2"}">
		<td align="center"><input onclick="checkAllFieldsandGetSelect(2,'delfollow[]','delfollow','delete_btn');" class="checkuser"  name="delfollow[]" id="delfollow[]" type="checkbox" value="{$follow.id}" ></td>
          <td valign="middle">{$follow.parent}{$follow.name|default:''}</td>
		  <td align="center" valign="middle" class="notify_pic"><img  title="{t}IMMEDIATENOTIFY{/t}" class="selected" src="{$adresse_images}/notify_immediate.png" width="16" height="16" />
          <!--<a href="#"><img title="{t}DELAYEDNOTIFY{/t}" src="{$adresse_images}/notify_day.png" width="16" height="16" /></a><a href="#"><img src="{$adresse_images}/notify_summary.png" title="{t}WEEKLYDIGEST{/t}" width="16" height="16" /></a><a href="#" ><img src="{$adresse_images}/notify_stop.png" title="{t}SUSPENDEDNOTIFY{/t}" width="16" height="16" /></a>-->
          
          </td>
		</tr>

{/foreach}  
        </tbody>
    </table>

	{* TAB FOOTER *}
    <div class="box_end_div" style="margin-right: -2px;">
      <div id="btn_delete_r" style="display: inline; float: left; padding-left: 5px;">
            <a class="small_bt_del" href="javascript:document.qnap_form.submit();" onclick="this.blur();"><span class="qnapstr" id="delete_btn">{t}DELETE{/t}</span></a>
      </div>
    
      <div style="display: inline; float: right; padding-top: 1px; padding-left: 4px;">
        <span class="total_tab">{t 1=$smarty.foreach.followls.total}TOTALWATCHED{/t}</span>
      </div>
    </div>


</div>
</form>





{literal}    
<script type="text/javascript">
$(":submit").button();

$().ready(function() {
	var validator = $("#profile_form").validate({
		
		submitHandler: function(form) {
			jQuery(form).ajaxSubmit({
				type:  'POST',
				dataType: 'json',
				success:  function(data) {	
					 $.Growl.show({
						'title'  : "",
						'message': data.message,
						'icon'   : "success",

					 });
				}
			});
		},
		
		
		rules: {
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

	function process_profile_form(data) { 
		alert(data.message); 
	}


	var chgt_pass = $("#change_pass_chk");
	
	var inital = chgt_pass.is(":checked");
	var topics = $("#change_pass_subform")[inital ? "removeClass" : "addClass"]("hide_form");
	var topicInputs = topics.find("input").attr("disabled", !inital);
	// show when chgt_pass is checked
	chgt_pass.click(function() {
		topics[this.checked ? "removeClass" : "addClass"]("hide_form");
		topicInputs.attr("disabled", !this.checked);
	});
	
	$("#profile_form input:not(:submit)").addClass("ui-widget-content");
	
	$(":submit").button();
	
	$(":reset").button();

	$(":reset").click(function() {
		validator.resetForm();
		$('#change_pass_chk').attr('checked', false);
		$("#change_pass_subform").addClass("hide_form");
		$("#change_pass_subform").find("input").attr("disabled",true);
	});


});
</script>
{/literal}





	</div>
</div>


