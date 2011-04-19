{* COMMENT *}
<div class="comment_part" id="top_info">
    <span class="corners-top">
    <span><img src="{$adresse_images}/corner_tr.gif" alt="" width="13" height="11" /></span>
    <img src="{$adresse_images}/corner_tl.gif" alt="" width="13" height="11" />
    </span>
	<div class="commentbody">
	{if $current_media.readable_type|lower eq 'jpg'}<a class="button btn_exif" id="show-btn_exif" href="#" title="{t}EXIFINFO{/t}"><span>EXIF</span></a> {/if}
    {if $can_download_file}<a class="button btn_download" href="api.php?ac=getFileContent&file={$current_media.file_id}&dwl=1" target="_blank" ><span>{t}DOWNLOAD{/t}</span></a>{/if}
	

    <h1 id="title_ed">{if $current_media.smart_name}{$current_media.smart_name}{else}{t}PASTITRE{/t}{/if}</h1>
    <div id="description_ed">{if $current_media.smart_description}{$current_media.smart_description|nl2br}{else}{t}PASDESCRIPTION{/t}{/if}</div>
 
{if $can_manage_metadata}
<br /><br />



<script language="javascript">
    $(document).ready(function() {


		
		$('#title_ed').dblclick(function () {
			$(this).find("img:last").remove();
		  });
		$('#description_ed').dblclick(function () {
			$(this).find("img:last").remove();
		  });
		  		  
		$('#title_ed').hover(
		  function () {
			$('#title_ed:not(:has(form))').append($("<img src='{$adresse_images}/pencil.png'>"));
		  }, 
		  function () {
			$('#title_ed:not(:has(form))').find("img:last").remove();
		  }
		);
		

		$('#description_ed').hover(
		  function () {
			$('#description_ed:not(:has(form))').append($("<img src='{$adresse_images}/pencil.png'>"));
		  }, 
		  function () {
			$('#description_ed:not(:has(form))').find("img:last").remove();
		  }
		);
		
	  	$("#title_ed").editable("api.php?ac=editTitle&id={$current_media.file_id}", { 
			  indicator : "<img src='{$adresse_images}/large-loading.gif'>",
			  select : true,
			  tooltip   : '{t}DOUBLECLICKEDIT{/t}',
			  event     : "dblclick",
			  submit : '{t}OK{/t}',
			  cancel : '{t}CANCEL{/t}',
			  cssclass : "editable"
	  	});
		$("#description_ed").editable("api.php?ac=editDescription&id={$current_media.file_id}", { 
			  indicator : "<img src='{$adresse_images}/large-loading.gif'>",
			  type   : 'textarea',
			  rows : 5,
			  select : true,
			  tooltip   : '{t}DOUBLECLICKEDIT{/t}',
			  event     : "dblclick",
			  submit : '{t}OK{/t}',
			  cancel : '{t}CANCEL{/t}',
			  cssclass : "editable"
	 	});
		

		
		

	
        $("#Tags").tagEditor(
        {
            items: [ {$tag_list} ],
            confirmRemoval: true,
			confirmRemovalText: '{t}REMOVETAGCONFIRM{/t}',
			titleRemovalText: '{t}REMOVETAGTITLE{/t}',
			cssPrefix:'tagitem',
			deletable:true,
			imageTag:true,
			imageTagUrl:"{$adresse_images}/tags/minus_small.png",
			file_id:'{$current_media.file_id}'
        });
    });

</script>
<div id="tag_input">Tags: <input id="Tags" name="Tags" type="text" value="" /></div>
{else}
    <ul class="tagEditor">
    {foreach $tag_list_array as $tag}
    <li class="tagitem_{0|rand:7}">{$tag.value}</li>
    {/foreach}
    </ul>
{/if}   
 
 
{if $can_read_comment}    
    </div>
	<div class="commentbody cbox">
		<div class="comment_round"><span class="cboxtxt">{t}COMMENTS{/t}</span></div>
    </div>
    <div id="comment_list_json">
  <div id="loading_form" style="position:absolute; display: inline; float: left; padding-top: 1px; padding-left: 4px;">
			<img width=20 src="{$adresse_images}/large-loading.gif" />
      	</div>     
	</div>   
{if $can_add_comment}   
<div class="box_add_comment">
	<div id="error_div"></div>
      <form action="api.php?ac=addcomment" method="get" id="submit_comment">
    <span class="oneField">
    <label class="preField">{t}NAME{/t}:</label>
        <input name="name" type="text" id="name" value="{if isset($smarty.post.name)}{$smarty.post.name|default:$user_info.name}{/if}" class="comment_format" />
    </span>
    <span class="oneField">
        <label class="preField">{t}EMAIL{/t}:</label>
        <input name="email" type="text" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email|default:$user_info.email}{/if}" class="comment_format" />
    </span>  
    <span class="oneField">
        <label class="preField">{t}COMMENT{/t}: </label>
        <textarea name="comment" cols="70" rows="4" id="comment" class="comment_format">{if isset($smarty.post.comment)}{$smarty.post.comment}{/if}</textarea>
    </span>   
    <span class="oneField">
        <label class="preField">&nbsp;</label>
        {t}PLEASEENTERVERIFCODE{/t}
    </span>  
    <span class="oneField">
        <label class="preField">&nbsp;</label>
		<img id="turing_code" src='api.php?ac=getimg_turing&rand={$time}' /> <input name="securite" type="text" id="securite" />
    </span> 
    <span class="oneField">
        <label class="preField">&nbsp;</label>
        <input name="submit" type="submit" value="{t}OK{/t}" /><input name="" type="reset" value="{t}CANCEL{/t}" />
    </span>
	<input name="file_id" type="hidden" value="{$current_media.file_id}" />      
      </form>
</div>    
<span class="corners-bottom">
<span><img src="{$adresse_images}/corner_br.gif" alt="" width="13" height="11" /></span>
<img src="{$adresse_images}/corner_bl.gif" alt="" width="13" height="11" />
</span>
{/if} 
   
</div>
{/if}

{literal}
<script type="text/javascript">
 $(document).ready(function() { 
    var options = { 
        success:       showResponse,  // post-submit callback 
       
  		dataType:  'json' 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
 
    // bind to the form's submit event 
    $('#submit_comment').submit(function() { 
        $(this).ajaxSubmit(options); 
        return false; 
    });
	
	function showResponse(responseText, statusText, xhr, $form)  { 
		if (responseText.success)
		{
			 $('#error_div').hide();
			 var div = $("<div>").appendTo("#comment_list_json"); 
			 div.hide();
			 div.append(responseText.message).fadeIn('slow');
			 $('#submit_comment').clearForm();
		}
		else
		{	
			$('#error_div').hide().html('<div id="error_message"><strong>'+responseText.message+'</strong></div>').fadeIn('slow');
			$("#inner_center_demo").attr({ scrollTop: $("#inner_center_demo").attr("scrollHeight") });
		}
		$('#turing_code').attr("src","api.php?ac=getimg_turing&rand="+new Date().getTime());
	}

    //retrieve comments to display on page
	$.ajax({
	  url: "api.php?ac=getcomment&file_id={/literal}{$current_media.file_id}{literal}&jsoncallback=?",
  	  cache: false,
	  dataType: "html",
	  success: function(html){
		 $("#comment_list_json").hide().append(html).fadeIn('slow');
		 $("#loading_form").fadeOut();
		}	

	});


	 
}); 


  
</script>
{/literal}