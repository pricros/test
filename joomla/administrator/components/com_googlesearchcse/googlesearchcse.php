<?php
defined( '_JEXEC' ) or die;

#echo "Backend - test123";

jimport('joomla.application.component.controller');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
//require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT.DS.'admin.googlesearch.lib.php');

$controller = JController::getInstance('Googlesearchcse');
$controller->registerDefaultTask('listconfig');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

