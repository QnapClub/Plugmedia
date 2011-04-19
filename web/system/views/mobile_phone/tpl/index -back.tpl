<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>PlugMedi@</title>
        <style type="text/css" media="screen">@import "{$adresse_css}/jqtouch.min.css";</style>
        <style type="text/css" media="screen">@import "{$adresse_css}/theme.css";</style>
        <script src="{$adresse_js}/jquery.1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="{$adresse_js}/jqtouch.min.js" type="application/x-javascript" charset="utf-8"></script>
 {literal}
        <script type="text/javascript" charset="utf-8">
            var jQT = new $.jQTouch({
                icon: '{/literal}{$adresse_images}{literal}/jqtouch.png',
                addGlossToIcon: false,
                startupScreen: '{/literal}{$adresse_images}{literal}/jqt_startup.png',
                statusBar: 'black',
                preloadImages: [
                    '{/literal}{$adresse_images}{literal}/back_button.png',
                    '{/literal}{$adresse_images}{literal}/back_button_clicked.png',
                    '{/literal}{$adresse_images}{literal}/button_clicked.png',
                    '{/literal}{$adresse_images}{literal}/grayButton.png',
                    '{/literal}{$adresse_images}{literal}/whiteButton.png',
                    '{/literal}{$adresse_images}{literal}/loading.gif'
                    ]
            });
            // Some sample Javascript functions:
            $(function(){
                // Show a swipe event on swipe test
                $('#swipeme').swipe(function(evt, data) {                
                    $(this).html('You swiped <strong>' + data.direction + '</strong>!');
                });
                $('a[target="_blank"]').click(function() {
                    if (confirm('This link opens in a new window.')) {
                        return true;
                    } else {
                        $(this).removeClass('active');
                        return false;
                    }
                });
                // Page animation callback events
                $('#pageevents').
                    bind('pageAnimationStart', function(e, info){ 
                        $(this).find('.info').append('Started animating ' + info.direction + '&hellip; ');
                    }).
                    bind('pageAnimationEnd', function(e, info){
                        $(this).find('.info').append(' finished animating ' + info.direction + '.<br /><br />');
                    });
                // Page animations end with AJAX callback event, example 1 (load remote HTML only first time)
                $('#callback').bind('pageAnimationEnd', function(e, info){
                    if (!$(this).data('loaded')) {                      // Make sure the data hasn't already been loaded (we'll set 'loaded' to true a couple lines further down)
                        $(this).append($('<div>Loading</div>').         // Append a placeholder in case the remote HTML takes its sweet time making it back
                            load('ajax.html .info', function() {        // Overwrite the "Loading" placeholder text with the remote HTML
                                $(this).parent().data('loaded', true);  // Set the 'loaded' var to true so we know not to re-load the HTML next time the #callback div animation ends
                            }));
                    }
                });
                // Orientation callback event
                $('body').bind('turn', function(e, data){
                    $('#orient').html('Orientation: ' + data.orientation);
                });
            });

        </script>
  {/literal}       
        <style type="text/css" media="screen">
            body.fullscreen #home .info {
                display: none;
            }
            #about {
                padding: 100px 10px 40px;
                text-shadow: rgba(255, 255, 255, 0.3) 0px -1px 0;
                font-size: 13px;
                text-align: center;
                background: #161618;
            }
            #about p {
                margin-bottom: 8px;
            }
            #about a {
                color: #fff;
                font-weight: bold;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div id="about" class="selectable">

<br /><br />
                <p align="center"><strong>Plugmedia</strong><br />
                    <em>For QNAP</em></p>
                
                <p><br /><br /><a href="#" class="grayButton goback">Close</a></p>
        </div>
       
       
 
  
        <div id="home" class="current">
            <div class="toolbar">
                <h1>PlugMedi@</h1>
                <a class="button slideup" id="infoButton" href="#about">About</a>
            </div>
            <ul class="rounded">
                <li class="arrow"><a href="mobile.php?page=browse">Browse directories</a></li>
                <li class="arrow"><a href="#play_radio">Play radio</a></li>
                <li class="arrow"><a href="#random_picture">Random Picture</a></li>
                <li class="arrow"><a href="#last_items">Last Items</a> <small class="counter">56</small></li>                                   
            </ul>
            <h2>External Links</h2>
            <ul class="rounded">
                <li class="forward">Plugmedia Website</li>

            </ul>
            <div class="info">
                <p>Add this page to your home screen to view the custom icon, startup screen, and full screen mode.</p>
            </div>
        </div>
    </body>
</html>