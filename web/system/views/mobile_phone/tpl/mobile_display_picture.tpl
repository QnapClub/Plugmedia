	
	<script type="text/javascript">
		
		/*
		 * IMPORTANT!!!
		 * REMEMBER TO ADD  rel="external"  to your anchor tags. 
		 * If you don't this will mess with how jQuery Mobile works
		 */
		
		(function(window, $, PhotoSwipe){
			
			$(document).ready(function(){
				
				
				$('div.gallery-page').live('pageshow', function(e){
						
					// See if there is a PhotoSwipe instance associated with the page.
					// For this demo I've assumed one page has one instance and the ID 
					// for each instance is the same as the page ID.
					//
					// Of course, it's up to you how many instances per page and what
					// ID naming convention you use!
					var 
						currentPage = $(e.target),
						photoSwipeInstanceId = currentPage.attr('id'),
						photoSwipeInstance = PhotoSwipe.getInstance(photoSwipeInstanceId)
						options = {};
					
					if (typeof photoSwipeInstance === "undefined" || photoSwipeInstance === null) {
						photoSwipeInstance = $("ul.gallery a", e.target).photoSwipe(options, photoSwipeInstanceId);
					}
					
					return true;
					
				})
					
			});
		
		}(window, window.jQuery, window.Code.PhotoSwipe));
		
	</script>
<!--<div id="browse">
             <div class="toolbar">
                <h1>Display</h1>
					 			<a class="button back" href="#">Retour</a>
					 			<a class="button slideup" href="mobile.php">Home</a>
            </div>
           
           
  <img src="api.php?ac=rotatePic&pic={$current_media.file_id}&percent=1" width="{$current_media.size.0}" height="{$current_media.size.1}" align="middle" id="art_pic" />         

			
</div> -->


<div data-role="page" data-add-back-btn="true" id="Gallery2" class="gallery-page">

	<div data-role="header">
		<h1>Second Gallery</h1>
	</div>

	<div data-role="content">	
		
		<ul class="gallery">
		
			<li><a href="images/full/010.jpg" rel="external"><img src="images/thumb/010.jpg" alt="Image 010" /></a></li>
			<li><a href="images/full/011.jpg" rel="external"><img src="images/thumb/011.jpg" alt="Image 011" /></a></li>
			<li><a href="images/full/012.jpg" rel="external"><img src="images/thumb/012.jpg" alt="Image 012" /></a></li>
			<li><a href="images/full/013.jpg" rel="external"><img src="images/thumb/013.jpg" alt="Image 013" /></a></li>
			<li><a href="images/full/014.jpg" rel="external"><img src="images/thumb/014.jpg" alt="Image 014" /></a></li>
			<li><a href="images/full/015.jpg" rel="external"><img src="images/thumb/015.jpg" alt="Image 015" /></a></li>
			<li><a href="images/full/016.jpg" rel="external"><img src="images/thumb/016.jpg" alt="Image 016" /></a></li>
			<li><a href="images/full/017.jpg" rel="external"><img src="images/thumb/017.jpg" alt="Image 017" /></a></li>
			<li><a href="images/full/018.jpg" rel="external"><img src="images/thumb/018.jpg" alt="Image 018" /></a></li>
		
		</ul>
		
	</div>

	<div data-role="footer">
		<h4>&copy; 2011 Code Computerlove</h4>
	</div>

</div>
