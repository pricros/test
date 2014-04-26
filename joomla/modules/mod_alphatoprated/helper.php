<?php
/**
* @version		$Id: mod_alphatoprated.php 2010-02-02 v1.0.2 $
* @package		AlphaContent for Joomla
* @copyright	Copyright (C) 2008-2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAlphaTopRatedHelper {

	function getList(&$params) {
		global $mainframe;

		$db			      =& JFactory::getDBO();
		$user		      =& JFactory::getUser();
		
		$count		      = intval($params->get('count', 5));
		$secid		      = trim($params->get('secid'));
		$catid		      = trim($params->get('catid'));
		$menuid		      = intval($params->get('itemidmenu'), 0);
		$componentused    = trim($params->get('componentused', 'com_content'));
		$showstars        = intval($params->get('showstars', 1));
		$showvotecount    = intval($params->get('showvotecount', 1));
		$showvotevalue    = intval($params->get('showvotevalue', 1));
		$limitcharstitle  = intval($params->get('limitcharstitle', 200));    
		$aid	   	      = $user->get('aid', 0);
		
		if ( !$menuid ) return;
		
		// Get general component configuration
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphacontent'.DS.'configuration'.DS.'configuration.php' );
		$alphacontentparams = new alphaConfiguration();
		
		$params->def( 'numstars', $alphacontentparams->numstars );
		$params->def( 'widthstars', $alphacontentparams->widthstars );			
		
		$nullDate	= $db->getNullDate();
		$date =& JFactory::getDate();
		$now  = $date->toMySQL();
		
		$queryRating  = " ROUND(ar.total_value/ar.total_votes) AS rating, ar.total_value/ar.total_votes AS realrating, ar.total_votes AS rating_count";
		$queryRating2 = " LEFT JOIN #__alpha_rating AS ar ON a.id = ar.id AND ar.component='".$componentused."' AND ar.cid='0' AND ar.rid='0'";
		
		if ($secid) {
			$ids = explode( ',', $secid );
			JArrayHelper::toInteger( $ids );
			$wheres[] = ' AND (s.id=' . implode( ' OR s.id=', $ids ) . ')';
		}
		
		if ($catid) {
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$wheres[] = ' AND (cc.id=' . implode( ' OR cc.id=', $ids ) . ')';
		}
		
		switch ( $componentused ) {
		
			case "com_weblinks":
				// published
				$wheres[] = " a.published = '1' AND a.approved = '1'";
		
				$query = "SELECT a.id, a.title," .
						" CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT(':', a.alias) ELSE '' END AS slug,".
						" a.catid as catslug,".
						" CONCAT('index.php?option=com_weblinks&view=weblink&id=', a.id) AS href,".
						$queryRating .
						" FROM #__weblinks AS a" .
						" LEFT JOIN #__categories AS cc ON a.catid = cc.id" .
						$queryRating2 .
						" WHERE " . implode( " AND ", $wheres ).
						" ORDER BY realrating DESC";
				break;
				
			case "com_contact":
				// published
				$wheres[] = " a.published = '1'";
				$query = "SELECT a.id, a.`name` AS title," .
						" CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT(':', a.alias) ELSE '' END AS slug,".
						" CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(':', cc.id, cc.alias) ELSE '' END as catslug,".
						" CONCAT('index.php?option=com_contact&view=contact&id=', a.id) AS href,".
						$queryRating .
						" FROM #__contact_details AS a" .
						" LEFT JOIN #__categories AS cc ON a.catid = cc.id" .
						" LEFT JOIN #__groups AS g ON a.access = g.id".
						$queryRating2 .
						" WHERE " . implode( " AND ", $wheres ).
						" ORDER BY realrating DESC";			
				break;
				
			case "com_content":
			default:
			$state = " (a.state = '1')";	
			// published
			if (!$user->authorize('com_content', 'edit', 'content', 'all'))	{
				$wherepublish  = ' ( ';
				$wherepublish .= ' ( a.created_by = ' . (int) $user->id . ' ) ';
				$wherepublish .= '   OR ';
				$wherepublish .= $state .
							' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
							' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )';
				$wherepublish .= ' ) ';
				$wheres[] = $wherepublish;
			}			 
			if ($user->aid !== null) {
				$wheres[] = " a.access <= " . (int) $user->aid;
			}
			$query = "SELECT a.id, a.title,".
					" CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT(':', a.alias) ELSE '' END as slug," .
					" CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(':', cc.id, cc.alias) ELSE a.catid END as catslug," .
					" CONCAT('index.php?option=com_content&view=article&id=', a.id) AS href," .
					$queryRating .
					" FROM #__content AS a" .
					" LEFT JOIN #__sections AS s ON a.sectionid = s.id" .
					" LEFT JOIN #__categories AS cc ON a.catid = cc.id" .
					" LEFT JOIN #__users AS u ON u.id = a.created_by" .
					" LEFT JOIN #__groups AS g ON a.access = g.id" .
					$queryRating2 .
					" WHERE " . implode( " AND ", $wheres ) .
					" ORDER BY realrating DESC";
		}
				
		
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
		
		$i		= 0;
		$lists	= array();
		
		@session_start();
		$_SESSION['acdirectory'] = $menuid;
		
		if ( $rows ) 
		{
			foreach ( $rows as $row )
			{
				$lists[$i]->id = $row->id; 
				$lists[$i]->rating = "";
				$sluggy = $row->slug;
				if ( $row->catslug && $sluggy!='' ) $sluggy .= "&amp;catid=" . $row->catslug;
				//$sluggy .= "&amp;directory=" . $menuid;
				$sluggy .= "&amp;Itemid=" . $menuid;
				$lists[$i]->text = "<a href=\"". JRoute::_( $row->href.$sluggy ) ."\">" . htmlspecialchars( modAlphaTopRatedHelper::_ac_ATR_Substr($row->title, $limitcharstitle ) ) . "</a>";		
				$lists[$i]->component = $componentused;
				if ( !$showstars && $showvotevalue ) {
					$lists[$i]->rating .= $row->rating . "/" . $alphacontentparams->numstars;
				}
				if ( !$showstars && $showvotecount ) {
					$voting = ($row->rating_count>1) ? JText::_('ATRVOTES') : JText::_('ATRVOTE') ;
					$lists[$i]->rating .= " (" . $row->rating_count . " " . $voting . ")";
				}
				$i++;
			}
		}

		return $lists;
		
	}
	
	function _ac_ATR_Substr( $text, $length=200 ) {
		if ( strlen($text) > $length ) {     
			$text = substr( $text, 0, $length );
			$blankpos = strrpos( $text, ' ' );    
			$text = substr( $text, 0, $blankpos );    
			$text .= "...";
		}  
		return $text;  
	}
}
?>