<?php
/*
 * @component AlphaContent
 * @copyright Copyright (C) 2005 - 2011 Bernard Gilly. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (class_exists('plgSystemReReplacer')) { 
	echo "Extension doesn't not work if ReReplacer Plugin is enabled"; 
	exit; 
}
if (class_exists('plgSystemRedact')) { 
	echo "Extension doesn't not work if Redaction Plugin is enabled";
	exit;
}

// include utils
include( JPATH_COMPONENT.DS.'assets'.DS.'includes'.DS.'alphacontent.functions.php' );

// Component Helper
jimport('joomla.application.component.helper');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new alphacontentController();

// Perform the Request task
$controller->execute( JRequest::getVar('task', 'showdirectory', 'default', 'string') );

// Redirect if set by the controller
$controller->redirect();

?>