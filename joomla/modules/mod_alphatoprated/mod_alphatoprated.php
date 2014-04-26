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

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$list = modAlphaTopRatedHelper::getList($params);
require(JModuleHelper::getLayoutPath('mod_alphatoprated'));