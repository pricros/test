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

$document	= & JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_alphacontent/assets/css/rating.css');
require_once (JPATH_SITE.DS.'components'.DS.'com_alphacontent'.DS.'assets'.DS.'includes'.DS.'alphacontent.drawrating.php' );

if ( $list ) {
?>
<?php
	foreach ($list as $item) { ?>
	<?php echo $item->text; ?> <?php echo $item->rating; ?><br />
	<?php if ( $params->get('showstars', 1) ) echo rating_bar( $item->id, $params->get('numstars'), $item->component, $params->get('widthstars'), 'static', 'moduleATR', 0, 0, 1); ?>
<?php
 	 }
} 
?>