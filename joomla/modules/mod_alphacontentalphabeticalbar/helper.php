<?php
/**
* @version		$Id: mod_alphacontentalphabeticalbar 2008-08-14 21:26:32 v1.0.0 $
* @package		AlphaContent for Joomla
* @copyright	Copyright (C) 2008. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAlphaContentAlphabeticalBarHelper {

	function getIndexHTML(&$params) {
		global $mainframe;
		
		$useACCSS 			 = intval($params->get('useAC_CSS'), 1);		
		$menuid		      	 = intval($params->get('itemidmenu'), 0);
		$menu 			     = JSite::getMenu();
		$paramsmenucomponent = $menu->getParams($menuid);
		
		if ( !$menuid ) return;		
		
		$url_alphacontent = "index.php?option=com_alphacontent&amp;Itemid=" . $menuid;

		$alphabeticalindex = @explode( ",", $paramsmenucomponent->get('alphabeticalindex') );
		
		$alphabeticalbar = modAlphaContentAlphabeticalBarHelper::getAlphabeticalBarModule( $alphabeticalindex, $paramsmenucomponent, $url_alphacontent );
		
		return array ( $alphabeticalbar, $useACCSS );
		
	}

	function getAlphabeticalBarModule( $ar_bar, $params, $url ) {
		global $options;
		
		// build alphabetical bar
		$alphabar = "";		
		
		$linkletter = $url . "&amp;letter=";
		
		// specials chars
		if ( $options['letter']=='#' ) {		
			$alphabar .= "\r\n<b>#</b>\r\n";
		} else {
			$alphabar .= "\r\n<a href=\"".JRoute::_($linkletter.urlencode("#")) . "\" title=\"#\">#</a>\r\n";
		}
		
		$alphabar .= stripslashes($params->get('seperatingchar'));
		
		// numbers
		if ( $options['letter']=='0-9' ) {		
			$alphabar .= "\r\n<b>0-9</b>\r\n";
		} else {
			$alphabar .= "\r\n<a href=\"".JRoute::_($linkletter."0-9") . "\" title=\"0-9\">0-9</a>\r\n";
		}
		
		// letters
		$tagbr = 0;
		for($i=0;$i<sizeof($ar_bar);$i++) {
			
			if ( $options['letter']==$ar_bar[$i] ) {
				$alphabar .= stripslashes($params->get('seperatingchar'));
				$alphabar .= "<b>" . $ar_bar[$i] . "</b>";
			} else {
				if ( $ar_bar[$i]!=strtolower('<br/>') && $ar_bar[$i]!=strtolower('<br />') ) {				
					if ( !$tagbr ) {
						$alphabar .= stripslashes($params->get('seperatingchar'));						
					} else $tagbr = 0;					
					$alphabar .= "<a href=\"" . JRoute::_($linkletter . $ar_bar[$i]) . "\" title=\"" . $ar_bar[$i] . "\">" . $ar_bar[$i] . "</a>";
				} else {
					$alphabar .= "<br />";
					$tagbr = 1;
				}
			}			
			$alphabar .= "\r\n";
		}
		
		return $alphabar;
		
	}

}
?>