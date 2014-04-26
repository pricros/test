<?php
/**
 * Youtubeplaylist Module
 *
 * @version 4.0.0
 * @package Youtubeplaylist
 * @author Nguyen Hoang Viet
 * @copyright Copyright (C) 2008 Luyenkim.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__).DS.'helper.php');
//$counterID = $module->id;
$html = modYoutubeplaylistHelper::getContent($params,'m'.$module->id);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
//require(JModuleHelper::getLayoutPath('mod_youtubeplaylist'));
require JModuleHelper::getLayoutPath('mod_youtubeplaylist', $params->get('layout', 'default'));
