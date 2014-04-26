<?php
defined( '_JEXEC' ) or die;

require_once(JPATH_COMPONENT.DS.'googlesearch.lib.php');
$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__googleSearch_cse_conf LIMIT 1");
$rows = $db->loadObjectList();
$r = $rows[0];

$app = new googleSearch_DisplayForm($r, '', '2.5', 0, '', 1);
