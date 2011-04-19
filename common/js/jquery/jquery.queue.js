/**
 * Ajax Queue Plugin
 */
(function($) {
    var ajax = $.ajax;
    var pendingRequests = {};
 
    $.ajax = function(settings) {
        settings = jQuery.extend(
            settings, 
            jQuery.extend(
                {}, 
                jQuery.ajaxSettings, 
                settings
            )
        );	
        var port = settings.port;
 
        switch(settings.mode) {
            case "abort": 
                if ( pendingRequests[port] ) {
                    pendingRequests[port].abort();
                }
                return pendingRequests[port] = ajax.apply(this, arguments);
 
            case "queue": 
                var _old = settings.complete;
                settings.complete = function(){
                    if ( _old )
                        _old.apply( this, arguments );
                    jQuery([ajax]).dequeue("ajax" + port );
                };
                jQuery([ ajax ]).queue("ajax" + port, function(){
                    ajax( settings );
                });
                return;
 
            case "dequeue": 
                jQuery([ajax]).dequeue("ajax" + port );
 
                if(jQuery.isFunction(settings.complete))
                    settings.complete(settings);
 
                return;
        }
 
        return ajax.apply(this, arguments);
    };
})(jQuery);