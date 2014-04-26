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

$divID = "";
if ( $useACCSS )
{
	$document	= & JFactory::getDocument();
	$document->addStyleSheet(JURI::base(true).'/components/com_alphacontent/assets/css/alphacontent.css.css');
	$divID = " id=\"alphabeticalbar\"";
}

if ( $alphabeticalbar )
{
	echo "<div$divID><div align=\"center\"><p>" . $alphabeticalbar . "</p></div></div>";
} 
?>