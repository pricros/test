<?php
/*
 * @component AlphaContent
 * @copyright Copyright (C) 2005 - 2011 Bernard Gilly. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * AlphaContent Content Plugin
 */
class plgContentAlphacontent extends JPlugin
{

	function plgContentAlphacontent( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		if ( !@$article->id ) return;
		
		$view		= JRequest::getCmd('view');		

		JPlugin::loadLanguage( 'com_alphacontent' );
		
		@session_start();
		
		if ( @$_SESSION['acdirectory']!='' ){
			$selectedDirectory = @$_SESSION['acdirectory'];
		} else return;

		$id         = $article->id;
		$option     = JRequest::getVar ( 'option', '', 'GET', 'string' );
		$layout 	= JRequest::getVar ( 'layout', '', 'GET', 'string' );
		$Itemid 	= JRequest::getVar ( 'Itemid', 0, 'GET', 'int' );
		$ratingbar  = "";
		$googlemaps = "";
		
		$menus = &JSite::getMenu();
		if ( ($menus->getDefault()==$menus->getActive() && $option == 'com_content') || $option != 'com_content' ) return;

		// Get plugin info
		$plugin =& JPluginHelper::getPlugin('content', 'alphacontent');
		$pluginParams = new JParameter( $plugin->params );		
		
		$readytoshow = 0;
		$showonintro = $pluginParams->get( 'showonintro', 0 );
		if ( $option=='com_content' ) {
			switch ( $view ) {
				case 'article':
					if ( $pluginParams->get( 'enabled', 0 ) ) {
						$readytoshow = 1;
					}
					break;
				case 'frontpage':
					if ( $pluginParams->get( 'enabled' ) && $showonintro ) {
						$readytoshow = 1;				
					}
					break;
				case 'section':
				case 'article':
					if ( $pluginParams->get( 'enabled' ) && $showonintro && $layout=='blog' ) {
						$readytoshow = 1;				
					}
					break;
				default:
					$readytoshow = 0;
			}
		}
		
		$excludeID = $pluginParams->get( 'excludeID', '' );	
		$listexclude = @explode ( ",", $excludeID );		
		
		// Get AlphaContent default params
		$dparams = $this->setDirectoryParams();

		// check whether plugin has been unpublished or is an AlphaContent Directory
		if ( $selectedDirectory || $readytoshow ) {
			// get Ajax rating bar for AlphaContent
			if ( $dparams->get('activeglobalsystemrating') || $pluginParams->get( 'enabled', 0 ) ) {			
				switch ( $option ) {				
					case 'com_content':
					default:
						$component4rating = 'com_content';
				}			
				
				if ( !in_array ( $article->id, $listexclude ) ) {
					$document = & JFactory::getDocument();
					$document->addScript(JURI::base(true).'/components/com_alphacontent/assets/js/behavior.js');
					$document->addScript(JURI::base(true).'/components/com_alphacontent/assets/js/rating.js');
					$document->addStyleSheet(JURI::base(true).'/components/com_alphacontent/assets/css/rating.css');
					require_once (JPATH_SITE.DS.'components'.DS.'com_alphacontent'.DS.'assets'.DS.'includes'.DS.'alphacontent.drawrating.php' );
					$ratingbar = rating_bar( $id, $dparams->get('numstars', 5), $component4rating, $dparams->get('widthstars', 16), '', '', 0, 0, 1 );		
					$article->text = $ratingbar . $article->text;
				}
			}
		}
		
		// insert Google Maps link
		//if ( $dparams->get('apikeygooglemap') ) {
			$mapIsDefined = 0;								
			if ( preg_match('#{ALPHAGMAP=(.*)}#Uis', $article->text, $m) ) {				
				$a = explode("|", $m[1]);
				if ( count($a)==3 ) {
					// add style sheet
					$document	= & JFactory::getDocument();
					$document->addStyleSheet(JURI::base(true).'/components/com_alphacontent/assets/css/alphacontent.css');
					$thewidthmap  = $dparams->get('widthgooglemap', 400) + 20;
					$theheightmap = $dparams->get('widthgooglemap', 250) + 20;
					$status       = "status=no,toolbar=no,scrollbars=no,titlebar=no,menubar=no,resizable=no,width=".$thewidthmap.",height=".$theheightmap.",directories=no,location=no";
					$googlemaps   = "<a href=\"javascript:void window.open('index2.php?option=com_alphacontent&amp;task=viewmap&amp;la=".$a[0]."&amp;lo=".$a[1]."&amp;txt=".$a[2]."', 'win2', '$status');\">" . JTEXT::_('AC_MAP') . "</a>";	
				} else $googlemaps = "";
			}			
		//} 
		$article->text = preg_replace( " |{ALPHAGMAP=(.*)}| ", "", $article->text );
		$article->text .= '<p>' . $googlemaps . '</p>';
		
		// insert Report listing
		if ( $dparams->get('list_showreportlisting') && $selectedDirectory ) {
		
			$report="";
						
			$url  = 'index.php?option=com_alphacontent&task=report';
			
			$report_type = 'com_content';
			
			$url  .= '&type='.$report_type.'&id='.$id.'&tmpl=component';
			$status = 'width=400,height=300,menubar=yes,resizable=yes';		
			$text = JText::_('AC_REPORT');
			$attribsr['title'] = JText::_( 'AC_REPORT_THIS_LISTING' );
			$attribsr['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";		
			$report = JHTML::_('link', JRoute::_($url), $text, $attribsr);
			
			$article->text .= '<p><img src="images/M_images/edit_unpublished.png" title="" alt="" />&nbsp;' . $report . '</p>';
			
		}
		
		// show Sharethis widget in article
		if ( !in_array ( $article->id, $listexclude ) ) {			
			//$menus = &JSite::getMenu();
			if ( ($selectedDirectory || $pluginParams->get( 'enabled', 0 )) && !$params->get( 'intro_only' ) ) {	
				if ( !$params->get('show_intro') ) $article->text .= $this->showShareThisWidget( $dparams );
			}
		}
		
		// Display Related Items
		$displayListRelatedItem = "";
		if ( $selectedDirectory && !$params->get( 'intro_only' ) ) {
			// Get the menu item object			
			//$menus = &JSite::getMenu();
			$menus->setActive($selectedDirectory);
			$menu  = $menus->getActive();
			$menuname = $menu->name;
			$menuid = $menu->id;
			$dparams = $this->setDirectoryParams();
			
			if ( $dparams->get('showrelateditems') ) {			
				$relatedItems = $this->getRelatedItems($dparams->get('numrelateditems'), $selectedDirectory);
				if ( count($relatedItems) ) {				
					$displayListRelatedItem = "<div id=\"alpharelateditems\"><p>" . JTEXT::_('AC_RELATEDARTICLES') . "</p>";
					$displayListRelatedItem .= "<ul>";
					foreach ($relatedItems as $item) {
						$displayListRelatedItem .= "<li>";	
						$displayListRelatedItem .= $item->created . " - ";
						$displayListRelatedItem .= "<a href=\"" . $item->route . "\">";
						$displayListRelatedItem .= $item->title . "</a>";
						$displayListRelatedItem .= "</li>";
					}
					$displayListRelatedItem .= "</ul></div>";
				}
				$article->text .= $displayListRelatedItem;
			}
		}		
	}

	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		$app = JFactory::getApplication();
		
		$pathway =& $app->getPathway();
		
		JPlugin::loadLanguage( 'com_alphacontent' );
		
		@session_start();
		if ( @$_SESSION['acdirectory'] ){
			$selectedDirectory = @$_SESSION['acdirectory'];
		} else return;

		$catid = $article->catid;
				
		$url = "index.php?option=com_alphacontent";
		
		$result[] = null;
		
		if ( $selectedDirectory && !$params->get( 'intro_only' ) ) {
			// Get the menu item object			
			$menus = &JSite::getMenu();
			$menus->setActive($selectedDirectory);
			$menu  = $menus->getActive();
			$menuname = $menu->name;
			$menuid = $menu->id;
			
			$menuparams = $menus->getParams($menuid);
			
			$dparams = $this->setDirectoryParams();
			
			$db	=& JFactory::getDBO();
			$query = "SELECT c.id, c.title, c.image, c.description, c.section, s.title AS sectiontitle FROM #__categories AS c, #__sections AS s WHERE c.id = '" . $catid . "' AND c.section=s.id";
			$db->setQuery( $query );
			$resultcat = $db->loadObjectList();
			
			if ( $resultcat ) {
				$result[0]->idsection      = $resultcat[0]->section;
				$result[0]->titlesection   = $resultcat[0]->sectiontitle;
				$result[0]->catid		   = $resultcat[0]->id;
				$result[0]->currentcat     = $resultcat[0]->title;
				$result[0]->imagecat       = $resultcat[0]->image;
				$result[0]->descriptioncat = $resultcat[0]->description;
			} else {
				// uncategorized
				$result[0]->idsection      = '0';
				$result[0]->titlesection   = JText::_( 'AC_UNCATEGORIZED' );
				$result[0]->catid		   = '0';
				$result[0]->currentcat     = JText::_( 'AC_UNCATEGORIZED' );
				$result[0]->imagecat       = $dparams->get('imageuncategorizedsection');
				$result[0]->descriptioncat = JText::_('AC_DESCRIPTION_UNCATEGORIZED');
			}
			
			// add style sheet
			$document	= & JFactory::getDocument();
			$document->addStyleSheet(JURI::base(true).'/components/com_alphacontent/assets/css/alphacontent.css');
			
			// Title page
			if ( $menuparams->def( 'show_page_title', 1 ) ) {
			?>
			<div class="componentheading<?php echo $menuparams->get( 'pageclass_sfx' ); ?>">
				<?php
				$page_title = ($menuparams->get('page_title'))? $menuparams->get('page_title') : $menuname ;
				echo $page_title;
				?>
			</div>
			<?php 
			}

			// Alphabetical bar
			if ( $menuparams->get('showalphabeticalbar') ) {
				$ar_bar = explode (",", $menuparams->get('alphabeticalindex') );
				$alphabeticalbar = $this->getAlphabeticalBar( $ar_bar, $dparams, $url, $menuid );
				echo $alphabeticalbar;
			}			
			
			// AlphaContent pathway
			$ac_patwhay = $this->ac_pathway( $url, $result, $menuname, $menuid );						
			echo $ac_patwhay;
			
			// Joomla pathway
			$catid = JRequest::getVar ( 'catid', 0, 'GET', 'int' );
			$source = "&amp;Itemid=" . $menuid ;
			$sectionname = $result[0]->titlesection;
			$catname = $result[0]->currentcat;	
			$urlsection = $url . "&amp;section=".$result[0]->idsection . $source ;
			$urlcat = $urlsection . "&amp;category=" . $catid . $source ;			
			
			//$pathway->addItem($menuname, $url . $source );
			$pathway->addItem($sectionname, $urlsection);	
			$pathway->addItem($catname, $urlcat);
			$pathway->addItem($article->title, '');
			
			/* 
			If you want showing also the current category info, you can remove comment before the line below
			Important: Not loading plugins content for the category description 
			comment the line -> echo $this->showShareThisWidget( $dparams );
			*/
			// $this->show_category( $url, $result, $dparams ) ;			
						
			echo $this->showShareThisWidget( $dparams );
			
			echo "<br />";
			$_SESSION['acdirectory']='';
			//unset($_SESSION['acdirectory']);
		}
		
	}
	
	function setDirectoryParams() {

		$app = JFactory::getApplication();
		
		// Get general component configuration
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphacontent'.DS.'configuration'.DS.'configuration.php' );
		$alphacontentparams = new alphaConfiguration();
		
		// Get the page/component configuration
		$directoryparams = &$app->getParams();
		
		$directoryparams->def( 'list_homeresult', $alphacontentparams->list_homeresult );
		$directoryparams->def( 'list_featuredID', $alphacontentparams->list_featuredID );
		$directoryparams->def( 'list_numcols', $alphacontentparams->list_numcols );		
		$directoryparams->def( 'list_introstyle', $alphacontentparams->list_introstyle );		
		$directoryparams->def( 'list_titlelinkable', $alphacontentparams->list_titlelinkable );
		$directoryparams->def( 'list_numindex', $alphacontentparams->list_numindex );
		$directoryparams->def( 'list_iconnew', $alphacontentparams->list_iconnew );
		$directoryparams->def( 'list_iconhot', $alphacontentparams->list_iconhot );
		$directoryparams->def( 'list_showdate', $alphacontentparams->list_showdate );
		$directoryparams->def( 'list_formatdate', $alphacontentparams->list_formatdate );
		$directoryparams->def( 'list_showauthor', $alphacontentparams->list_showauthor );
		$directoryparams->def( 'list_showsectioncategory', $alphacontentparams->list_showsectioncategory );
		$directoryparams->def( 'list_showhits', $alphacontentparams->list_showhits );
		$directoryparams->def( 'list_shownumcomments', $alphacontentparams->list_shownumcomments );
		$directoryparams->def( 'list_commentsystem', $alphacontentparams->list_commentsystem );
		$directoryparams->def( 'list_showprint', $alphacontentparams->list_showprint );
		$directoryparams->def( 'list_showpdf', $alphacontentparams->list_showpdf );
		$directoryparams->def( 'list_showemail', $alphacontentparams->list_showemail );
		$directoryparams->def( 'list_showreportlisting', $alphacontentparams->list_showreportlisting );		
		$directoryparams->def( 'list_email_administrator', $alphacontentparams->list_email_administrator );		
		$directoryparams->def( 'list_showreadmore', $alphacontentparams->list_showreadmore );		
		$directoryparams->def( 'list_showlinkmap', $alphacontentparams->list_showlinkmap );
		$directoryparams->def( 'list_shownumberpagetotal', $alphacontentparams->list_shownumberpagetotal );		
		$directoryparams->def( 'list_resultperpage', $alphacontentparams->list_resultperpage );
		$directoryparams->def( 'list_showsearchbox', $alphacontentparams->list_showsearchbox );
		$directoryparams->def( 'list_showsearchboxbutton', $alphacontentparams->list_showsearchboxbutton );
		$directoryparams->def( 'list_showorderinglist', $alphacontentparams->list_showorderinglist );
		$directoryparams->def( 'list_defaultordering', $alphacontentparams->list_defaultordering );
		$directoryparams->def( 'list_showimage', $alphacontentparams->list_showimage );
		$directoryparams->def( 'list_imageposition', $alphacontentparams->list_imageposition );
		$directoryparams->def( 'list_widthimage', $alphacontentparams->list_widthimage );
		$directoryparams->def( 'list_usePhpThumb', $alphacontentparams->list_usePhpThumb );
		$directoryparams->def( 'list_effectimage', $alphacontentparams->list_effectimage );
		$directoryparams->def( 'apikeygooglemap', $alphacontentparams->apikeygooglemap );
		$directoryparams->def( 'zoomlevel', $alphacontentparams->zoomlevel );
		$directoryparams->def( 'widthgooglemap', $alphacontentparams->widthgooglemap );
		$directoryparams->def( 'heightgooglemap', $alphacontentparams->heightgooglemap );
		$directoryparams->def( 'showmaptypemenu', $alphacontentparams->showmaptypemenu );
		$directoryparams->def( 'showmapcontrolsmenu', $alphacontentparams->showmapcontrolsmenu );
		$directoryparams->def( 'activeglobalsystemrating', $alphacontentparams->activeglobalsystemrating );
		$directoryparams->def( 'numstars', $alphacontentparams->numstars );
		$directoryparams->def( 'widthstars', $alphacontentparams->widthstars );
		$directoryparams->def( 'showsharethis', $alphacontentparams->showsharethis );
		$directoryparams->def( 'sharethiscode', $alphacontentparams->sharethiscode );	
		$directoryparams->def( 'list_keywebsnapr', $alphacontentparams->list_keywebsnapr );
		$directoryparams->def( 'list_sizewebsnapr', $alphacontentparams->list_sizewebsnapr );
		$directoryparams->def( 'list_keyartviper', $alphacontentparams->list_keyartviper );

		return $directoryparams;
	}
	
	/*
	// If you want showing also the current category info, you can remove comment before this text
	// Important: Not load plugins content for the category description 
	
	function show_category( $url, $directory, $dparams ) {
	
	// include utils
	require_once (JPATH_SITE.DS.'components'.DS.'com_alphacontent'.DS.'assets'.DS.'includes'.DS.'alphacontent.functions.php' );
	?>
	<div id="alphacategory">
	<?php
		$thecategory = JTEXT::_( $directory[0]->currentcat );
		// Show image section
		if ( $dparams->get('showimagecategory') ) {
			$thecategory .= insertImageDirectory( $directory[0]->imagecat, $directory[0]->currentcat, $dparams->get('widthimagesectioncat') );		
		}
		echo "<span class=\"ac_title_section_directory\">" . $thecategory . "</span> "
			 . "<p class=\"ac_category_description\">" . $directory[0]->descriptioncat . "</p>"
			 ;
	
		echo showShareThisWidget( $dparams );
		echo "<div style=\"clear:both;\"></div>";
	?>
	</div>
	<?php
	}
	*/
	
	function ac_pathway( $url, $directory, $menuname, $_itemid ) {
	
		// Create a pathway for go back to the directory
		$alphaPathway	= "<div id=\"alphapathway\"><p>";
		$source 		= "&amp;Itemid=" . $_itemid ;
		$sectionname	= $directory[0]->titlesection;
		$catname 		= $directory[0]->currentcat;	
		$urlpathway 	= $url . "&amp;section=".$directory[0]->idsection . "&amp;Itemid=" . $_itemid ;
		$urlcat 		= $url . "&amp;section=".$directory[0]->idsection . "&amp;category=" . $directory[0]->catid . "&amp;Itemid=" . $_itemid ; 
		if ( $directory[0]->catid ) {
			$alphaPathway .= "<a href=\"" . JRoute::_($url . $source) . "\">" . $menuname . "</a> &raquo; <a href=\"" . JRoute::_($urlpathway) . "\">" . $sectionname . "</a> &raquo; <a href=\"" . JRoute::_($urlcat) . "\">" . $catname . "</a>";		
		} else 	$alphaPathway .= "<a href=\"" . JRoute::_($url . $source) . "\">" . $menuname . "</a> &raquo; <a href=\"" . JRoute::_($urlpathway) . "\">" . $sectionname . "</a> &raquo; " . $catname ;		

		$alphaPathway .= "</p></div>";
				
		return  $alphaPathway;			
	
	}	
	
	function getAlphabeticalBar( $ar_bar, $dparams, $url, $_itemid ) {
	
		// build alphabetical bar
		$alphabar = "<div id=\"alphabeticalbar\"><div align=\"center\"><p>";				
		
		$linkletter = $url . "&amp;letter=";
		
		// specials chars
		$alphabar .= "\r\n<a href=\"".JRoute::_($linkletter . urlencode("#") . "&amp;Itemid=" . $_itemid ) . "\" title=\"#\">#</a>\r\n";
		
		$alphabar .= stripslashes($dparams->get('seperatingchar'));

		// numbers
		$alphabar .= "\r\n<a href=\"".JRoute::_($linkletter . "0-9" . "&amp;Itemid=" . $_itemid) . "\" title=\"0-9\">0-9</a>\r\n";
		
		// letters
		$tagbr = 0;
		for($i=0;$i<sizeof($ar_bar);$i++) {
			if ( $ar_bar[$i]!=strtolower('<br/>') && $ar_bar[$i]!=strtolower('<br />') ) {				
				if ( !$tagbr ) {
					$alphabar .= stripslashes($dparams->get('seperatingchar'));						
				} else $tagbr = 0;					
				$alphabar .= "<a href=\"" . JRoute::_($linkletter . $ar_bar[$i] . "&amp;Itemid=" . $_itemid ) . "\" title=\"" . $ar_bar[$i] . "\">" . $ar_bar[$i] . "</a>";
			} else {
				$alphabar .= "<br />";
				$tagbr = 1;
			}		
			$alphabar .= "\r\n";
		}
		$alphabar .= "</p></div></div>";
		
		return $alphabar;
	}
	
	
	function getRelatedItems( $limiter=5, $selectedDirectory ) {

		$db					=& JFactory::getDBO();
		$user				=& JFactory::getUser();

		$option				= JRequest::getCmd('option');
		$view				= JRequest::getCmd('view');

		$temp				= JRequest::getString('id');
		$temp				= explode(':', $temp);
		$id					= $temp[0];

		$showDate			= 1;

		$nullDate			= $db->getNullDate();


		$date =& JFactory::getDate();
		$now  = $date->toMySQL();

		$related			= array();

		if ($option == 'com_content' && $view == 'article' && $id)
		{
			// select the meta keywords from the item
			$query = 'SELECT metakey' .
					' FROM #__content' .
					' WHERE id = '.(int) $id;
			$db->setQuery($query);

			if ($metakey = trim($db->loadResult()))
			{
				// explode the meta keys on a comma
				$keys = explode(',', $metakey);
				$likes = array ();

				// assemble any non-blank word(s)
				foreach ($keys as $key)
				{
					$key = trim($key);
					if ($key) {
						$likes[] = $db->getEscaped($key);
					}
				}

				if (count($likes))
				{
					// select other items based on the metakey field 'like' the keys found
					$query = 'SELECT a.id, a.title, DATE_FORMAT(a.created, "%Y-%m-%d") AS created, a.sectionid, a.catid, cc.access AS cat_access, s.access AS sec_access, cc.published AS cat_state, s.published AS sec_state,' .
							' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
							' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
							' FROM #__content AS a' .
							' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' .
							' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
							' LEFT JOIN #__sections AS s ON s.id = a.sectionid' .
							' WHERE a.id != '.(int) $id .
							' AND a.state = 1' .
							' AND a.access <= ' .(int) $user->get('aid', 0) .
							' AND ( a.metakey LIKE "%'.implode('%" OR a.metakey LIKE "%', $likes).'%" )' .
							' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
							' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )' .
							' ORDER BY a.created DESC' .
							' LIMIT ' . $limiter;
					$db->setQuery($query);
					$temp = $db->loadObjectList();

					if (count($temp))
					{
						foreach ($temp as $row)
						{
							if (($row->cat_state == 1 || $row->cat_state == '') && ($row->sec_state == 1 || $row->sec_state == '') && ($row->cat_access <= $user->get('aid', 0) || $row->cat_access == '') && ($row->sec_access <= $user->get('aid', 0) || $row->sec_access == ''))
							{
								//$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid) . "&amp;directory=" . $selectedDirectory);
								$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));

								$related[] = $row;
							}
						}
					}
					unset ($temp);
				}
			}
		}
		return $related;
	}
	
	function showShareThisWidget( $dparams ) {	
		$sharethis = ( $dparams->get('showsharethis')=='1' || $dparams->get('showsharethiswidget')=='1' ) ? '<p>' . stripslashes( $dparams->get('sharethiscode') ) . '</p>' : '' ;
		return $sharethis;	
	}
}
?>