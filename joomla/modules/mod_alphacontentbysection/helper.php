<?php
/**
* @version		$Id: mod_alphacontentbysection 2008-05-26 v1.0.0 $
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

class modAlphaContentBySectionHelper {

	function getList(&$params) {
		global $mainframe;
		
		$db			      =& JFactory::getDBO();
		$user		      =& JFactory::getUser();
		
		JPlugin::loadLanguage( 'com_alphacontent' );		

		$menuid		      = intval($params->get('itemidmenu'), 0);
		$componentused    = trim($params->get('componentused', 'com_content'));
		$secid		      = trim($params->get('secid'));		
		
		if ( $secid ) {
			$ids = explode( ',', $secid );
			JArrayHelper::toInteger( $ids );
			$secid = ' AND (id=' . implode( ' OR id=', $ids ) . ')';
		}
		
		$aid	   	      = $user->get('aid', 0);
		
		$nullDate	= $db->getNullDate();
		$date =& JFactory::getDate();
		$now  = $date->toMySQL();		
		
		if ( !$menuid ) return;
		
		$menu = JSite::getMenu();
		$paramsmenucomponent = $menu->getParams($menuid);
		
		$url = "index.php?option=com_alphacontent";			
		
		$sort_sections = $paramsmenucomponent->get('orderingsectioncat');
		
		switch ( $componentused ) {
		
			case "com_weblinks":
				$listsections[]=null;
				$listsections[0]->id    = 'com_weblinks';
				$listsections[0]->title =  JText::_('AC_WEBLINKS');
				$listsections[0]->image = $paramsmenucomponent->get('imageweblinkssection');
				break;
				
			case "com_contact":
				$listsections[]=null;
				$listsections[0]->id    = 'com_contact_details';
				$listsections[0]->title =  JText::_('AC_CONTACTS');
				$listsections[0]->image = $paramsmenucomponent->get('imagecontactsection');
				break;
				
			case "com_content":
			default:
				// Sections
				$query = "SELECT id, title, image"
				. " FROM #__sections"
				. " WHERE published = '1' "			
				. " AND access <= '$aid'"
				. $secid
				. " ORDER BY $sort_sections";
				$db->setQuery( $query );
				$listsections = $db->loadObjectList();
		}
		modAlphaContentBySectionHelper::getSections($url, $params, $listsections, $sort_sections);			
		
	}
	
	function getSections($url, &$params, &$listsections, $sort_sections) {
		global $mainframe;
		
		$db			      =& JFactory::getDBO();
		$user		      =& JFactory::getUser();
		
		$count		      = intval($params->get('count', 5));
		$catid		      = trim($params->get('catid'));
		$menuid		      = intval($params->get('itemidmenu'), 0);
		$componentused    = trim($params->get('componentused', 'com_content'));
		$show_categories  = intval($params->get('show_categories', 1));
		$link_on_categorie= intval($params->get('link_on_categorie', 1));
		$num_categories	  = trim($params->get('num_categories', ''));
		$showimage		  = intval($params->get('showimage', 1));
		$img_width		  = intval($params->get('img_width', 48));
		$img_borderwidth  = intval($params->get('img_borderwidth', 1));
		$img_colorborder  = trim($params->get('img_colorborder', 'CECECE'));
		$img_bgcolor	  = trim($params->get('img_bgcolor', 'FFFFFF'));
 
		$aid	   	      = $user->get('aid', 0);
		
		if ($catid) {
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$catid = ' AND (id=' . implode( ' OR id=', $ids ) . ')';
		}
		
		if ( count($listsections) ) {
			for ( $i=0, $n=count($listsections); $i < $n; $i++ ){	
			
				$row = $listsections[$i];	
				
				switch ($row->id) {
					case 'com_weblinks':
						$link = $url . "&amp;section=weblinks&amp;Itemid=" . $menuid ;
						break;
					case 'com_contact_details':
						$link = $url . "&amp;section=contacts&amp;Itemid=" . $menuid ;
						break;
					default:
						$link = $url . "&amp;section=". $row->id . "&amp;Itemid=" . $menuid ;
						break;

				}
				
				$linkTitle = "<a href='" . JRoute::_($link) . "'><strong>" . htmlspecialchars($row->title) . "</strong></a>";
				
				?>
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				  <tr>
				  <?php 
				  if ( $showimage ) { 
					$constructdiv ="<div style=\"text-align:center;width:".$img_width."px;height:".$img_width."px;border-width:".$img_borderwidth."px;border-color:#".str_replace('#', '', $img_colorborder ).";border-style:solid;background-color:#".str_replace('#', '', $img_bgcolor ).";\">";
					$enddiv = "</div>";
					
					$max_width = $img_width - ( $img_borderwidth * 2 );
				
					$imagepath = JPATH_SITE . "/images/stories/".$row->image;		
					$imagelink = JURI::base(true)."/images/stories/".$row->image;		
			
					$dim=@getimagesize( $imagepath ); 
					$largeur = $dim[0];
					$hauteur = $dim[1];			
					
					if ( $largeur>$hauteur ){
						if ( $largeur>$max_width ){   
							$reduire=$max_width/$largeur;   
							$largeur=$max_width;   
							$hauteur=ceil($hauteur*$reduire);   
						}   	
					}elseif( $hauteur>$largeur ){
						if ( $hauteur>$max_width ){   
							$reduire=$max_width/$hauteur;   
							$hauteur=$max_width;   
							$largeur=ceil($largeur*$reduire);
						}	   
					}elseif( $largeur==$hauteur ){
							$largeur=$max_width;   
							$hauteur=$max_width;
					}
					
					if ( $max_width-$hauteur == 0 ){
						$margintop = 0;
					}else{
						$margintop = ( $max_width-$hauteur )/2;
					}		
					
					$img = "&nbsp;";
					if ( @file_exists( $imagepath ) && $row->image!='' ){ 
						$img = "<img src='".$imagelink."' width='".$largeur."' heigth='".$hauteur."' style='margin-top:".$margintop."px' border='0' alt='' />";
					}
				  ?>
					<td width="<?php echo $img_width ; ?>" valign="top">
					<?php 
					echo "<a href='" . JRoute::_($link) . "'>" . $constructdiv . $img . $enddiv . "</a>";
					?>
					</td>
					<td class="small" width="6">&nbsp;</td>
				  <?php } // end if show image ?>		  	
					<td valign="top">
					<?php 
					echo $linkTitle ;
					
					// Categories
					if ( $show_categories ) {
					
						$query = "SELECT id, title"
						. "\nFROM #__categories"
						. "\nWHERE published = '1'"
						. "\nAND section = '$row->id'"
						. "\nAND access <= '$aid'"
						. $catid;
						$db->setQuery( $query );
						$listcategs = $db->loadObjectList();
						$total = count($listcategs);
					
						$query = "SELECT id, title"
						. "\nFROM #__categories"
						. "\nWHERE published = '1'"
						. "\nAND section = '$row->id'"
						. "\nAND access <= '$aid'"
						. $catid
						. "\nORDER BY $sort_sections $num_categories";
						$db->setQuery( $query );
						$listcategs = $db->loadObjectList();
				
						if ( count($listcategs) ) {
							echo "<br />";
							echo "<div class='small'>";
							for ($ii=0, $nn=count($listcategs); $ii < $nn; $ii++) {
								$yrow = $listcategs[$ii];
								if ( $link_on_categorie ) {
									$linksouscat = $link . "&amp;category=".$yrow->id."&amp;Itemid=" . $menuid ;
									echo "<a href='".JRoute::_($linksouscat)."'>";
								}
								echo htmlspecialchars($yrow->title);	
								if ( $link_on_categorie ) {
									echo "</a>";
								}
								echo ", ";
							}
							if ($num_categories!='' ) {
								if ( $total > intval(str_replace('LIMIT ', '', $num_categories))) {
									echo "<a href='" . JRoute::_($link) . "'>...</a>";									
								}
							}
							echo "</div>";							
						}
					 }
					?>
					</td>
				  </tr>
				</table>
				<br />
				<?php
			}
		}	
	}	
}
?>