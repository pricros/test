<?php 
/**
 * Joomla! 1.5 module Youtube Playlist
 *
 * @version $Id: 2011-03-16 13:43:32 svn $
 * @author WEBKUL
 * @package Joomla
# copyright Copyright (C) 2010 webkul.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 *
 */ 
 
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$jquery              = trim( $params->get( 'jquery' ) );
$videolinks          = trim( $params->get( 'videolinks' ) );
$videoTitle          = trim( $params->get( 'videoTitle' ) );
$width               = trim( $params->get( 'width' ) );
$height              = trim( $params->get( 'height' ) );
$addThumbs           = trim( $params->get( 'addThumbs' ) );
$autoPlay            = trim( $params->get( 'autoPlay' ) );
$allowFullScreen     = trim( $params->get( 'allowFullScreen' ) );
$relVedio			= trim( $params->get( 'relVedio' ) );

$ytuserstream			= trim( $params->get( 'ytuserstream' ) );
$wkytstream			= trim( $params->get( 'wkytstream' ) );
$yt_numstream		= trim( $params->get( 'yt_numstream' ) );

require(JModuleHelper::getLayoutPath('mod_custom_youtubeplaylist'));