<?php

/*------------------------------------------------------------------------
# mod_endofslidebox module
# ------------------------------------------------------------------------
# author    WebKul
# copyright Copyright (C) 2010 webkul.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.webkul.com
# Technical Support:  Forum - http://www.webkul.com/index.php?Itemid=86&option=com_kunena
-------------------------------------------------------------------------*/
// no direct access

defined('_JEXEC') or die('Restricted access'); 

if($wkytstream == '1') {
	$wkytstream = 'true';
	}
else {
	$wkytstream = '';
	}
	
if($addThumbs == '1') {
	$addThumbs = 'true';
	}
else {
	$addThumbs = 'false';
	}
	
if($autoPlay == '1') {
	$autoPlay = 'true';
	}
else {
	$autoPlay = 'false';
	}
	
if($allowFullScreen == '1') {
	$allowFullScreen = 'true';
	}
else {
	$allowFullScreen = 'false';
	}

$videolinksArray = explode(',',$videolinks);
$videoTitleArray = explode(',',$videoTitle);
$ytpath = JURI::root();

 ?>
 <link rel="stylesheet" type="text/css" href="modules/mod_custom_youtubeplaylist/css/jquery.ad-gallery.css">
 <style type="text/css">
td.greyline {
    background:transparent !important;}

.ad-gallery {
  width: <?php echo $width."px" ?>;
}
#ytvideo,
#ytvideo2 {
	margin-right:10px;
}

.yt_holder {
    background: #f3f3f3;
    padding: 10px;
    float: left;
    border: 1px solid #e3e3e3;
	margin-bottom:15px;
	
}

.yt_holder .currentvideo {
	background: #e6e6e6;
}		
</style>
<script type="text/javascript">
if (typeof jQuery == 'undefined')
{
    document.write(unescape("%3Cscript src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js' type='text/javascript'%3E%3C/script%3E"));
}
</script>
<script type="text/javascript" src="modules/mod_custom_youtubeplaylist/js/jquery.youtubeplaylist.js"></script>
<script type="text/javascript" src="modules/mod_custom_youtubeplaylist/js/jquery.ad-gallery.js"></script>

<div class="yt_holder">
	<div id="ytvideo2"></div>
	<div id="gallery" class="ad-gallery">
		<div class="ad-nav">
			<div class="ad-thumbs">
				<?php if (!empty($wkytstream)) { ?>
					<ul class="ytubeplay ad-thumb-list">						
					</ul>
				<?php } else { ?>
					<ul class="ytubeplay ad-thumb-list">
						<?php for($i=0; $i<count($videolinksArray); $i++) { ?>
							<li><a href="<?php echo $videolinksArray[$i]; ?>"><?php echo $videoTitleArray[$i]; ?></a></li>            
						<?php } ?>
					</ul>
				<?php } ?>			
			</div>
		</div>
	</div>
</div>
<script type="text/ecmascript">
jQuery.noConflict();
var ytpath ='<?php echo $ytpath; ?>';
var wkytstream ='<?php echo $wkytstream; ?>';
if (wkytstream =='true') {						
	var wk_youtube_user_id='<?php echo $ytuserstream; ?>';
	jQuery.ajax({
				url:'http://gdata.youtube.com/feeds/users/'+wk_youtube_user_id+'/uploads?alt=json-in-script&max-results=<?php echo $yt_numstream; ?>&format=5',
				type:'get',
				cache:true,
				dataType:'jsonp',		
			success:function(wk_data) 
			{
				if(!wk_data.feed.entry)
				{
					return false;
				}	
				jQuery.each(wk_data.feed.entry, function(i,e) {
					if(!e.published)
					{
						added=new Date(e.updated.$t);
					}
					else
					{
						added=new Date(e.published.$t);
					}	

					var wk_myval =e.link[0].href;					
					wk_myval = wk_myval.replace('&feature=youtube_gdata', '');
					
					var yt_title=e.title.$t;
					var yt_title=yt_title.substring(0,20);								
					
					jQuery('ul.ytubeplay').append(jQuery('<li/>')
														.append(jQuery('<a/>')
															.attr('href',wk_myval)
															.text(yt_title)
														));															
				});			
				
							
				jQuery("ul.ytubeplay").ytplaylist({
					addThumbs:<?php echo $addThumbs  ?>, 
					autoPlay: <?php echo $autoPlay  ?>, 
					holderId: 'ytvideo2',
					playerHeight: <?php echo $height ?>,
					playerWidth: <?php echo $width ?>,
					showRelated: <?php echo $relVedio ?>,
					allowFullScreen: <?php echo $allowFullScreen  ?>			
				});
			
				jQuery(function() {    
					var galleries = jQuery('.ad-gallery').adGallery();    
				});			
				
			}
		});
}
else
{	
	jQuery(document).ready(function() {
		jQuery("ul.ytubeplay").ytplaylist({
			addThumbs:<?php echo $addThumbs  ?>, 
			autoPlay: <?php echo $autoPlay  ?>, 
			holderId: 'ytvideo2',
			playerHeight: <?php echo $height ?>,
			playerWidth: <?php echo $width ?>,
			showRelated: <?php echo $relVedio ?>,
			allowFullScreen: <?php echo $allowFullScreen  ?>			
		});
	});
	jQuery(function() {    
		var galleries = jQuery('.ad-gallery').adGallery();    
	});			
}		
</script>