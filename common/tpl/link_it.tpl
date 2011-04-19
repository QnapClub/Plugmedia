{literal}
<style type="text/css">
#d_clip_button_forum,#d_clip_button_blog,#d_clip_button_single
{
	float:right;
	background-image:url(system/views/common/img/page_paste.png);
	background-repeat:no-repeat;
	font-size:0.8em;
	color:#CCCCCC;
	padding:2px;
	padding-top:0;
	padding-left:20px;
}
#linkit_content
{
	color:#FFFFFF;
}
h3
{
	margin:0;
	padding:0;
	color:#FFFFFF;
	float:left;
	font-size:1.2em;
	margin-bottom:4px;
}
input
{
	margin-bottom:10px;
}
hr {
	border-top-width: 1px;
	border-top-style: solid;
	border-top-color: #FFFFFF;
	margin:10px 10px;
}

</style>
{/literal}
<div id="linkit_content">
<script type="text/javascript" src="system/views/common/js/ZeroClipboard.js"></script>
    
<h3>{t}FORUMLINK{/t}</h3><div id="d_clip_button_forum">{t}COPYTOCLIPBOARD{/t}</div>
<input type="text" value="[img]http://{$server_name}/plugmedia/api.php?ac=rotatePic&pic={$link_pic}&percent=1[/img]" id="link_forum" size="80" />
<hr>

<h3>{t}BLOGLINK{/t}</h3><div id="d_clip_button_blog">{t}COPYTOCLIPBOARD{/t}</div>
<input type="text" value='&lt;img src="http://{$server_name}/plugmedia/api.php?ac=rotatePic&pic={$link_pic}&percent=1" /&gt;' size="80" id="link_blog" />
<hr>
<h3>{t}SIMPLELINK{/t}</h3><div id="d_clip_button_single">{t}COPYTOCLIPBOARD{/t}</div>
<input type="text" value="http://{$server_name}/plugmedia/api.php?ac=rotatePic&pic={$link_pic}&percent=1" size="80" id="link_single" />

<script language="JavaScript">
var clip = new ZeroClipboard.Client();
clip.setText( document.getElementById('link_forum').value );
clip.setHandCursor( true );
clip.glue( 'd_clip_button_forum' );
						
var clip1 = new ZeroClipboard.Client();
clip1.setText( document.getElementById('link_blog').value );
clip1.setHandCursor( true );
clip1.glue( 'd_clip_button_blog' );

var clip2 = new ZeroClipboard.Client();
clip2.setText( document.getElementById('link_single').value );
clip2.setHandCursor( true );
clip2.glue( 'd_clip_button_single' );
</script>
</div>