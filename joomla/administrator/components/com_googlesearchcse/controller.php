<?php
defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

class GooglesearchcseController extends JController
{
	function __construct($use_cse=0)
	{
		$this->use_cse = $use_cse;
		parent::__construct();
	}

	function listconfig()
	{
		$option = JRequest::getCmd('option');

		$db	= JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__googleSearch_cse_conf");
		$rows = $db->loadObjectList();

		require_once(JPATH_COMPONENT.DS.'admin.googlesearchcse.html.php');
		HTML_googleSearch_cse::listConfiguration($option, $rows[0]);

		#print '<p>Click the "Options" button on the top right to set the options</p>';
	}

	function save()
	{
		$option = JRequest::getCmd('option');
		$this->setRedirect('index.php?option=' . $option);

		#if (trim($_POST['google_id'])=='') {
		#	echo "<script> alert('Please enter your google adsense ID.'); window.history.go(-1); </script>\n";
		#	exit();
		#}

		if (!$this->use_cse) {
			/*if (trim($_POST['google_id'])!='' && !preg_match('/^\d{16}$/', $_POST['google_id'])) {
				echo "<script> alert('The google adsense ID should be a 16-digit number.'); window.history.go(-1); </script>\n";
				exit();
			}*/
		}

		if (trim($_POST['width'])=='' || $_POST['width']<=0) {
			echo "<script> alert('Please enter the width of the search result in pixels.'); window.history.go(-1); </script>\n";
			exit();
		}

		if ($_POST['width_searchfield']<=0) $_POST['width_searchfield']=32;
		if ($_POST['mod_width_searchfield']<=0) $_POST['mod_width_searchfield']=16;

		if (!preg_match('/^\d+$/', $_POST['width'])) {
			echo "<script> alert('The width of the search result should be a number in pixels.'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!preg_match('/^\d+$/', $_POST['width_searchfield'])) {
			echo "<script> alert('The width of the search field should be an interger number.'); window.history.go(-1); </script>\n";
			exit();
		}

		if (preg_match('/^---/', $_POST['site_encoding'])) $_POST['site_encoding'] = 'ISO-8859-1';

		// check colors
		if (!$this->use_cse) {
			googleSearch_check_color('title_color');
			googleSearch_check_color('bg_color');
			googleSearch_check_color('text_color');
			googleSearch_check_color('url_color');
		}

		googleSearch_check_color('watermark_color_on_blur');
		googleSearch_check_color('watermark_color_on_focus');
		googleSearch_check_color('watermark_bg_color_on_blur');
		googleSearch_check_color('watermark_bg_color_on_focus');

		$post = JRequest::get('post');

		$row =& JTable::getInstance('googlesearchcse', 'Table');

		if (!$row->bind($post)) {
			return JError::raiseWarning(500, $row->getError());
		}

		if (!$row->store()) {
			return JError::raiseWarning(500, $row->getError());
		}

		$this->setMessage('Message Saved');
	}

}

function googleSearch_check_color($fieldname) {
	if (!isset($_POST[$fieldname])) return;
	$label = str_replace('_', ' ', $fieldname);
	$regexp = '/^[0-9a-fA-F]{6}$/';
	if (preg_match($regexp, $_POST[$fieldname])) $_POST[$fieldname] = '#'.$_POST[$fieldname];
	$regexp = '/^#[0-9a-fA-F]{6}$/';
	$_POST[$fieldname] = strtoupper($_POST[$fieldname]);
	if (!preg_match($regexp, $_POST[$fieldname])) {
		echo "<script> alert('You have entered \'{$_POST[$fieldname]}\' for $label. Please enter the color in hexadecimal format e.g. #3366FF'); window.history.go(-1); </script>\n";
		exit();
	}
}
