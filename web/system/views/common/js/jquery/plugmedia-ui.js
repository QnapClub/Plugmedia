/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
*/



var PM = {

	readNextSong: function (){

		$( "#read_next" ).button( { icons: { primary: "icon_repeat_next" }, text: false } )
			.click(function(){
				if (!$(this).is(':checked'))
					$.cookie('pm_repeatnext', '0', { expires: 365});
				else
					$.cookie('pm_repeatnext', '1', { expires: 365});
			});		
		
	},
	

	growlNotification: function (icon, message, title){
		 $.Growl.show({
			'title'  : title,
			'message': message,
			'icon'   : icon,
			'timeout': 5000
		 });
	},

	// function to load a page in a DIV 
	// in: target (url of the page to load)
	loadingPage : function(target) { 
	// get the page URL
		if(typeof(check_indexing) !== 'undefined') 
			clearInterval(check_indexing);
		if(typeof(check_queue_d) !== 'undefined') 	
			clearInterval(check_queue_d);
		
		PM.killJWPlayerLoading();
		
		var url = target;
		// Show modal Loading window
		PM.createLoadingModalWin();
		// load target in div contains in the page
		
		$.ajax({
			dataType: 'json',
			url: url,
			cache:true,
			success: function(data) {
				
				PM.updatePagePart(data); 
						
			},
			error: function(){ PM.redirectToIndex();return false;	},
			complete: function(){ 	} ,
			beforeSend : function(){}   
		});
		

	},

	killJWPlayerLoading: function() {

		if (typeof(jwplayer) !== 'undefined' && jwplayer('mediaspace') != null) 
		{
			jwplayer('mediaspace').stop();
			delete jwplayer('mediaspace');
			delete jwplayer;
		}
		
		
	},
	
	updatePagePart: function(data) {

		//var fields = data.serializeArray();
			 
		jQuery.each(data, function(target, value){
			$('#'+target).html(value).fadeTo(300, 1);
		});
				
		$.modal.close();
					
				
		PM.createGridList();
		PM.refreshjBreadCrumb();
		PM.resetButtonBubblePopup();
		PM.button_list();

				
		$("#inner_center_demo").scrollTop(0);
		
		
	},
	

	resetButtonBubblePopup: function () {

		$(".tipsy").remove();

		$('#button_thumb').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_list').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_slideshow').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_cooliris').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_radio').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_watchdir_subscribe').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		$('#button_watchdir').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
	
		
	},
	
	redirectToIndex: function () {
		
		window.location.href ='index.php'; 
	},
	
	
	createLoadingModalWin : function()	{
		$("#osx-modal-content").modal({	
			overlayId: 'osx-overlay',
			containerId: 'osx-container',
			closeHTML: null,
			minHeight: 80,
			opacity: 40, 
			position: ['0',],
			overlayClose: true,
			close :false,
			overlayClose :false,
			modal : false,
			onOpen: function (d) {PM.openModalLoading(d)},
			onClose: function (d) { PM.closeModalLoading(d);}
		});	
		
	},
	
	openModalLoading : function (d)	{
		var self = this;
		self.container = d.container[0];
		d.overlay.fadeIn('slow', function () {
			$("#osx-modal-content", self.container).show();
			var title = $("#osx-modal-title", self.container);
			title.show();
			d.container.slideDown('fast', function () {
				setTimeout(function () {
					var h = $("#osx-modal-data", self.container).height()+ title.height() + 20; // padding
					d.container.animate(
						{height: h}, 
						100,
						function () {	}
					);
				}, 50);
			});
		})	
					
	},
	
	closeModalLoading: function (d)	{
			$.modal.close(); 	
	},

	
	
	createGridList: function (){
		default_val = PM.refreshGridList();
		PM.addGridSlider(default_val);
	},
	
	refreshGridList: function () {
		var default_val = $.cookie('pm_slider_size');
		if (typeof(default_val)=='undefined' || default_val== null)
			default_val = '30';
		$('ul.grid li').css('font-size',default_val+"px");
		return default_val;
	},
	addGridSlider: function (default_val){
		$("#grid_slider").slider({
			value: default_val,
			max: 40,
			min: 20,
			animate: "fast",
			slide: function(event, ui) {
				$('ul.grid li').css('font-size',ui.value+"px");
				$.cookie('pm_slider_size', ui.value, { expires: 365});
			}
		});
		
	},
	
	
	refreshjBreadCrumb: function (){
		jQuery("#breadCrumb0").jBreadCrumb();
	},
	
	


	addTree: function()	{
		$("#pm_tree").jstree({ 
			"plugins" : [ "themes", "json_data", "ui", "cookies", "types", "contextmenu" ],
			"themes" : {
			            "theme" : "classic",
						"url" : PM_config.url_css+"/tree_style.css",
				            "dots" : true
				        },
			"json_data" : { 
				"ajax" : {
					"url" : "./api.php?ac=getalloweddir&disp=json",
					"type": "POST",
					"data" : function (n) { 
						return { 
							"node" : n.attr ? n.attr("id").replace("node_","") : "getroot_node" 
						}; 
					}
				}
			},
			"contextmenu": {
                "items": {
                    "create": false,
					"rename": false,
                    "remove": false,
                    "ccp": false, 
                    "add_playlist": {
                        "label": "Add all items to playlist",
                         "action": function (obj) { alert(obj); }
                    },
                    "create_root": {
                        "label": "Assign thumbnail",
                         "action": function (obj) { alert(obj); }
                    }

                } // end items
            }, 	
			
			"cookies" : { "save_opened":"jstree_open", "save_selected":false},		
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
				"initially_open" : [ "root" ]
			}
		})
		
 
		
	},
	
	lauch_login_windows: function()
	{
		

		$('#basic-modal-content').modal({

			containerCss :{
				height:200,
				width:400
			},
			onOpen: function (dialog) {
				dialog.overlay.fadeIn('fast');
				dialog.container.fadeIn('fast', function () { dialog.data.fadeIn('slow');} );

	
				
			}			
			
		});
		return false;
		
	},
	
	watch_dir: function(button){

		button.children(":first").removeClass("icon_watchdir");
		button.children(":first").addClass("icon_wait"); 
				
				
		$.ajax({
			dataType: 'json',
			url: 'api.php?ac=addfollower&'+button.attr("value"),
			cache:false,
			success: function(data) {
				if (data.success)
					button.children(":first").removeClass("icon_wait").addClass("icon_watchdir_ok");	
			},
			error: function(){ button.children(":first").removeClass("icon_wait").addClass("icon_watchdir");	}
		});			
		
	},
	watch_dir_register: function(button){

		button.children(":first").removeClass("icon_watchdir_ok");
		button.children(":first").addClass("icon_wait"); 
				
				
		$.ajax({
			dataType: 'json',
			url: 'api.php?ac=removefollower&'+button.attr("value"),
			cache:false,
			success: function(data) {
				if (data.success)
					button.children(":first").removeClass("icon_wait").addClass("icon_watchdir");	
			},
			error: function(){ button.children(":first").removeClass("icon_wait").addClass("icon_watchdir_ok");	}
		});			
		
		
	},	
	
	supportKeyboard: function(){

		$(document).keydown (function(e) {
			switch(e.keyCode) { 
					// User pressed "left" arrow
					case 37:
						if ($('#left_link').length > 0)
							PM.loadingPage($('#left_link').attr('href'));
					break;
					// User pressed "right" arrow
					case 39:
						if ($('#right_link').length > 0)
							PM.loadingPage($('#right_link').attr('href'));
					break;
				}
			
		});
	
		
	},

	button_list: function(){
		$('#button_watchdir').button({ 
				text:false,
				icons: {primary: 'icon_watchdir' }
			}).toggle(
				function () {
						PM.watch_dir($('#button_watchdir'));
					},
				function () {
						PM.watch_dir_register($('#button_watchdir'));
					}
			
			);	
		$('#button_watchdir_subscribe').button({ 
				text:false,
				icons: {primary: 'icon_watchdir_ok' }
			}).toggle(
				function () {
						PM.watch_dir_register($('#button_watchdir_subscribe'));
					},
				function () {
						PM.watch_dir($('#button_watchdir_subscribe'));
					}
			
			);				
			
					
		$('#button_radio').button({ 
				text:false,
				icons: {primary: 'icon_radio' }
			}).bind("click",function(){ window.open($(this).attr("value")); return false; });		
		$('#button_thumb').button({ 
				text:false,
				icons: {primary: 'icon_thumb' }
			}).bind("click",function(){	PM.resetButtonBubblePopup(); });	
		$('#button_thumb_list').button({ 
				text:false,
				icons: {primary: 'icon_thumb_list' }
			}).bind("click",function(){	PM.resetButtonBubblePopup(); });	
		$('#button_list').button({ 
				text:false,
				icons: {primary: 'icon_list' }
			}).bind("click",function(){	PM.resetButtonBubblePopup(); });	
		$('#button_slideshow').button({ 
				text:false,
				icons: {primary: 'icon_slideshow' }
			}).bind("click",function(){ window.open($(this).attr("value")); return false; });		
		$('#button_cooliris').button({ 
				text:false,
				icons: {primary: 'icon_cooliris' }
			}).bind("click",function(){ PicLensLite.start({feedUrl:$(this).attr("value")}); });		



			$('#button_thumb').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_list').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_slideshow').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_cooliris').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_radio').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_watchdir_subscribe').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
			$('#button_watchdir').tipsy({fade: true, gravity: $.fn.tipsy.autoNS, title: 'ref', fade: true});
		
		
	},
	


	enableSearchForm: function(){
		$('#search_form').ajaxForm({ 
			dataType:  'json', 
			 beforeSubmit: function() { $('#search_loading').fadeIn("slow"); },
			 success: function(responseJson, statusText, xhr, $form) { 
				$('#search_loading').fadeOut();
				PM.updatePagePart(responseJson); 
			}
		});		
	},




	
	init: function(){
		
		PM.refreshjBreadCrumb();
		PM.createGridList();
		PM.addTree();
		PM_toolbar.init();	
		PM.button_list();
		PM.supportKeyboard();
		
		PM.enableSearchForm();
		
		var outerLayout, innerLayout;
		outerLayout = $("body").layout( layoutSettings_Outer );
		var westSelector = "body > .ui-layout-west"; 
		$("<span></span>").addClass("pin-button").prependTo( westSelector );
		outerLayout.addPinBtn( westSelector +" .pin-button", "west");
		$("<span></span>").attr("id", "west-closer" ).prependTo( westSelector );
		outerLayout.addCloseBtn("#west-closer", "west");
		innerLayout = $( outerLayout.options.center.paneSelector ).layout( layoutSettings_Inner );
		
		
		
		// HIDE LOADING MASK AT START
		$('#loading').fadeOut("slow");
		$('#loading-mask').fadeOut("slow");		

		$("input.[name='login_reset']").click(function(){ $.modal.close(); });
	
		$('#login_form').ajaxForm({ 
			dataType:  'json', 
			success:  processJson,
			beforeSubmit: function(formData, jqForm, options) { 
				$("#result").hide();
				for (var i=0; i < formData.length; i++) { 
					if (!formData[i].value) { 
						$("#result > p").html(PM_config.lang_fillinfield);
						$("#result").fadeIn("slow");
						return false; 
					} 
				} 
				showLoginOnSubmit();
			}
		}); 

		function showLoginOnSubmit()
		{
			$('#login_loading').fadeIn("slow");
		}
		function hideLoginOnSubmit()
		{
			$('#login_loading').fadeOut();
		}
		
		

		

		function processJson(data) { 
			if (!data)
			{
				hideLoginOnSubmit();
				$("#result > p").html(PM_config.lang_errorlogin);
				$("#result").fadeIn("slow");
			}
			else
				window.location.reload();
		}


		$('.custom_target').live('click', function() {
		 	PM.loadingPage($(this).attr("href"));
			return false; // avoid redirect
		});	
	
		

		$('.btn_download').live('click', function() {
		 	PM.killJWPlayerLoading();
		});	













		
	}
}




// MODAL WINDOWS (OSX style)
jQuery(function ($) {
	var OSX = {
		container: null,
		init: function () {
			$("input.osx, a.osx").click(function (e) {
				e.preventDefault();	

				$("#osx-modal-content").modal({
					overlayId: 'osx-overlay',
					containerId: 'osx-container',
					closeHTML: null,
					minHeight: 80,
					opacity: 65, 
					position: ['0',],
					overlayClose: true,
					onOpen: OSX.open,
					onClose: OSX.close
				});
			});
		},
		open: function (d) {
			var self = this;
			self.container = d.container[0];
			d.overlay.fadeIn('slow', function () {
				$("#osx-modal-content", self.container).show();
				var title = $("#osx-modal-title", self.container);
				title.show();
				d.container.slideDown('slow', function () {
					setTimeout(function () {
						var h = $("#osx-modal-data", self.container).height()
							+ title.height()
							+ 20; // padding
						d.container.animate(
							{height: h}, 
							200,
							function () {
								$("div.close", self.container).show();
								$("#osx-modal-data", self.container).show();
							}
						);
					}, 300);
				});
			})
		},
		close: function (d) {
			var self = this; // this = SimpleModal object
			d.container.animate(
				{top:"-" + (d.container.height() + 20)},
				500,
				function () {
					self.close(); // or $.modal.close();
				}
			);
		}
	};

	OSX.init();

});


// *********** CONFIG ***************************
var layoutSettings_Inner = {
	north: {
		spacing_open:			0			// cosmetic spacing
	,	togglerLength_open:		0			// HIDE the toggler button
	,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
	,	size:					27
	,	resizable: 				false
	,	showOverflowOnHover:	true		
	,	slidable:				false
	,	fxName:					"none"
	}
};

var layoutSettings_Outer = {
	name: "outerLayout" 
	,	defaults: {
			useStateCookie:			true
		,	paneClass:				"pane" 		// default = 'ui-layout-pane'
		,	resizerClass:			"resizer"	// default = 'ui-layout-resizer'
		,	togglerClass:			"toggler"	// default = 'ui-layout-toggler'
		,	buttonClass:			"button"	// default = 'ui-layout-button'
		,	contentSelector:		".content"	// inner div to auto-size so only it scrolls, not the entire pane!
		,	contentIgnoreSelector:	"span"		// 'paneSelector' for content to 'ignore' when measuring room for content
		,	togglerLength_open:		35			// WIDTH of toggler on north/south edges - HEIGHT on east/west edges
		,	togglerLength_closed:	35			// "100%" OR -1 = full height
		,	togglerTip_open:		"Close This Pane"
		,	togglerTip_closed:		"Open This Pane"
		,	resizerTip:				"Resize This Pane"
		//	effect defaults - overridden on some panes
		,	fxName:					"slide"		// none, slide, drop, scale
		,	fxSpeed_open:			750
		,	fxSpeed_close:			1500
		,	fxSettings_open:		{ easing: "easeInQuint" }
		,	fxSettings_close:		{ easing: "easeOutBounce" }
	}
	,	north: {
			spacing_open:			0			// cosmetic spacing
		,	size:					97
		,	togglerLength_open:		0			// HIDE the toggler button
		,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
		,	resizable: 				false
		,	slidable:				false
		//	override default effect
		,	fxName:					"none"
	}
	,	south: {
			size:					35	
		,	spacing_closed:			0			// HIDE resizer & toggler when 'closed'
		,	togglerLength_open:		0			// HIDE the toggler button
		,	slidable:				false		// REFERENCE - cannot slide if spacing_closed = 0
		,	resizable: 				false
		,	spacing_open:			0			// cosmetic spacing
				
	}
	,	west: {
			size:					250
		,	spacing_closed:			5			// wider space when closed
		,	togglerLength_closed:	21			// make toggler 'square' - 21x21
		,	spacing_open:			5			// wider space when closed
		,	togglerAlign_closed:	"center middle"		// align to top of resizer
		,	togglerLength_open:		40			// NONE - using custom togglers INSIDE west-pane
		,	togglerTip_open:		"Close West Pane"
		,	togglerTip_closed:		"Open West Pane"
		,	resizerTip_open:		"Resize West Pane"
		,	slideTrigger_open:		"click" 	// default
		,	initClosed:				false
		//	add 'bounce' option to default 'slide' effect
			
	}
		
		
	,	center: {
			paneSelector:			"#mainContent" 			// sample: use an ID to select pane instead of a class
		,	onresize:				"innerLayout.resizeAll"	// resize INNER LAYOUT when center pane resizes
		,	minWidth:				200
		,	minHeight:				200
	}
};

