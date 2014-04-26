<?php

 /**
 * @package		Joomla.Site
 * Youtubeplaylist Module
 *
 * @version 5.0
 * @package Youtubeplaylist
 * @author Nguyen Hoang Viet
 * @copyright Copyright (C) 2008 Luyenkim.net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class modYoutubeplaylistHelper {
/*
        <option value="http://gdata.youtube.com/feeds/api/videos?q=" selected>Default YouTube</option>
        <option value="http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?q=">YouTube: Top Favorites</option>
        <option value="http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?q=">YouTube: Most Recent</option>
        <option value="http://gdata.youtube.com/feeds/api/standardfeeds/US/top_rated_Comedy?q=">YouTube: Comedy</option>
        <option value="http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?q=">YouTube: Most Viewed</option>
        <option value="http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?q=">YouTube: Top Rated</option>
        <option value="http://www.trbailey.net/javascript/simple/demo/blip.php?search=">Blip TV</option>
		//http://docs.joomla.org/Spacer_parameter_type
*/
	function getContent(&$params, $mid = '') {	
		$mainframe = &JFactory::getApplication();
		$doc = &JFactory::getDocument();
		//used for javascript
		$theId = $mid;
		$theWrapper = $mid.'-wrapper';
		$thePlaceholder = $mid.'-placeholder';

		$width = $params->get('width','425');
		$height = $params->get('height','355');
		$width3 = $params->get('imagewidth','50');
		$numimage3  = $params->get('numimage','4');
		$height3  = floor($width3*3/4);
		$widths = $params->get('sl_imagewidth','50');
		$heights = floor($widths*3/4);
		$numimages = $params->get('sl_numimage','4');

		$allowfullscreen = $params->get('allowfullscreen','1');
		$randomplay = intval($params->get('randomplay','0'));
		
		$autoplay = $params->get('autoplay',0);
	
		$disp_style = $params->get('style','number'); //Display style - 0 number list, 1-Drop list, 2-image list, 3-image list slider

		
		$hd_jwplay_on = $params->get('hd_jwplay_on',0); //0: Normal 1: HD Playing	  

		$playlist = intval($params->get('playlist','0')); //0:video 1:Playlist 2:User

		$cached_time = $params->get('cached_time',0);
		$position = $params->get('position',0); //0 above; 1: Below; 2: Customize

		
		$playerskin = $params->get('playerskin',0);
		$playlist_position = $params->get('playlist_position','bottom');
		$playlist_size = $params->get('playlist_size','320');
		if($autoplay) $theAutostart = 'true'; else $theAutostart = 'false';

/******************************************************************************************************/		
		//Chuẩn bị danh sách đề mục video
		$videotype = array(0=>'video', 1 => 'playlist', 2 => 'user', 3=>'combination');
		$videolist = array(0=>'vid', 1=>'vpid', 2=>'vuid', 3=>'ytid');
		
		if(!$params->get($videolist[$playlist],0)) return JText::_('MISSING_ID');

		$theoptions['type'] = $videotype[$playlist];
		$theoptions['mid'] = $mid;
		$theoptions['source'] = $videolist[$playlist];
		$theoptions['autostart'] = $theAutostart;
		$theoptions['divid'] = $mid;
		$theoptions['playlist_items'] = $params->get('playlist_items','25'); //$playlist_items
		//$theoptions['allowfullscreen'] = $params->get('allowfullscreen','1')=='1'?'true':'false';
		

		//Built the list
		$youtube_list = modYoutubeplaylistHelper::built_video_list($params, $theoptions);		
		
/******************************************************************************************************/
		//default video for playing NOW
		$defaultvideo = new stdClass;
		$defaultvideo->type = $theoptions['type'];
		
		$i = 0;
		if($randomplay)
			do {
			$t_id = rand(0,$youtube_list->items-1); //0 Choose a video
			$defaultvideo->video = $youtube_list->videos[$t_id];
			$defaultvideo->title = $youtube_list->titles[$t_id];
			$defaultvideo->image = $youtube_list->images[$t_id];
			if(!is_null($youtube_list->types)) $defaultvideo->type = $youtube_list->types[$t_id];
			if($i++ == 10) break;
			} while ($defaultvideo->video == '...');
		else {
			$defaultvideo->video = $youtube_list->videos[$i];
			$defaultvideo->title = $youtube_list->titles[$i];
			$defaultvideo->image = $youtube_list->images[$i];
			if(!is_null($youtube_list->types)) $defaultvideo->type = $youtube_list->types[$i];
		}	
		$theoptions['search'] = $defaultvideo->video;
		$theoptions['_type'] = $defaultvideo->type;
	/***********************************************************************************/
	

    //Number list - 0
	//Drop list - 1
		//built_control($params, $youtube, $theoptions)
    
	//image list

		//Load default script and Style	
		modYoutubeplaylistHelper::InitScriptAndStyle($theoptions);		
		$html = modYoutubeplaylistHelper::InitDefaultPlaying($params, $defaultvideo, $theoptions);
/*****************************************************/
	$playid_list_p = $playid_list = '';
		if($params->get('user_playlist',1) == 1){
			if($playlist == 2)//user
				$playid_list_p = modYoutubeplaylistHelper::built_image_playlist_hidden($params, $youtube_list->videos, strtolower($defaultvideo->video), $mid);
			else {//combination
				$theuser = array();
				for($i = 0; $i < count($youtube_list->videos); $i++)
					if($youtube_list->types[$i] == 'user') $theuser[] = $youtube_list->videos[$i];
				$playid_list_p = modYoutubeplaylistHelper::built_image_playlist_hidden($params, $theuser, strtolower($defaultvideo->video), $mid);
			}
		}
			
	$playid_list .= modYoutubeplaylistHelper::built_control($params, $youtube_list, $theoptions);
	
	$playid_video_description = '<div id="'.$mid.'-youtubevideo-description">'.JText::_('PLAYING_DESCRIPTION').$defaultvideo->title.'</div>';
	
	$textb4 = $params->get('before_string');
	$textat = $params->get('after_string'); //nl2br
	
	switch($position){
		case 1: //below
			$playlist_ctrl = '';
			if($params->get('playlist',0)>0) $playlist_ctrl = modYoutubeplaylistHelper::showNPbutton($mid, $params);
			$html = $html .$playid_video_description.$playlist_ctrl. 
			'<div id="'.$mid.'-youtubevideo-playlist">' .$playid_list_p. $playid_list.'</div>';	
		break;
		case 2:
			$ctrl_text = array('box_number', 'box_droplist', 'box_image', 'box_image_slider', 'box_image_hidden', 'playing_control_text_button','playing_control_image_button','video_description');
				for($i = 0; $i < 2; $i++){
				//Text before then after
					if($i == 0) $t = $textb4;
					else $t = $textat;
					
					foreach($ctrl_text as $item_text){
						$code = '{playlist_'.$item_text.'}';
						if(strpos($t, $code)!== false){
							switch($item_text){
								case 'box_number':
									$params->set('style','number');
									$t = str_replace($code,modYoutubeplaylistHelper::built_control($params, $youtube_list, $theoptions),$t);
								break;
								case 'box_droplist':
									$params->set('style','drop');
									$t = str_replace($code,modYoutubeplaylistHelper::built_control($params, $youtube_list, $theoptions),$t);
								break;
								case 'box_image':
									$params->set('style','image');
									$t = str_replace($code,modYoutubeplaylistHelper::built_control($params, $youtube_list, $theoptions),$t);
								break;	
								case 'box_image_slider':
									$params->set('style','slider');
									$t = str_replace($code,modYoutubeplaylistHelper::built_control($params, $youtube_list, $theoptions),$t);
								break;
								case 'box_image_hidden':
									$playlist = $params->get('playlist');
									if(empty($playid_list_p)) {
										$playid_list_p = '';
										if( $youtube_list->type == 'user')//user
											$playid_list_p = modYoutubeplaylistHelper::built_image_playlist_hidden($params, $youtube_list->videos, strtolower($defaultvideo->video), $mid);
										elseif( $youtube_list->type == 'combination') {//combination
											$theuser = array();
											for($i = 0; $i < count($youtube_list->videos); $i++) //make a list
												if($youtube_list->types[$i] == 'user') $theuser[] = $youtube_list->videos[$i];
											$playid_list_p = modYoutubeplaylistHelper::built_image_playlist_hidden($params, $theuser, strtolower($defaultvideo->video), $mid);
										}
									}
									
									$t = str_replace($code,$playid_list_p,$t);
								break;						
								case 'playing_control_text_button':
									$params->set('playlist_np',1);
									$t = str_replace($code,modYoutubeplaylistHelper::showNPbutton($mid, $params),$t);
								break;	
								case 'playing_control_image_button':
									$params->set('playlist_np',2);
									$t = str_replace($code,modYoutubeplaylistHelper::showNPbutton($mid, $params),$t);
								break;		
								case 'video_description':
									//$playid_video_description
									$t = str_replace($code,$playid_video_description,$t);
								break;
								}
							}
						}
					if($i == 0) $textb4 = $t;
					else $textat = $t;
				}
		break;
		default: //0: above
			$playlist_ctrl = '';
			if($params->get('playlist',0)>0) $playlist_ctrl = modYoutubeplaylistHelper::showNPbutton($mid, $params);
			$html = '<div id="'.$mid.'-youtubevideo-playlist">' . $playid_list.$playid_list_p.'</div>'.
				$playlist_ctrl. $playid_video_description . $html;
			//$html = $playid_list . '<br />' . $html;	

	}
			if($textb4) {$textb4 = str_replace('{','<',$textb4);$textb4 = str_replace('}','>',$textb4);}
			if($textat) {$textat = str_replace('{','<',$textat);$textat = str_replace('}','>',$textat);}		
	return $textb4.$html.$textat; //. modYoutubeplaylistHelper::justtest();
   }
	
	function built_video_list(& $params, $theoptions){
		//Chuẩn bị danh sách đề mục video
		$videolist = $types = $videos = $titles = $images = array();
		$thevideolist = $params->get($theoptions['source']);
		$cached_time = $params->get('cached_time',0);
		$cb_youtubelist_play = false; $theparams = '';
		
	
		//cb_youtubelist_field
		if( $params->get('cb_youtubelist',0)) {
			if (file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' )) {
				include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
				cbimport( 'cb.field' );
				cbimport( 'language.front' );
				//JError::raiseWarning(500, 'CB loaded');

				global $_CB_framework; //, $_CB_database, $ueConfig;
				
				outputCbTemplate( $_CB_framework->getUi() );
				$myId = $_CB_framework->myId();
				
				if ( $myId ) {
					$cbUser = & CBuser::getInstance( $myId );
					if ( $cbUser !== null ) {
						//$thumbnailAvatarHtmlWithLink = $cbUser->getField( 'avatar', null, 'html', 'none', 'list' );
						$youtubelist = $cbUser->getField( $params->get('cb_youtubelist_field','') );
						
						if(!empty($youtubelist)){
							//JError::raiseWarning(500, $youtubelist);
							$thevideolist = $youtubelist;
								
							if(strpos($youtubelist, '::data::') > 0) {
								$_t = explode('::data::',$youtubelist, 2);
								$theparams = $_t[0];
								$thevideolist = trim(@$_t[1],"\t\n");								
								}						
							$theoptions['type'] = 'combination';						
							$cb_youtubelist_play = true;
							$cached_time = 0;
						}
					}
				}
			}
		}		

		$type 	= 	$theoptions['type'];
		$mid 	=	$theoptions['mid'];
		
		$videolist = preg_split('/\n|<br \/>/', $thevideolist); //[\n,] tach cho ca \n va , 
		//Get video list
		
		
		//$cached_time = 0;
		if($theoptions['type'] == 'combination'){
			$seq = 3;
			$seq_type = 1;
			$seq_id = 1; //combination
			$seq_title = 2;
		}else {
			$seq = 2;
			$seq_type = 0;
			$seq_id = 0;//id items
			$seq_title = 1;			
		}		
		$thefile = JPATH_SITE.DS. 'modules'.DS.'mod_youtubeplaylist'.DS.'params'.DS.'module_'.$mid.'.php';
		
		$nitems = 0;		
		if(!file_exists($thefile) || ($cached_time== 0) || ($cached_time == -2)){
			for ($i = 0; $i < count($videolist); $i++) {
				//JError::raiseWarning(500, trim($videolist[$i]));
				$temp = explode($params->get('split_letter','|'),trim($videolist[$i]),$seq);
				
				if($seq_type) $type = @$temp[0];				
				$vid_id = modYoutubeplaylistHelper::getPattern(@$temp[$seq_id],$type); //type: video, playlist, user					
					if(!isset($temp[$seq_title]) && ($type == 'video')) {
						$video_info = modYoutubeplaylistHelper::getVideo($vid_id);
						$temp[$seq_title] = $video_info['title'].' ('.$video_info['length'].')';				
					}
				if(empty($vid_id)) continue;
				if($seq_type) $types[] = $type;
				$videos[]= $vid_id;
				//JError::raiseWarning(500, $i.'/'.count($videolist).' '.$vid_id);
				//$temp[1] = isset($temp[1])? $temp[1]: modYoutubeplaylistHelper::getTitle($vid_id);
				$t_ = isset($temp[$seq_title])? $temp[$seq_title]:JText::_('CLICK_TO_PLAY');
				$t_ = htmlspecialchars_decode ($t_);
				//http://www.wetpaintinjected.com/page/Single+Quotes,+JavaScript,+and+Event+Handlers#fbid=tx8zT8vqJEl
				$titles[] = htmlspecialchars (str_replace("'", "\'",$t_)); //special chars: ", &, <, >
				$image_url = '';
				if($type=='video') $image_url = 'http://img.youtube.com/vi/'.$videos[$nitems].'/default.jpg';
				else $image_url = modYoutubeplaylistHelper::get_1stthumbnail($type,$videos[$nitems]);
				
				$nitems++;
				if($image_url) $images[] = $image_url;
				else { //If image not found, the playlist ID there is no video.
					array_pop($titles); 
					array_pop($videos);
					if($seq_type) array_pop($types); 
					$nitems--;
					}
			}
			//JError::raiseWarning(500, count($videos));
			$youtube = new stdClass;
			$youtube->videos = $videos;
			$youtube->titles = $titles;
			$youtube->images = $images;
			if($seq_type) //$theoptions['type'] == 'combination'
				$youtube->types = $types;
				else $youtube->types = null;
			$youtube->type = $theoptions['type'];
			
			$youtube->items = $nitems;
			//JError::raiseWarning(500, print_r($youtube,true));
			//return;
			if(($cached_time == 0 || $cached_time == '-2') && file_exists($thefile)) unlink($thefile); //Delete the file

			if($cached_time >0 || ($cached_time == -1) || ($cached_time == -2)) { //cached_time != 0 Make the cache file
				modYoutubeplaylistHelper::write_params_itemid($mid,$youtube); //Creat file
				//include $thefile;
				if($cached_time == '-2') modYoutubeplaylistHelper::built_params($params, $mid);	
			}
		} else {
			include $thefile;
			//JError::raiseWarning(500, print_r($videolist,true));
			if($cached_time>0 && (filectime($thefile) < (time()-$cached_time))) unlink($thefile);  //$cached_time
		}
		//if ($mid == 50) JError::raiseWarning(500, 'Hello: '.$params->toString());
		//DONE
		
		//return;
		
		$items = $youtube->items;
		$randomitems = $params->get('randomitems',0);
		$rand_list = ($params->get('randomlist',0)==1) && ($randomitems>0) && ($randomitems < $items);
		if($rand_list){
			$rand_keys = array_rand($youtube->videos, $randomitems);
			$t = array();
			$t = $youtube->videos;
			$youtube->videos = array();
			foreach($rand_keys as $key)$youtube->videos[] = $t[$key];
			
			$t = $youtube->titles;
			$youtube->titles = array();
			foreach($rand_keys as $key)$youtube->titles[] = $t[$key];
			
			$t = $youtube->images;
			$youtube->images = array();
			foreach($rand_keys as $key)$youtube->images[] = $t[$key];
			
			if($youtube->type == 'combination'){
				$t = $youtube->types;
				$youtube->types = array();
				foreach($rand_keys as $key)$youtube->types[] = $t[$key];
			}
			$youtube->items = $randomitems;
		}
			//playlistid from an user
			if($youtube->type == 'combination' && $cb_youtubelist_play){
				for ($i = 0; $i < count($youtube->videos); $i++) 
					if($youtube->type == 'user')
						modYoutubeplaylistHelper::write_params_user(strtolower($youtube->videos[$i]), modYoutubeplaylistHelper::get_tag($theparams,'cached_time', 60));
			}
			
		return $youtube;
	}
	function load_style_script(& $params){
	
	}
	function built_current(& $params){
	
	}
	

	function built_control(&$params, $youtube, $theoptions, $style = '') {
		$doc = &JFactory::getDocument();
			//Number list
		$count = $youtube->items - 1;
		$mid = $theoptions['mid'];
		$page = $theoptions['type'];
		//$theoptions['divid']
		
		
		$width	= $params->get('width','425');
		$height = $params->get('height','355');
		$theSkin =  $params->get('playerskin',0);
		$playing_list = '';
		if($style == '') $style = $params->get('style','number');
		switch ($style){
		case 'number':
			$playid_list_title = JText::_('PLAYLIST');
			if($count>=0)
			for ($i = 0; $i <= $count; $i ++) {
				if(!is_null($youtube->types)) $page = $youtube->types[$i];
				$playing_list .='<a href="javascript:void(0)" onclick="initPlayer(\''.$mid.'\',\''.$page.'\',\''. $youtube->videos[$i] .'\',\''.$width.'\',\''. $height.'\',\''.$theSkin.'\',\'true\'); document.getElementById(\''.$mid.'-youtubevideo-description\').innerHTML=\''.htmlspecialchars(JText::_('PLAYING_DESCRIPTION'),ENT_QUOTES).'\' + this.title;  return false;" title="'.$youtube->titles[$i].'">'.$i.'</a> ';
				if($i < $count) $playing_list .='| ';
			}
			$playing_list = $playid_list_title . $playing_list;
			break;
		case 'drop':
			
			$playing_list ='<form name="'.$mid.'youtube-droplist" action="#" method="get"> 
			<select name="droplist" onchange="'.
			'initPlayer(\''.$mid.'\',\''. $page.'\',this.form.droplist.value,\''.$width.'\',\''. $height.'\',\''.$theSkin.'\',\'true\');document.getElementById(\''.$mid.'-youtubevideo-description\').innerHTML=\''.htmlspecialchars(JText::_('PLAYING_DESCRIPTION'),ENT_QUOTES).'\' + this.form.droplist.options[this.form.droplist.selectedIndex].text;">
			<option value="...">'.JText::_('PLAYLIST_SELECTION').'</option>'; 
			for ($i = 0; $i <= $count; $i ++) {
				if(!is_null($youtube->types)) $pagex = '|'.$youtube->types[$i]; else $pagex = '';
				$playing_list .="\n".
				'<option value="'.$youtube->videos[$i].$pagex.'">'.$youtube->titles[$i].'</option> ';
			}
			$playing_list .="\n".'</select>';
			$playing_list .="\n".'</form>'."\n";
			break;
		case 'image':
	//function built_image_list($page, &$videos, &$titles, &$images, $divid=''){
			$width3 = $params->get('imagewidth','50');
			$numimage3  = $params->get('numimage','4');
			$height3  = floor($width3*3/4);	
			$divid =' '.$theoptions['divid'];
			if($numimage3==0) $numimage3 =  $count + 1;
			$playing_list ='<div class="mod_youtubeplaylist">';
			for ($i = 0; $i <= $count; $i ++) {
				if(!is_null($youtube->types)) $page = $youtube->types[$i];
				$playing_list .='<a href="javascript:void(0)" onclick="initPlayer(\''.$mid.'\',\''. $page.'\',\''.$youtube->videos[$i].'\',\''.$width.'\',\''. $height.'\',\''.$theSkin.'\',\'true\'); document.getElementById(\''.$mid.'-youtubevideo-description\').innerHTML=\''.htmlspecialchars(JText::_('PLAYING_DESCRIPTION'),ENT_QUOTES).$youtube->titles[$i].'\'; return false;" title="'.$youtube->titles[$i].'"><img width="'.$width3.'" height="'.$height3.'" src="'.$youtube->images[$i].'" alt="" /></a>'; //alt = "" 
			 $k = $i + 1;
			if(($k % $numimage3) == 0) $playing_list .='<br />'; 
			}
			$playing_list .='</div>';
			break;
		case 'slider':	
/************************/

		$img_num = $params->get('sl_numimage','4');
		
		$box_background = $params->get('box_background','#EBE2E5');

		if($img_num==0) $img_num =  $count + 1;

		$img_width = $params->get('sl_imagewidth','50');
		$img_height = floor($img_width*3/4);
		
		$img_padding = 2;
		$img_margin_right = 4;
		
		$thumb_box_width = $img_width * $img_num;// + 2*$img_num;
		$gallery_box_width = $thumb_box_width + 50;
		
		$gallery_container = "{$mid}_gallery_container";
		$thumb_container = "{$mid}_thumb_container";
		$thumbs = "{$mid}_thumbs";
		
		$playing_list ="\n\t\t<div id=\"{$gallery_container}\">
		<div id=\"{$thumb_container}\">
			<div id=\"{$thumbs}\">";
		for ($i = 0; $i <= $count; $i ++) {
			if(!is_null($youtube->types)) $page = $youtube->types[$i];
		//initPlayer(id, page, theSearch, width, height, theSkin, theAutostart, theOptions) {
			$playing_list .=
			'<a href="javascript:void(0)" onclick="initPlayer(\''.$mid.'\',\''. $page.'\',\''.$youtube->videos[$i].'\',\''.$width.'\',\''. $height.'\',\''.$theSkin.'\',\'true\'); document.getElementById(\''.$mid.'-youtubevideo-description\').innerHTML=\''.htmlspecialchars(JText::_('PLAYING_DESCRIPTION'),ENT_QUOTES).$youtube->titles[$i].'\'; return false;" title="'.$youtube->titles[$i].'"><img width="'.$img_width.'" height="'.$img_height.'" src="'.$youtube->images[$i].'" alt="" /></a>'; 
		}
		$playing_list .=	
'			</div>
		</div>
	</div>	';

		$doc->addScript(JURI::root(true).'/modules/mod_youtubeplaylist/js/slideit.js'); //Ok done
		//$doc->addStyleSheet(JURI::root(true).'/modules/mod_youtubeplaylist/css/slideit.css'); //
		$slideit = "\nwindow.addEvent('domready', function(){
		new SlideItMoo({
					itemsVisible:{$img_num},
					thumbsContainer: '{$thumbs}',
					elementScrolled: '{$thumb_container}',
					overallContainer: '{$gallery_container}',
					addfwdbutton: '{$mid}_addfwd',
					addbkwdbutton: '{$mid}_addbkwd'});
		});\n";
		$doc->addScriptDeclaration($slideit);
		$img_height +=6;
		$img_height1 = $img_height+2;
		//$img_height += 8;
		
		$c_top = (int) ($img_height - 16)/2 ; //18 = 22 - 6
		$slider_style = "\n#{$gallery_container} { width:{$gallery_box_width}px ; height:{$img_height}px ; margin:6px auto 6px; background:{$box_background}; padding:5px 0px 0px;; display:block; position:relative; }
#{$thumb_container}{ position:relative ; overflow:hidden ; width:{$thumb_box_width}px ; height:{$img_height1}px ; margin:0px auto 0px; }
#{$thumbs} { white-space:nowrap; display:block; position:relative; }
#{$thumbs} a { padding-left:2px; margin:0px; }
#{$thumbs} a img{ border:1px #333333 solid; }
.{$mid}_addfwd { display:block; position:absolute; cursor:pointer; width:25px; height:{$img_height}px; top:{$c_top}px; right:0px; background:url(".JURI::root()."modules/mod_youtubeplaylist/images/icon_next.png) no-repeat; }
.{$mid}_addbkwd { display:block; position:absolute; cursor:pointer; width:25px; height:{$img_height}px; top:{$c_top}px; left:2px; background:url(".JURI::root()."modules/mod_youtubeplaylist/images/icon_previous.png) no-repeat;}";
	$doc->addStyleDeclaration($slider_style);
		//if($string) $playing_list = $string.$playing_list;
/************************/		
			break;
			}
		return $playing_list;
			
	}

   //extra functions
function InitScriptAndStyle($theoptions){
	$mainframe = &JFactory::getApplication();
	$doc = &JFactory::getDocument();
	
	if(!$mainframe->get('loadswfobject')) { //Load only one time
		$doc->addScript(JURI::root(true).'/modules/mod_youtubeplaylist/js/swfobject.js'); //Ok done
		$doc->addScript(JURI::root(true).'/modules/mod_youtubeplaylist/js/jwplayer.js'); //Ok done	
		$doc->addScript(JURI::root(true).'/modules/mod_youtubeplaylist/js/player.js'); //Ok done		//youtubeplaylist		 
		$doc->addStyleSheet( JURI::root(true).'/modules/mod_youtubeplaylist/css/style.css' );
		$myscript = "\n".'var mod_youtubeplaylist_player = \''.JURI::root(true).'/modules/mod_youtubeplaylist/player/player.swf\';';
		$myscript .= "\n". 'var mod_youtubeplaylist_skin = \''.JURI::root(true).'/modules/mod_youtubeplaylist/player/skin/\';';
		$myscript .= "\nvar theOptions = Array();\nvar theCurrentPlayingId = Array();\nvar ajaxcontentId = '';\nvar paramsfilefolder = '".JURI::root(true)."/modules/mod_youtubeplaylist/params/';\n";
		
		$doc->addScriptDeclaration($myscript);		
		$mainframe->set( 'loadswfobject', true ); 
	}
	$myscript = "\ntheCurrentPlayingId['".$theoptions['mid']."']='".$theoptions['search']."';";
	if($theoptions['type']!='video'){
		$myscript .= "\ntheOptions['".$theoptions['mid']."ypage']='1';";
		$myscript .= " theOptions['".$theoptions['mid']."yresult']='".$theoptions['playlist_items']."';";
	}
	$doc->addScriptDeclaration($myscript);	
}
function InitDefaultPlaying(&$params, $defaultvideo, $theoptions){ 
		$doc = &JFactory::getDocument(); 
		//Default playing
		$theId = $theoptions['mid'];
		$width = $params->get('width');
		$height = $params->get('height');
		$theSkin = $params->get('playerskin',0);
		$theAutostart = $theoptions['autostart'];
		$theWrapper = $theId.'-wrapper';
		$thePlaceholder = $theId.'-placeholder';
		$playlist_position = $params->get('playlist_position','bottom');
		$playlist_size = $params->get('playlist_size','320');
		// $theoptions['playlist_items']; $playlist_items = $params->get('playlist_items','25');
		
	    $html = "\n\t<div id=\"{$theWrapper}\">\n\t\t<div id=\"{$thePlaceholder}\">".
		JText::_('Loading Player...')
		."\n\t\t</div>\n\t</div>";
		if($defaultvideo->type != 'video'){			
			$myscript = "\ntheOptions['".$theId."playlist_position']='{$playlist_position}';\n";
			$myscript .= "theOptions['".$theId."playlist_size']='{$playlist_size}';\n";
			$doc->addScriptDeclaration($myscript);
			}

		if($params->get('hd_jwplay_on',0)){			
			$myscript = "theOptions['".$theId."hdon'] = 'true';\n";
			$doc->addScriptDeclaration($myscript);
			}
		$html .= "\n\t<script type=\"text/javascript\">initPlayer('{$theId}', '{$defaultvideo->type}', '{$defaultvideo->video}', '{$width}', '{$height}', '{$theSkin}', '{$theAutostart}');</script>\n";
		return $html;
}
//modYoutubeplaylistHelper::showNPbutton2($mid, $params->get('playlist_np', 1 ) && ($params->get('playlist',0)>0))
function showNPbutton($mid, & $params, $style=' class="mod_youtubeplaylist_NP2"', $prev ='&lt;&lt; Previous page', $next = 'Next page &gt;&gt;', $separate = '&nbsp;' ){
	$html = '';
	if(@$style[0] != ' ') $style = ' '.$style;
	$show = $params->get('playlist_np', 1 );
	if($show == 1){
	$separate = $separate."<a href=\"javascript:void(0)\" onclick=\"jwplayer('{$mid}-placeholder').play();\">Play/Pause</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"jwplayer('{$mid}-placeholder').stop();\">Stop</a>".$separate;
	if($show) $html = '<div'.$style.'><a href="javascript:void(0)" onclick="Playingpreviuos(\''.$mid.'\'); return false;">'.$prev.'</a>'.$separate.'<a href="javascript:void(0)" onclick="Playingnext(\''.$mid.'\'); return false;">'.$next.'</a></div>';
	}elseif($show == 2){
	//Icon from http://somerandomdude.com/projects/bitcons/
		$html = '<div'.$style.'>'.$separate;
		$image_root = JURI::root(true).'/modules/mod_youtubeplaylist/images/icons';
		$buttons = array('first','previous','pause','play','stop','next','last');
		$control = array('Playingpreviuos(\''.$mid.'\')','playlistPrev()','play(false)','play(true)','stop()','playlistNext()','Playingnext(\''.$mid.'\')');
		$title = array('Previous page','Previous video','Pause','Play','Stop','Next video','Next page');
		$i = 0;
		$set = $params->get('playlist_np_color', 'tan' );
		foreach($buttons as $button){		
			
			if($i == 0 || $i== 6) $html .= "<a href=\"javascript:void(0)\" onclick=\"{$control[$i]}; return false;\">";
			else $html .= "<a href=\"javascript:void(0)\" onclick=\"jwplayer('{$mid}-placeholder').{$control[$i]}; return false;\">";
			$html .= "<img src=\"{$image_root}/{$set}/{$button}.png\" alt=\"$button\" title=\"{$title[$i]}\"/></a>\n";
			$i++;
			$html .= $separate;
		}
		$html .= '</div>';
	}
	return $html;
}
function get_1stthumbnail($page, $playlistid){
	if($page == 'user' || $page == 'user-playlist') $url = "http://gdata.youtube.com/feeds/api/users/{$playlistid}";
	else $url = "http://gdata.youtube.com/feeds/api/playlists/{$playlistid}?v=2";
	$html = modYoutubeplaylistHelper::loadhtml($url);
	$returnValue = preg_match('@media:thumbnail url=\'(.*?)\'@', $html, $matches);
	unset($html);
	if($matches) return $matches[1];
	else return 'http://www.youtube.com/img/pic_youtubelogo_123x63.gif';
}
function get_playlist_title($userid){
	$url = "http://gdata.youtube.com/feeds/api/users/{$userid}/playlists?v=2";
	$html = modYoutubeplaylistHelper::loadhtml($url);
	$returnValue = preg_match_all('/<yt:playlistId>(.*?)<\/yt:playlistId>/', $result, $matches);
	$returnValue = $returnValue && preg_match_all('/<title>(.*?)<\/title>/', $result, $matches2);
	array_shift($matches2[1]);
	unset($html);
	if($returnValue) return array($matches[1],$matches2[1]);
}

function getPattern($url, $type){
		switch($type){
		//0:video 1:Playlist 2:User 3:Combination
		case 'video':
			$id = modYoutubeplaylistHelper::getPatternFromUrl($url);
			break;
		case 'playlist':
			$id =  modYoutubeplaylistHelper::getPatternFromPUrl($url);
			break;	
		case 'user':
		case 'user-playlist':	
			$id = modYoutubeplaylistHelper::getPatternFromUserUrl($url);
			break;
		default:	
		}
	return $id;
}
protected function getPatternFromUrl($url)
{
	$url = $url.'&';
	//http://www.youtube.com/watch?v=wsP6P5B05A4&feature=feedu
	//http://www.youtube.com/watch?v=vxaGIMKdOfw&feature=player_embedded#!
	$pattern = '/v=(.+?)&+/';
	preg_match($pattern, $url, $matches);
	if(!@$matches[1]) { 
		//http://www.youtube.com/user/VOALearningEnglish#p/c/46706B4B3D2E6C5E/3/wsP6P5B05A4
		$pattern = '@&(.+?)\/+@';
		preg_match($pattern, strrev($url), $matches);
			if(!@$matches[1]) { 
				//wsP6P5B05A4
				$url = 'v='.$url;
				$pattern = '/v=(.+?)&+/';				
				preg_match($pattern, $url, $matches);
				}
			else $matches[1] = strrev ($matches[1]);
		}
	//echo $matches[1]; die;
	return (@$matches[1]);
}
/*
{
	$url = $url.'&';
	$pattern = '/v=(.+?)&+/';
	preg_match($pattern, $url, $matches);
	if(!@$matches[1]) { $url = 'v='.$url; preg_match($pattern, $url, $matches);}
	//echo $matches[1]; die;
	return (@$matches[1]);
}
*/
protected function getPatternFromPUrl($url){
	$findme   = 'http://';
	$pos = strpos($url, $findme);
	$data = $url;
	if ($pos !== false) {
		$url .= '/';
		$pattern = '@c\/(.+?)\/+@';
	//http://www.youtube.com/user/SuperSimpleSongs#g/c/028565C616627F50
	//http://www.youtube.com/user/SuperSimpleSongs#p/c/028565C616627F50/0/ZhODBFQ2-bQ
	//http://www.youtube.com/my_playlists?p=6EF8A28A655B431C
	//http://www.youtube.com/watch?v=vIs87n8cYNY&playnext=1&list=PL6EF8A28A655B431C
	//6EF8A28A655B431C - default setting
	//http://www.youtube.com/playlist?list=PLF9D072436E8F2DB8&feature=viewall

		preg_match($pattern, $url, $matches);
		if(!@$matches[1]) {
			$pattern = '@p=(.+?)\/+@';
			preg_match($pattern, $url, $matches);
					if(!@$matches[1]) {
					//http://www.youtube.com/playlist?list=PLF9D072436E8F2DB8&feature=viewall
					$pattern = '@list=PL(.+?)&+@';
					preg_match($pattern, $url, $matches);
						if(!@$matches[1]) {
						//http://www.youtube.com/watch?v=vIs87n8cYNY&playnext=1&list=PL6EF8A28A655B431C
							$pattern = '@list=PL(.+?)\/+@';
							preg_match($pattern, $url, $matches);
							
								if(!@$matches[1]) {
								//http://www.youtube.com/user/SuperSimpleSongs#grid/user/B532F4C0AEFCEC20
									$pattern = '@grid/user/(.+?)\/+@';
									preg_match($pattern, $url, $matches);
								}							
						}
				}			

				
			}
		if(@$matches[1] && (strlen($matches[1])==16)) $data = $matches[1];
	}
	//echo $matches[1]; die;
	return $data;
} 

protected function getPatternFromUserUrl($url){
	$findme   = 'http://';
	$pos = strpos($url, $findme);
	$data = $url;
	if ($pos !== false) {
		$url = strtr($url,'?','#'); //for ******
		if(strpos($url, '#')=== false) $url .= '#gdata';
		if(strpos($url, 'profile?user=')=== false) $pattern = '@user\/(.+?)\#+@';
		else $pattern = '@user=(.+?)\#+@';
//http://www.youtube.com/user/soujijess1149#p/c/F525122699DA4C6E
//http://www.youtube.com/user/soujijess1149
//http://www.youtube.com/profile?user=ComputerHistory#g/p
//[******]convert from '?' to '#' - strtr($url,'?','#');
//http://www.youtube.com/user/SuperSimpleSongs?feature=grec_index#g/c/B532F4C0AEFCEC20

		preg_match($pattern, $url, $matches);
		if(@$matches[1]) $data = $matches[1];
	}
	//echo $matches[1]; die;
	return $data;
} 	

public function loadhtml($url) {
    if (function_exists('curl_init')) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($curl);
		curl_close($curl);
    } else if (ini_get('allow_url_fopen') == true) {
		$html = file_get_contents ($url);
    } else {
      throw new Exception("Can't load data.");
    }
	return $html;
  }

 protected function sec2hms ($sec, $padHours = false) 
  {

    // start with a blank string
    $hms = "";
    
    // do the hours first: there are 3600 seconds in an hour, so if we divide
    // the total number of seconds by 3600 and throw away the remainder, we're
    // left with the number of hours in those seconds
    $hours = intval(intval($sec) / 3600); 

    // add hours to $hms (with a leading 0 if asked for)
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
          : $hours. ":";
    
    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($sec / 60) % 60); 

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($sec % 60); 

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
    
  }
  
function getVideo($vidID) { 
        // set video data feed URL 
        $feedURL = 'http://gdata.youtube.com/feeds/api/videos/'. $vidID;
         
	if (ini_get('allow_url_fopen') == true) {
		 $entry = simplexml_load_file($feedURL);
	}
	else if (function_exists('curl_init')) {		
        $ch = curl_init();    // initialize curl handle 
        curl_setopt($ch, CURLOPT_URL,$feedURL); // set url to post to 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); // times out after 4s 
         
        $result = curl_exec($ch); // run the whole process*/ 
		$entry = simplexml_load_string($result);
		}
	else {
      // Enable 'allow_url_fopen' or install cURL.
      throw new Exception("Can't load data.");
    }
        // read feed into SimpleXML object        
         
        // parse video entry 
        $video = modYoutubeplaylistHelper::parseVideoEntry($entry); 
         
        //These variables include the video information 
            $videos["title"] = stripslashes($video->title); 
            $videos["description"] = stripslashes($video->description); 
            $videos["thumbnail"] = stripslashes($video->thumbnailURL);
            $videos["length"] = modYoutubeplaylistHelper::sec2hms($video->length); 
            return $videos; 
    }
 protected function parseVideoEntry($entry) {       
      $obj= new stdClass; 
       
      // get nodes in media: namespace for media information 
      $media = $entry->children('http://search.yahoo.com/mrss/'); 
      $obj->title = $media->group->title; 
      $obj->description = $media->group->description; 
       
      // get video player URL 
      $attrs = $media->group->player->attributes(); 
      $obj->watchURL = $attrs['url'];  
       
      // get video thumbnail 
      $attrs = $media->group->thumbnail[0]->attributes(); 
      $obj->thumbnailURL = $attrs['url'];  
             
      // get <yt:duration> node for video length 
      $yt = $media->children('http://gdata.youtube.com/schemas/2007'); 
      $attrs = $yt->duration->attributes(); 
      $obj->length = $attrs['seconds'];  
       
      // get <yt:stats> node for viewer statistics 
      $yt = $entry->children('http://gdata.youtube.com/schemas/2007'); 
      $attrs = $yt->statistics->attributes(); 
      $obj->viewCount = $attrs['viewCount'];  
       
      // get <gd:rating> node for video ratings 
      $gd = $entry->children('http://schemas.google.com/g/2005');  
      if ($gd->rating) {  
        $attrs = $gd->rating->attributes(); 
        $obj->rating = $attrs['average'];  
      } else { 
        $obj->rating = 0;          
      } 
         
      // get <gd:comments> node for video comments 
      $gd = $entry->children('http://schemas.google.com/g/2005'); 
      if ($gd->comments->feedLink) {  
        $attrs = $gd->comments->feedLink->attributes(); 
        $obj->commentsURL = $attrs['href'];  
        $obj->commentsCount = $attrs['countHint'];  
      } 
       
      //Get the author 
      $obj->author = $entry->author->name; 
      $obj->authorURL = $entry->author->uri; 
       
       
      // get feed URL for video responses 
      $entry->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom'); 
      $nodeset = $entry->xpath("feed:link[@rel='http://gdata.youtube.com/schemas/ 
      2007#video.responses']");  
      if (count($nodeset) > 0) { 
        $obj->responsesURL = $nodeset[0]['href'];       
      } 
          
      // get feed URL for related videos 
      $entry->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom'); 
      $nodeset = $entry->xpath("feed:link[@rel='http://gdata.youtube.com/schemas/ 
      2007#video.related']");  
      if (count($nodeset) > 0) { 
        $obj->relatedURL = $nodeset[0]['href'];       
      } 
     
      // return object to caller   
      return $obj;       
    }
//WRITE CACHED DATA//
protected function write_params_itemid($mid, $youtube) {
	$page = $youtube->type;
	// Data to write	
	$youtube_data = "\n\t\$youtube = new stdClass;";
	$youtube_data .= "\n\t\$youtube->type = '{$youtube->type}';";
	$youtube_data .= "\n\t\$youtube->items = {$youtube->items};";
	if($youtube->types){
		$youtube_data .= "\n\t\$youtube->types = array(";
		$i = 0;
		foreach($youtube->types as $item) {
			if($i++ !=0) $youtube_data .= ',';
			$youtube_data .= "'{$item}'";
			}
		$youtube_data .= ");";
	} else $youtube_data .= "\n\t\$youtube->types = null;";
	$youtube_data .= "\n\t\$youtube->videos = array(";
	$i = 0;
	foreach($youtube->videos as $video) {
		if($i++ !=0) $youtube_data .= ',';
		$youtube_data .= "'{$video}'";
		}
	$youtube_data .= ");";
	$youtube_data .= "\n\t\$youtube->titles = array(";
	$i = 0;
	foreach($youtube->titles as $title) {
		if($i++ !=0) $youtube_data .= ',';
		$youtube_data .= "'{$title}'";
		}
	$youtube_data .= ");";
	$youtube_data .= "\n\t\$youtube->images = array(";
	$i = 0;
	foreach($youtube->images as $image) {
		if($i++ !=0) $youtube_data .= ',';
		$youtube_data .= "'{$image}'";
		}
	$youtube_data .= ");\n";
	
	//playlistid from an user
	if($page == 'user'){
		foreach($youtube->videos as $video) {
			modYoutubeplaylistHelper::write_params_user($video);
		}
	}
	//playlistid from an user
	if($page == 'combination'){
		$i = 0;
		foreach($youtube->types as $type) {
			if($type == 'user')
			modYoutubeplaylistHelper::write_params_user($youtube->videos[$i]);
			$i++;
		}
	}	
	$thedata = "<?php
	// Check to ensure this file is included in Joomla!
	defined( '_JEXEC' ) or die('Restricted access');".
	"\n{$youtube_data}\n?>";
	// Write the cache to the file
	$file = 'module_'.$mid.'.php';
	$fp = fopen(JPATH_SITE.DS. 'modules'.DS.'mod_youtubeplaylist'.DS.'params'.DS.$file, 'w');
	if ($fp)
	{
		fwrite($fp, $thedata);
	}
	fclose($fp);
}
//WRITE CACHED DATA FOR USER//
protected function write_params_user($user, $cached_time = 0) {
	$file = 'user_'.strtolower($user).'.php';
	$fullfile = JPATH_SITE.DS. 'modules'.DS.'mod_youtubeplaylist'.DS.'params'.DS.'user'.DS.$file;
	
	if(file_exists($fullfile) && $cached_time > 0 && (filectime($fullfile) > (time()-$cached_time))) return;
	
	//playlistid from an user
	//$playlist_title = modYoutubeplaylistHelper::get_playlist_title($video);
	$url = "http://gdata.youtube.com/feeds/api/users/{$user}/playlists?v=2";
	$html = modYoutubeplaylistHelper::loadhtml($url);
	$user = strtolower($user);
	$returnValue1 = preg_match_all('/<yt:playlistId>(.*?)<\/yt:playlistId>/', $html, $matches);
	$returnValue = $returnValue1 && preg_match_all('/<title>(.*?)<\/title>/', $html, $matches2);
	unset($html);
	$youtube_data = '';
	if($returnValue){
	// Data to write	
		$youtube_data = "\n\t\$youtube = new stdClass;";
		$youtube_data .= "\n\t\$youtube->type = 'playlist';";
		$youtube_data .= "\n\t\$youtube->types = null;";
		$youtube_data .= "\n\t\$youtube->items = {$returnValue1};";
		$youtube_data .= "\n\t\$youtube->videos = array(";	
		
		array_shift($matches2[1]);
		$i = 0;
		$t_images = '';
		foreach($matches[1] as $playlist) {
			if($i++ !=0) { $youtube_data .= ','; $t_images .=','; }
			$youtube_data .= "'{$playlist}'";
			$t_images .= '\'' . modYoutubeplaylistHelper::get_1stthumbnail('playlist',$playlist) .'\'';
			}
		$youtube_data .= ");\n";
		$youtube_data .= "\t\$youtube->titles = array(";
		$i = 0;
		foreach($matches2[1] as $title) {
			$title = htmlspecialchars (str_replace("'", "&rsquo;",$title),ENT_QUOTES);
			if($i++ !=0) $youtube_data .= ',';
			$youtube_data .= "'{$title}'";
			}
		$youtube_data .= ");\n";
		
		$youtube_data .= "\t\$youtube->images = array({$t_images});\n";
		}
	else {
		$youtube_data = "\n\t\$youtube = new stdClass;";
		$youtube_data .= "\n\t\$youtube->type = 'playlist';";
		$youtube_data .= "\n\t\$youtube->items = 0;";	
		$youtube_data .= "\n\t\$youtube->videos = \$youtube->titles = \$youtube->images = array();\n";
	}
	$thedata = "<?php
	// Check to ensure this file is included in Joomla!
	defined( '_JEXEC' ) or die('Restricted access');".
	"\n{$youtube_data}\n?>";

	// Write the cache to the file
	$fp = fopen($fullfile, 'w');
	if ($fp)
	{
		fwrite($fp, $thedata);
	}
	fclose($fp);				
}
function built_image_playlist_hidden(& $params, & $userlist, $defaultuser, $mid){
	$width	= $params->get('width','425');
	$height = $params->get('height','355');
	$theSkin =  $params->get('playerskin',0);
	$playing_list = '';
	$width3 = $params->get('imagewidth','50');
	$numimage3  = $params->get('numimage','4');
	$height3  = floor($width3*3/4);
	
	foreach($userlist as $user){
		$user = strtolower($user);
			if($defaultuser == $user) $style= ' style="display:block;" id="'.$mid.'-'.$user.'"'; 
				else $style= ' style="display:none;" id="'.$mid.'-'.$user.'"';
			
			$thefile = JPATH_SITE.DS. 'modules'.DS.'mod_youtubeplaylist'.DS.'params'.DS.'user'.DS.'user_'.strtolower($user).'.php';
			//JError::raiseWarning(500, $thefile);
			if(file_exists($thefile)) require $thefile; //DO NOT USE require_once
				else continue;
			
			//JError::raiseWarning(500, print_r($youtube,true));
			if($numimage3==0) $numimage3 =  $youtube->items;
		$playing_list .='<div class="mod_youtubeplaylist"'.$style.'>';
		for ($i = 0; $i < $youtube->items; $i ++) {
			$playing_list .='<a href="javascript:void(0)" onclick="initPlayer(\''.$mid.'\',\'playlist\',\''.$youtube->videos[$i].'\',\''.$width.'\',\''. $height.'\',\''.$theSkin.'\',\'true\'); document.getElementById(\''.$mid.'-youtubevideo-description\').innerHTML=\''.htmlspecialchars(JText::_('PLAYING_DESCRIPTION'),ENT_QUOTES).$youtube->titles[$i].'\'; return false;" title="'.$youtube->titles[$i].'"><img width="'.$width3.'" height="'.$height3.'" src="'.$youtube->images[$i].'" alt="" /></a>'; //alt = "" 
		 $k = $i + 1;
		if(($k % $numimage3) == 0) $playing_list .='<br />'; 
		}
		$playing_list .='</div>';
	}
	//if($mid=='m87') JError::raiseWarning(500, 'A:'.htmlspecialchars($playing_list));
	return $playing_list;
}
private function built_params(&$params, $mid){
	$db = & JFactory::getDBO();
	$mid = substr($mid, 1);
	$mid = intval($mid);
	if($mid > 0){
		//Reset status for update parameter of the plugin
		$params->set('cached_time', '-1');

		$query	= $db->getQuery(true);
		$query->update('#__modules');
		$query->set('params='.$db->Quote($params->toString()));
		$query->where('module='.$db->Quote('mod_youtubeplaylist'));
		$query->where('id='.$db->Quote($mid));
		$db->setQuery((string)$query);//(string) very importal 
		$db->query();
	}
}
protected function get_tag($theparams,$tag, $defaultvalue = null) {
	$passed = preg_match_all("|{$tag}[\s\v]*=[\s\v]*['\"](.*)['\"]|Ui",$theparams,$matches,PREG_PATTERN_ORDER);
	if ( $passed == 0) $passed = preg_match_all("|{$tag}[\s\v]*=[\s\v]*([\S]+)[\s\v]|Ui",$theparams,$matches,PREG_PATTERN_ORDER);
	if($passed) $defaultvalue = $matches[1][0];
	return $defaultvalue;
}
}
?>