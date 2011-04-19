/**************************************************************
*
* Growl - for the net and jQuery (http://labs.d-xp.com/growl/)
* 
* @author  : Artur Heinze
* @version : 1.01 
**************************************************************/

(function($){
  
  //GROWL OBJECT
  //--------------------------------------------------------------------
  
  $.Growl = {

    _statsCount: 0,
    
    show: function(options){
    
      var settings = $.extend({
        "id": ("gs"+$.Growl._statsCount++),
        "icon": false,
        "title": false,
        "message": "",
        "cls": "",
        "speed": 500,
        "timeout": 3000
      },options);
      
      if($("#"+settings.id).length!=0){
        $("#"+settings.id).remove();
      }
      
      //append status
      this._getContainer().prepend(
        '<div id="'+settings.id+'" class="growlstatus '+settings.cls+'" style="display:none;"><div class="growlstatusclose"></div>'+settings.message+'</div>'
      );
      
      var status = $("#"+settings.id);
      
      //bind close button
      status.find(".growlstatusclose").bind('click',function(){
        $.Growl.close($(this).parent().attr("id"),true,settings.speed);
      });
      
      //show title
      if(settings.title!==false){
        $("#"+settings.id).prepend('<div class="growltitle">'+settings.title+'</div>');
      }
      
      //show icon
      if(settings.icon!==false){
        
        status.addClass("growlwithicon");
        status.addClass("growlicon_"+settings.icon);
      }
      
      status
      //do not hide on hover
      .hover(
        function(){
          $(this).addClass("growlhover");
        },
        function(){
          $(this).removeClass("growlhover");
          if(settings.timeout!==false){
            window.setTimeout("$.Growl.close('"+settings.id+"')", settings.timeout);
          }
        }
      )      
      //show status+handle timeout
      .fadeIn(settings.speed,function(){
        if(settings.timeout!==false){
          window.setTimeout("$.Growl.close('"+settings.id+"')", settings.timeout);
        }
      });
      
      return settings.id;
    },
    
    close: function(id,force,speed){
    
      if(arguments.length==0){
        $(".growlstatus",this._getContainer()).hide().remove();
      }else{
          if(!$("#"+id).hasClass("growlhover") || force){
              $("#"+id).animate({opacity:"0.0"}, speed);
              $("#"+id).slideUp(function(){
                  $(this).remove();
            })
          }
      }
    
    },
    
    _getContainer: function(){
      
      if($("#growlcontainer").length==0) {
        $("body").append('<div id="growlcontainer"></div>');
      }
      
      return $("#growlcontainer");
      
    }
  
  };
  
  
  //HELPER FUNCTIONS
  //--------------------------------------------------------------------
  //
  // none for now

})(jQuery);