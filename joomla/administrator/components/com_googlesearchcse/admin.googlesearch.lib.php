<?php
/**
* admin.googlesearch.lib.php
* Author: kksou
* Copyright (C) 2006-2009. kksou.com. All Rights Reserved
* Website: http://www.kksou.com/php-gtk2
* Jan 3, 2009
*/

#defined('_JEXEC') or die();

class googleSearch_input {
	function output($label, $var, $value, $postfix='', $prefix='') {
		/*print "<tr><td width=\"20%\" class=\"key\">";
		print "<label for=\"$var\">$label</label></td>";
		if ($postfix!='') $postfix = '&nbsp;&nbsp;<i>'.$postfix.'</i>';
		print '<td>'.$prefix.$value.$postfix.'</td></tr>';*/

		print "<tr><td width=\"20%\" class=\"key\">$label</td>";
		if ($postfix!='') $postfix = '&nbsp;&nbsp;<i>'.$postfix.'</i>';
		print '<td>'.$prefix.$value.$postfix.'</td></tr>';
	}
}

class googleSearch_inputfield extends googleSearch_input {
	function googleSearch_inputfield($label, $var, &$row, $size, $postfix='', $prefix='') {
		$value = $row->$var;
		$str = "<input class=\"inputbox\" type=\"text\" name=\"$var\" id=\"$var\" size=\"$size\" value=\"$value\" />";
		$this->output($label, $var, $str, $postfix, $prefix);
	}
}

class googleSearch_input_yesnoSelectList extends googleSearch_input {
	function googleSearch_input_yesnoSelectList($label, $var, &$row, $postfix='', $prefix='') {
		$value = $row->$var;
		global $googleSearch_jver;
		if ($googleSearch_jver=='1.0') {
			$str = mosHTML::yesnoSelectList( $var, "", $value);
		} else {
			#$str = JHTML::_('select.booleanlist',  $var, '', $value );
			$str = JHTML::_('select.booleanlist',  $var, 'class="inputbox"', $value );
		}
		$this->output($label, $var, $str, $postfix, $prefix);
		#JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
	}
}

class googleSearch_input_radiobuttons extends googleSearch_input {
	function googleSearch_input_radiobuttons($label, $options, $var, &$row, $postfix='', $prefix='') {
		$str = '';
		foreach($options as $k=>$v) {
			$checked = '';
			if ($k==$row->$var) $checked = ' checked';
			$str .= "<input type=\"radio\" name=\"$var\" value=\"$k\"$checked>$v ";
			#$str .= "<input type=\"radio\" name=\"$var\" value=\"$k\"$checked><span style=\"line-height:2em;\">$v</span> "."<br/>\n";
		}
		$this->output($label, $var, $str, $postfix, $prefix);
	}
}

class googleSearch_input_radiobuttons2 extends googleSearch_input {
	function googleSearch_input_radiobuttons2($label, $options, $var, &$row, $postfix='', $prefix='') {
		$str = '';
		$str .= "<table border=\"0\"><tr valign=\"top\">";
		$i=1;
		foreach($options as $k=>$v) {
			$checked = '';
			if ($k==$row->$var) $checked = ' checked';
			#$str .= "<input type=\"radio\" name=\"$var\" value=\"$k\"$checked>$v ";
			#$str .= "<input type=\"radio\" name=\"$var\" value=\"$k\"$checked><span style=\"line-height:4em;\">$v</span> "."<br/>\n";
			if ($i==1 || $i==2 || $i==5) $str .= "<td>";
			$str .= "<input type=\"radio\" name=\"$var\" value=\"$k\"$checked><span style=\"line-height:4em;\">$v</span> "."<br/>\n";
			if ($i==1 || $i==4 || $i==7) $str .= "</td>";
			++$i;
		}
		$str .= "</tr></table>";
		$this->output($label, $var, $str, $postfix, $prefix);
	}
}

class googleSearch_input_pulldownmenu extends googleSearch_input {
	function googleSearch_input_pulldownmenu($label, $options, $var, &$row, $postfix='', $prefix='') {
		$str = "<select name=\"$var\">";
		foreach($options as $k=>$v) {
			$selected = '';
			if ($k==$row->$var) $selected = ' selected';
			$str .= "<option value=\"$k\"$selected>$v</option>";
		}
		$str .= "</select>";
		$this->output($label, $var, $str, $postfix, $prefix);
	}
}

class googleSearch_input_textarea extends googleSearch_input {
	function googleSearch_input_textarea($label, $var, &$row, $cols, $rows, $postfix='', $prefix='') {
		$value = $row->$var;
		$value = str_replace('{', '<', $value);
		$value = str_replace('}', '>', $value);
		$value = htmlentities($value);
		$str = "<textarea class=\"textarea\" cols=\"$cols\" rows=\"$rows\" name=\"$var\" id=\"$var\" />$value</textarea>";
		$this->output($label, $var, $str, $postfix, $prefix);
	}
}

class googleSearch_config {

	function googleSearch_config($option, &$row, $ver='1.5', $use_cse=0) {

		$this->use_cse = $use_cse;

		global $googleSearch_jver;
		$googleSearch_jver = $ver;

		print '<p align="left">Please set the first two fields. All the others are optional.</p>';

		if ($ver=='1.0') {
			print '<form action="index2.php" method="post" name="adminForm">';
		} else {
			print '<form action="index.php" method="post" name="adminForm">';
		}

		?>

		<div class="col100">
			<fieldset class="adminform">
			<table class="admintable">
				<tbody>

				    <?php
				    if ($this->use_cse) {
				    	#$a = new googleSearch_input_textarea('Search Box Code<br>(copy and paste the entire code provided by Google here)', 'google_code1', $row, 80, 10);
				    	#$a = new googleSearch_input_textarea('Search Results Code <br>(copy and paste the entire code provided by Google here)', 'google_code2', $row, 80, 10);
				    	$a = new googleSearch_inputfield('<font color="#0000ff">Custom Search Engine\'s unique identifier (cx)</font>', 'google_id', $row, 60, 'This the unique CSE ID generated by Google e.g. 000455696194071821846:reviews, or<br>partner-pub-1234567812345678:1smy9a-qdjr. It shows up as a "cx" value in the search box code provided by Google.', '');
				    } else {
				   		$a = new googleSearch_inputfield('<font color="#0000ff">Google Adsense ID</font>', 'google_id', $row, 32, 'Just enter the 16-digit number without any alphabets', 'pub-');
				    }
				    $a = new googleSearch_inputfield('<font color="#0000ff">Width of Search Result</font>', 'width', $row, 6, 'This is the width in pixels for the search result');
				    $a = new googleSearch_inputfield('Size of Search Field', 'width_searchfield', $row, 6);
				    $a = new googleSearch_inputfield('Search Button Label', 'search_button_label', $row, 20);

				    if (!$this->use_cse) {
				    	$a = new googleSearch_inputfield('Channel', 'channel', $row, 20, 'Optional: the 10-digit google channel number');

				    #$sitedomain = str_replace('http://', '', $_SERVER['HTTP_HOST']);
				    	$a = new googleSearch_inputfield('Search Domain', 'domain', $row, 32);
						$a = new googleSearch_inputfield('Search Domain Label', 'domain_name', $row, 32);
						$a = new googleSearch_input_yesnoSelectList('Search Domain as Default', 'domain_as_default', $row);
				    }

					$this->input_site_language($row);
					$this->input_site_encoding($row);
					$this->input_country($row);

					$radio_options = array('0'=>'Web only', '1'=>'Domain only', '2'=>'Show both options');
					$radio_options_yes_no = array('1'=>'Yes', '0'=>'No');

					#$a = new googleSearch_input_radiobuttons('Search Web/Your Domain', $radio_options, 'web_only', $row);
					#$a = new googleSearch_input_yesnoSelectList('Enable Google SafeSearch', 'safesearch', $row);
					#$a = new googleSearch_input_radiobuttons('Enable Google SafeSearch', $radio_options_yes_no, 'safesearch', $row);
					$a = new googleSearch_input_pulldownmenu('Enable Google SafeSearch', $radio_options_yes_no, 'safesearch', $row);

					#$a = new googleSearch_input_yesnoSelectList('Display Last Search Value', 'display_last_search', $row);
					#$a = new googleSearch_input_radiobuttons('Display Last Search Value', $radio_options_yes_no, 'display_last_search', $row);
					$a = new googleSearch_input_pulldownmenu('Display Last Search Value', $radio_options_yes_no, 'display_last_search', $row);

					#$a = new googleSearch_input_yesnoSelectList('Search Title Only', 'intitle', $row);
					#$a = new googleSearch_input_radiobuttons('Search Title Only', $radio_options_yes_no, 'intitle', $row);
					$a = new googleSearch_input_pulldownmenu('Search Title Only', $radio_options_yes_no, 'intitle', $row);

					$this->setup_colors();
					if (!$this->use_cse) {
						$this->input_color('Title Color', 'title_color', $row);
						$this->input_color('Background Color', 'bg_color', $row);
						$this->input_color('Text Color', 'text_color', $row);
						$this->input_color('URL Color', 'url_color', $row);
					}

					###$a = new googleSearch_input_yesnoSelectList('Display Google Watermark', 'display_google_watermark', $row);

					if ($this->use_cse) {
						if ($ver==='1.5') {
							$path = JURI::base().'components/com_googlesearchcse/gif';
						} else {
							global $mosConfig_live_site;
							$path = $mosConfig_live_site.'/administrator/components/com_googlesearchcse/gif';
						}

						$google_logo_pos_options = array(
						'none'=>'No logo<br>',
						'right'=>"Right <img src=\"$path/google_cse0.gif\" border=\"0\" alt=\"google_cse0.gif\" align=\"middle\" />",
						'right_gray'=>"Right <img src=\"$path/google_cse1.gif\" border=\"0\" alt=\"google_cse1.gif\" align=\"middle\" />",
						'right_black'=>"Right <img src=\"$path/google_cse2.gif\" border=\"0\" alt=\"google_cse2.gif\" align=\"middle\" /><br>",
						'bottom'=>"Bottom <img src=\"$path/google_cse0.gif\" border=\"0\" alt=\"google_cse0.gif\" align=\"middle\" />",
						'bottom_gray'=>"Bottom <img src=\"$path/google_cse1.gif\" border=\"0\" alt=\"google_cse1.gif\" align=\"middle\" />",
						'bottom_black'=>"Bottom <img src=\"$path/google_cse2.gif\" border=\"0\" alt=\"google_cse2.gif\" align=\"middle\" /><br>",
						);
						$a = new googleSearch_input_radiobuttons2('Google Logo Position', $google_logo_pos_options, 'google_logo_pos', $row);
					} else {
						$a = new googleSearch_input_radiobuttons('Google Logo Position', array('none'=>'None', 'left'=>'Left', 'above'=>'Above') , 'google_logo_pos', $row);
					}

					if ($this->use_cse) {
						#$a = new googleSearch_input_radiobuttons('Search Button Position', array('none'=>'None', 'right'=>'Right') , 'button_pos', $row);
						$a = new googleSearch_input_pulldownmenu('Search Button Position', array('none'=>'None', 'right'=>'Right'), 'button_pos', $row);
					} else {
						$a = new googleSearch_input_radiobuttons('Select Web/Domain Position', array('none_web'=>'None (Search Web only)', 'none_domain'=>'None (Search Domain only)', 'right'=>'Right', 'below'=>'Below') , 'radio_pos', $row);
						$a = new googleSearch_input_radiobuttons('Search Button Position', array('none'=>'None', 'right'=>'Right', 'below'=>'Below') , 'button_pos', $row);

						$google_logo_img_options = array(
						#'0'=>'No logo',
						'1'=>'<img src="http://www.google.com/logos/Logo_25wht.gif" border="0" alt="Google" align="middle" />',
						'2'=>'<img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" border="0" alt="Google" align="middle" />',
						'3'=>'<img src="http://www.google.com/images/poweredby_transparent/poweredby_000000.gif" border="0" alt="Google" align="middle" /> (for dark background)',
						);
						$a = new googleSearch_input_radiobuttons('Google Logo Image', $google_logo_img_options, 'google_logo_img', $row);
						$a = new googleSearch_inputfield('Search Button Image', 'button_img', $row, 48, 'Please enter the full url');
					}
					/*foreach(array('left', 'right', 'top', 'bottom') as $pos) {
						$pos2 = ucfirst($pos);
						$a = new googleSearch_inputfield("$pos2 padding for Search Field", 'searchfield_padding_'.$pos, $row, 3, "This is the amount of space (in pixels) on the $pos of the Search field");
					}*/
					#$a = new googleSearch_input_radiobuttons('Ad Location', array('top_bottom'=>'Top and Bottom', 'top_right'=>'Top and Right', 'right'=>'Right') , 'ad_pos', $row);
					$a = new googleSearch_input_pulldownmenu('Ad Location', array('top_bottom'=>'Top and Bottom', 'top_right'=>'Top and Right', 'right'=>'Right'), 'ad_pos', $row);

					#$a = new googleSearch_input_radiobuttons('Watermark Type', array('none'=>'None', 'google'=>'Google', 'text'=>'Text') , 'watermark_type', $row);
					$a = new googleSearch_input_pulldownmenu('Watermark Type', array('none'=>'None', 'google'=>'Google', 'text'=>'Text'), 'watermark_type', $row);

					$this->input_color('Text Color on blur', 'watermark_color_on_blur', $row);
					$this->input_color('Text Color on focus', 'watermark_color_on_focus', $row);
					$this->input_color('Background Color on blur', 'watermark_bg_color_on_blur', $row);
					$this->input_color('Background Color on focus', 'watermark_bg_color_on_focus', $row);
					$a = new googleSearch_inputfield('Watermark Text', 'watermark_str', $row, 32);
					#$a = new googleSearch_inputfield('Watermark Image', 'watermark_img', $row, 48, 'Please enter the full url');

					#print "<tr><td colspan=2><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
?>
				</tbody>
			</table>

			<?php
			print '<table class="admintable" cellpadding="4" cellspacing="0" border="0" align="left"><tbody><tr><td>';
			print "<br><p><b>Note: </b>The following settings are for use with the <b>googleSearch_cse module</b> to be used in conjunction with this component. If you're not using the googleSearch_cse module, you can ignore the following settings.</p>";
			print "</td></tr></tbody></table>";

			print '<table class="admintable" cellpadding="4" cellspacing="0" border="0" align="left"><tbody>';

			$a = new googleSearch_inputfield('Width of Search Field', 'mod_width_searchfield', $row, 6);

			#$a = new googleSearch_input_yesnoSelectList('Display search form in component', 'display_searchform', $row);
			$a = new googleSearch_input_pulldownmenu('Display search form in component', $radio_options_yes_no, 'display_searchform', $row);

			#$a = new googleSearch_input_yesnoSelectList('Display Last Search Value', 'mod_display_last_search', $row);
			$a = new googleSearch_input_pulldownmenu('Display Last Search Value', $radio_options_yes_no, 'mod_display_last_search', $row);

			###$a = new googleSearch_input_yesnoSelectList('Display Google Watermark (for module)', 'mod_display_google_watermark', $row);

			if ($this->use_cse) {
				#$a = new googleSearch_input_radiobuttons('Search Button Position', array('none'=>'None', 'right'=>'Right', 'below'=>'Below') , 'mod_button_pos', $row);
				$a = new googleSearch_input_pulldownmenu('Search Button Position', array('none'=>'None', 'right'=>'Right', 'below'=>'Below'), 'mod_button_pos', $row);
			} else {
				$a = new googleSearch_input_radiobuttons('Google Logo Position', array('none'=>'None', 'left'=>'Left', 'above'=>'Above') , 'mod_google_logo_pos', $row);
				$a = new googleSearch_input_radiobuttons('Select Web/Domain Position', array('none_web'=>'None (Search Web only)', 'none_domain'=>'None (Search Domain only)', 'right'=>'Right', 'below'=>'Below') , 'mod_radio_pos', $row);
				$a = new googleSearch_input_radiobuttons('Search Button Position', array('none'=>'None', 'right'=>'Right', 'below'=>'Below') , 'mod_button_pos', $row);
				$a = new googleSearch_input_radiobuttons('Google Logo Image ', $google_logo_img_options, 'mod_google_logo_img', $row);
				$a = new googleSearch_inputfield('Search Button Image', 'mod_button_img', $row, 48, 'Please enter the full url');
			}
			#$a = new googleSearch_inputfield('Left padding for Search Button (for module)', 'mod_button_left_padding', $row, 6, 'This is the amount of space (in pixels) on the left of the Search button');

			/*foreach(array('left', 'right', 'top', 'bottom') as $pos) {
				$pos2 = ucfirst($pos);
				$a = new googleSearch_inputfield("$pos2 padding for Search Field (for module)", 'mod_searchfield_padding_'.$pos, $row, 3, "This is the amount of space (in pixels) on the $pos of the Search field (in module)");
			}*/

			#$a = new googleSearch_input_radiobuttons('Watermark Type', array('none'=>'None', 'google'=>'Google', 'text'=>'Text') , 'mod_watermark_type', $row);
			$a = new googleSearch_input_pulldownmenu('Watermark Type', array('none'=>'None', 'google'=>'Google', 'text'=>'Text'), 'mod_watermark_type', $row);
			$this->input_color('Text Color on blur', 'mod_watermark_color_on_blur', $row);
			$this->input_color('Text Color on focus', 'mod_watermark_color_on_focus', $row);
			$this->input_color('Background Color on blur', 'mod_watermark_bg_color_on_blur', $row);
			$this->input_color('Background Color on focus', 'mod_watermark_bg_color_on_focus', $row);
			$a = new googleSearch_inputfield('Watermark Text', 'mod_watermark_str', $row, 32);
			#$a = new googleSearch_inputfield('Watermark Image', 'mod_watermark_img', $row, 48, 'Please enter the full url');


			print "<tr><td colspan=2><br><br><br><br><br><br></td></tr>";

			print "</tbody></table>";

			?>

			</fieldset>
		</div>

		<?php
		$this->hiddenfield('option', $option);
		$this->hiddenfield('task', '');
		$this->hiddenfield('id', $row->id);
		if ($ver=='1.0') $this->hiddenfield('act', 'configure');
		print '</form>';
	}

	function input_site_language($row) {
		$languages = array(
		"ar"=>"Arabic",
		"bg"=>"Bulgarian",
		"zh-CN"=>"Chinese (simplified)",
		"zh-TW"=>"Chinese (traditional)",
		"hr"=>"Croatian",
		"cs"=>"Czech",
		"da"=>"Danish",
		"nl"=>"Dutch",
		"en"=>"English",
		"fi"=>"Finnish",
		"fr"=>"French",
		"de"=>"German",
		"el"=>"Greek",
		"iw"=>"Hebrew",
		"hu"=>"Hungarian",
		"in"=>"Indonesian",
		"it"=>"Italian",
		"ja"=>"Japanese",
		"ko"=>"Korean",
		"no"=>"Norwegian",
		"pl"=>"Polish",
		"pt"=>"Portuguese",
		"ro"=>"Romanian",
		"ru"=>"Russian",
		"sr"=>"Serbian",
		"sk"=>"Slovak",
		"es"=>"Spanish",
		"sv"=>"Swedish",
		"th"=>"Thai",
		"tr"=>"Turkish",
		"vi"=>"Vietnamese",
		);

		$a = new googleSearch_input_pulldownmenu('Site Language', $languages, 'site_language', $row);
	}

	function input_site_encoding($row) {
		$encodings = array(
		"ISO-8859-1"=>"West European Latin-1 (ISO-8859-1)",
		"ISO-8859-15"=>"West European Latin-9 (ISO-8859-15)",
		"windows-1252"=>"Western (Windows-1252)",
		"ISO-8859-10"=>"Nordic Latin-6 (ISO-8859-10)",
		"ISO-8859-7"=>"Greek (ISO-8859-7)",
		"---1"=>"------",
		"Shift_JIS"=>"Japanese (Shift_JIS)",
		"EUC-JP"=>"Japanese (EUC-JP)",
		"ISO-2022-JP"=>"Japanese (ISO-2022-JP)",
		"---2"=>"------",
		"GB2312"=>"Chinese Simplified (GB2312)",
		"GB18030"=>"Chinese Simplified (GB18030)",
		"big5"=>"Chinese Traditional (Big5)",
		"EUC-KR"=>"Korean (EUC-KR)",
		"---3"=>"------",
		"windows-874"=>"Thai (Windows-874)",
		"windows-1258"=>"Vietnamese (Windows-1258)",
		"---4"=>"------",
		"ISO-8859-2"=>"Central European Latin-2 (ISO-8859-2)",
		"windows-1250"=>"Central European (Windows-1250)",
		"cp852"=>"Central European (CP852)",
		"ISO-8859-9"=>"Turkish Latin-5 (ISO-8859-9)",
		"windows-1254"=>"Turkish (Windows-1254)",
		"ISO-8859-3"=>"South European Latin-3 (ISO-8859-3)",
		"ISO-8859-8-I"=>"Hebrew (ISO-8859-8-I)",
		"windows-1255"=>"Hebrew (Windows-1255)",
		"windows-1256"=>"Arabic (Windows-1256)",
		"---5"=>"------",
		"ISO-8859-5"=>"Cyrillic (ISO-8859-5)",
		"KOI8-R"=>"Cyrillic (KOI8-R)",
		"windows-1251"=>"Cyrillic (Windows-1251)",
		"cp-866"=>"Cyrillic/Russian (CP-866)",
		"---6"=>"------",
		"UTF-8"=>"Unicode (UTF-8)",
		);

		$a = new googleSearch_input_pulldownmenu('Site Encoding', $encodings, 'site_encoding', $row);
	}

	function input_country($row) {
		$countries = array(
		""=>"any region",
		"countryAF" => "Afghanistan",
		"countryAL" => "Albania",
		"countryDZ" => "Algeria",
		"countryAS" => "American Samoa",
		"countryAD" => "Andorra",
		"countryAO" => "Angola",
		"countryAI" => "Anguilla",
		"countryAQ" => "Antarctica",
		"countryAG" => "Antigua and Barbuda",
		"countryAR" => "Argentina",
		"countryAM" => "Armenia",
		"countryAW" => "Aruba",
		"countryAU" => "Australia",
		"countryAT" => "Austria",
		"countryAZ" => "Azerbaijan",
		"countryBS" => "Bahamas",
		"countryBH" => "Bahrain",
		"countryBD" => "Bangladesh",
		"countryBB" => "Barbados",
		"countryBY" => "Belarus",
		"countryBE" => "Belgium",
		"countryBZ" => "Belize",
		"countryBJ" => "Benin",
		"countryBM" => "Bermuda",
		"countryBT" => "Bhutan",
		"countryBO" => "Bolivia",
		"countryBA" => "Bosnia and Herzegovina",
		"countryBW" => "Botswana",
		"countryBV" => "Bouvet Island",
		"countryBR" => "Brazil",
		"countryIO" => "British Indian Ocean Territory",
		"countryVG" => "British Virgin Islands",
		"countryBN" => "Brunei",
		"countryBG" => "Bulgaria",
		"countryBF" => "Burkina Faso",
		"countryBI" => "Burundi",
		"countryKH" => "Cambodia",
		"countryCM" => "Cameroon",
		"countryCA" => "Canada",
		"countryCV" => "Cape Verde",
		"countryKY" => "Cayman Islands",
		"countryCF" => "Central African Republic",
		"countryTD" => "Chad",
		"countryCL" => "Chile",
		"countryCN" => "China",
		"countryCX" => "Christmas Island",
		"countryCC" => "Cocos Islands",
		"countryCO" => "Colombia",
		"countryKM" => "Comoros",
		"countryCG" => "Congo - Brazzaville",
		"countryCD" => "Congo - Kinshasa",
		"countryCK" => "Cook Islands",
		"countryCR" => "Costa Rica",
		"countryHR" => "Croatia",
		"countryCU" => "Cuba",
		"countryCY" => "Cyprus",
		"countryCZ" => "Czech Republic",
		"countryDK" => "Denmark",
		"countryDJ" => "Djibouti",
		"countryDM" => "Dominica",
		"countryDO" => "Dominican Republic",
		"countryEC" => "Ecuador",
		"countryEG" => "Egypt",
		"countrySV" => "El Salvador",
		"countryGQ" => "Equatorial Guinea",
		"countryER" => "Eritrea",
		"countryEE" => "Estonia",
		"countryET" => "Ethiopia",
		"countryFK" => "Falkland Islands",
		"countryFO" => "Faroe Islands",
		"countryFJ" => "Fiji",
		"countryFI" => "Finland",
		"countryFR" => "France",
		"countryGF" => "French Guiana",
		"countryPF" => "French Polynesia",
		"countryTF" => "French Southern Territories",
		"countryGA" => "Gabon",
		"countryGM" => "Gambia",
		"countryGE" => "Georgia",
		"countryDE" => "Germany",
		"countryGH" => "Ghana",
		"countryGI" => "Gibraltar",
		"countryGR" => "Greece",
		"countryGL" => "Greenland",
		"countryGD" => "Grenada",
		"countryGP" => "Guadeloupe",
		"countryGU" => "Guam",
		"countryGT" => "Guatemala",
		"countryGN" => "Guinea",
		"countryGW" => "Guinea-Bissau",
		"countryGY" => "Guyana",
		"countryHT" => "Haiti",
		"countryHM" => "Heard Island and McDonald Islands",
		"countryHN" => "Honduras",
		"countryHK" => "Hong Kong SAR China",
		"countryHU" => "Hungary",
		"countryIS" => "Iceland",
		"countryIN" => "India",
		"countryID" => "Indonesia",
		"countryIR" => "Iran",
		"countryIQ" => "Iraq",
		"countryIE" => "Ireland",
		"countryIL" => "Israel",
		"countryIT" => "Italy",
		"countryCI" => "Ivory Coast",
		"countryJM" => "Jamaica",
		"countryJP" => "Japan",
		"countryJO" => "Jordan",
		"countryKZ" => "Kazakhstan",
		"countryKE" => "Kenya",
		"countryKI" => "Kiribati",
		"countryKW" => "Kuwait",
		"countryKG" => "Kyrgyzstan",
		"countryLA" => "Laos",
		"countryLV" => "Latvia",
		"countryLB" => "Lebanon",
		"countryLS" => "Lesotho",
		"countryLR" => "Liberia",
		"countryLY" => "Libya",
		"countryLI" => "Liechtenstein",
		"countryLT" => "Lithuania",
		"countryLU" => "Luxembourg",
		"countryMO" => "Macao SAR China",
		"countryMK" => "Macedonia",
		"countryMG" => "Madagascar",
		"countryMW" => "Malawi",
		"countryMY" => "Malaysia",
		"countryMV" => "Maldives",
		"countryML" => "Mali",
		"countryMT" => "Malta",
		"countryMH" => "Marshall Islands",
		"countryMQ" => "Martinique",
		"countryMR" => "Mauritania",
		"countryMU" => "Mauritius",
		"countryYT" => "Mayotte",
		"countryMX" => "Mexico",
		"countryFM" => "Micronesia",
		"countryMD" => "Moldova",
		"countryMC" => "Monaco",
		"countryMN" => "Mongolia",
		"countryMS" => "Montserrat",
		"countryMA" => "Morocco",
		"countryMZ" => "Mozambique",
		"countryMM" => "Myanmar",
		"countryNA" => "Namibia",
		"countryNR" => "Nauru",
		"countryNP" => "Nepal",
		"countryNL" => "Netherlands",
		"countryAN" => "Netherlands Antilles",
		"countryNC" => "New Caledonia",
		"countryNZ" => "New Zealand",
		"countryNI" => "Nicaragua",
		"countryNE" => "Niger",
		"countryNG" => "Nigeria",
		"countryNU" => "Niue",
		"countryNF" => "Norfolk Island",
		"countryKP" => "North Korea",
		"countryMP" => "Northern Mariana Islands",
		"countryNO" => "Norway",
		"countryOM" => "Oman",
		"countryPK" => "Pakistan",
		"countryPW" => "Palau",
		"countryPS" => "Palestinian Territory",
		"countryPA" => "Panama",
		"countryPG" => "Papua New Guinea",
		"countryPY" => "Paraguay",
		"countryPE" => "Peru",
		"countryPH" => "Philippines",
		"countryPN" => "Pitcairn",
		"countryPL" => "Poland",
		"countryPT" => "Portugal",
		"countryPR" => "Puerto Rico",
		"countryQA" => "Qatar",
		"countryRE" => "Reunion",
		"countryRO" => "Romania",
		"countryRU" => "Russia",
		"countryRW" => "Rwanda",
		"countrySH" => "Saint Helena",
		"countryKN" => "Saint Kitts and Nevis",
		"countryLC" => "Saint Lucia",
		"countryPM" => "Saint Pierre and Miquelon",
		"countryVC" => "Saint Vincent and the Grenadines",
		"countryWS" => "Samoa",
		"countrySM" => "San Marino",
		"countryST" => "Sao Tome and Principe",
		"countrySA" => "Saudi Arabia",
		"countrySN" => "Senegal",
		"countryYU" => "Serbia and Montenegro",
		"countrySC" => "Seychelles",
		"countrySL" => "Sierra Leone",
		"countrySG" => "Singapore",
		"countrySK" => "Slovakia",
		"countrySI" => "Slovenia",
		"countrySB" => "Solomon Islands",
		"countrySO" => "Somalia",
		"countryZA" => "South Africa",
		"countryGS" => "South Georgia and the South Sandwich Islands",
		"countryKR" => "South Korea",
		"countryES" => "Spain",
		"countryLK" => "Sri Lanka",
		"countrySD" => "Sudan",
		"countrySR" => "Suriname",
		"countrySJ" => "Svalbard and Jan Mayen",
		"countrySZ" => "Swaziland",
		"countrySE" => "Sweden",
		"countryCH" => "Switzerland",
		"countrySY" => "Syria",
		"countryTW" => "Taiwan",
		"countryTJ" => "Tajikistan",
		"countryTZ" => "Tanzania",
		"countryTH" => "Thailand",
		"countryTG" => "Togo",
		"countryTK" => "Tokelau",
		"countryTO" => "Tonga",
		"countryTT" => "Trinidad and Tobago",
		"countryTN" => "Tunisia",
		"countryTR" => "Turkey",
		"countryTM" => "Turkmenistan",
		"countryTC" => "Turks and Caicos Islands",
		"countryTV" => "Tuvalu",
		"countryVI" => "U.S. Virgin Islands",
		"countryUG" => "Uganda",
		"countryUA" => "Ukraine",
		"countryAE" => "United Arab Emirates",
		"countryGB" => "United Kingdom",
		"countryUS" => "United States",
		"countryUM" => "United States Minor Outlying Islands",
		"countryUY" => "Uruguay",
		"countryUZ" => "Uzbekistan",
		"countryVU" => "Vanuatu",
		"countryVA" => "Vatican",
		"countryVE" => "Venezuela",
		"countryVN" => "Vietnam",
		"countryWF" => "Wallis and Futuna",
		"countryEH" => "Western Sahara",
		"countryYE" => "Yemen",
		"countryZM" => "Zambia",
		"countryZW" => "Zimbabwe",
		);

		$a = new googleSearch_input_pulldownmenu('Country', $countries, 'country', $row, 'Select your country to determine which Google domain will be used for search results');
	}

	function setup_colors() {
	?>

<style type="text/css">
<!--
.picker_layer {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-decoration: none;
	background-color: #d4d0c8;
	border-width: 1px;
	border-style: solid;
	border-color: #666666;
	overflow: visible;
	height: auto;
	width: auto;
}
.picker_buttons {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	background-color:#d4d0c8;
	border-style:solid;
	border-color:#666666;
	border-width:1px;
	padding:1px;
	cursor:pointer;
	color:#000000;
}
.cell_color {
	cursor:pointer;
	width:9px;
	height:9px;
}
.color_table {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-decoration: none;
}
.choosed_color_cell{
	border-style:solid; border-color:#000000; border-width:1px;
}
.default_color_btn{
	width:17px; height:17px; background-image:url(defaultcolor.jpg);
	background-repeat:no-repeat; background-position:center;
}
-->
</style>

<script type="text/javascript">
/*
 *	Gchats color picker by Majid Khosravi
 *	Copyright (c) 2006 - 2008 Gchat Design Studio
 *	URL: http://www.gchats.com
 *	Date: April 24 2008
 *  Gchats color picker is freely distributable under the terms of GPL license.
 *  Please visit: http://www.gchats.com for updates
 *  @Version 1.1
 *--------------------------------------------------------------------------*/
// JavaScript Document
var layerWidth = 218;
var layerHeight = 144;
var currentId = "";
var orgColor ="";
function openPicker(id){
	currentId = id;
	removeLayer("picker");
	Obj = document.getElementById(id);
	orgColor = Obj.value;
	createLayer("picker",findPosX(Obj)+Obj.offsetWidth+20,findPosY(Obj));
}

function createLayer(id,left,top){
	var width = layerWidth;
	var height = layerHeight;
	var zindex = 1000;
	var bgcolor = "#d4d0c8";
	var txtcolor = "#000000";
	var msg = getPickerContent();
	if (document.layers) {
		if (document.layers[id]) {
		   return;
		}
		var layer=document.layers[id]=new Layer(width);
		layer.className = "picker_layer";
		layer.name = id;
		layer.left=left;
		layer.top=top;
		layer.clip.height=height;
		layer.visibility = 'show';
		layer.zIndex=zindex;
		layer.bgColor=bgcolor;
		layer.innerHTML = msg;
	}else if (document.all) {
		if (document.all[id]) {
			return
		}
  		var layer= '\n<DIV class="picker_layer" id='+id+' style="position:absolute'
		+'; left:'+left+"px"
		+'; top:'+top+"px"
		+'; width:'+width
		+'; height:'+height
		+'; visibility:visible'
		+'; z-index:'+zindex
		+';text-align:left">'
		+ msg
		+'</DIV>';
		document.body.insertAdjacentHTML("BeforeEnd",layer);
	}else if(document.getElementById){
		var layer = document.createElement ('div');
		layer.setAttribute ('id', id);
		document.body.appendChild (layer);
		var ly = document.getElementById(id);
		ly.className = "picker_layer";
		ly.style.position= "absolute";
		ly.style.left= left+"px";
		ly.style.top= top+ "px";
		ly.style.width= width+ "px";
		ly.style.height= height+ "px";
		ly.style.textAlign= "left";
		ly.innerHTML = msg;
	}
}
function showClr(color){
	Obj = document.getElementById(currentId);
	Obj.value = color;
	Obj.style.backgroundColor=color;
	Obj = document.getElementById("gcpicker_colorSample");
	Obj.style.backgroundColor=color;
	Obj = document.getElementById("gcpicker_colorCode");
	Obj.innerHTML = color;

}
function setClr(color){
	Obj = document.getElementById(currentId);
	Obj.value = color;
	Obj.style.backgroundColor=color;
	currentId = "";
	removeLayer("picker");

}
function cancel(){
	Obj = document.getElementById(currentId);
	Obj.value = orgColor;
	Obj.style.backgroundColor=orgColor;
	removeLayer("picker");
}
function removeLayer(id){
	if(document.getElementById(id) ==null){
		return;
	}
	if (document.layers && document.layers[id]) {
  		document.layers[id].visibility='hide'
		delete document.layers[id]
	}
	if (document.all && document.all[id]) {
		document.all[id].innerHTML=''
		document.all[id].outerHTML=''
	}else if(document.getElementById){
		 var b = document.body;
 		 var layer = document.getElementById(id);
	 	 b.removeChild(layer);
	}
}
function getPickerContent(){
	var content = 	'<table width="222" border="0" cellpadding="0" cellspacing="1"><tr><td>';
	content += '<table width="100%" border="0" cellpadding="0" cellspacing="1" class="color_table"><tr><td bgcolor="#CCCCCC" id="gcpicker_colorSample" width="40px" class="choosed_color_cell">&nbsp;</td><td align="center"><div id="gcpicker_colorCode">#CCCCCC</div></td><td width="60px" align="center"><input type="submit" value="" onclick="cancel()" class="default_color_btn" /></td></tr></table>';
	content += '</td></tr><tr><td>';
	content += colorTable()+'</td></tr></table>';
	return content;
}
function colorTable(){
	var clrfix = Array("#000000","#333333","#666666","#999999","#cccccc","#ffffff","#ff0000","#00ff00","#0000ff","#ffff00","#00ffff","#ff00ff");
	var table ='<table border="0"  cellpadding="0" cellspacing="0" bgcolor="#000000"><tr>';
	table += '';
	for(var j=0;j<3;j++){
		table += '<td width="11"><table bgcolor="#000000"  border="0"  cellpadding="0" cellspacing="1"  class="color_table">';
		for(var i=0;i<12;i++){
			var clr ='#000000';
			if(j==1){
				clr = clrfix[i];
			}
			table += '<tr><td bgcolor="'+clr+'" class="cell_color" onmouseover="showClr('+"'"+clr+"'"+')" onclick="setClr('+"'"+clr+"'"+')"></td></tr>';
		}
		table += '</table></td>';
	}
	table +='<td><table border="0" cellpadding="0" cellspacing="0">';
	for (var c = 0; c<6; c++) {
		if(c==0 || c==3){
			table +="<tr>";
		}
		table += "<td>"

		table = table+'<table border="0" cellpadding="0" cellspacing="1" class="color_table"> ';
		for (var j = 0; j<6; j++) {
			table +="<tr>";
			for (var i = 0; i<6; i++) {
				var clrhex = rgb2hex(j*255/5,i*255/5,c*255/5);
				table += '<td bgcolor="'+clrhex+'" class="cell_color" onmouseover="showClr('+"'"+clrhex+"'"+')" onclick="setClr('+"'"+clrhex+"'"+')"></td>';
			}
			table +="</tr>";
		}
		table +="</table>";
		table += "</td>"
		if(c==2 || c==5){
			table +="</tr>";
		}
	}
	table +='</table></td></tr></table>';
	return table;
}

function findPosX(obj){
	var curleft = 0;
	if(obj.offsetParent)
        while(1){
			curleft += obj.offsetLeft;
			if(!obj.offsetParent)
			break;
			obj = obj.offsetParent;
		}
	else if(obj.x)
	curleft += obj.x;
	return curleft;
}
function findPosY(obj){
	var curtop = 0;
	if(obj.offsetParent){
		while(1){
			curtop += obj.offsetTop;
			if(!obj.offsetParent){
				break;
			}
			obj = obj.offsetParent;
		}
    }else if(obj.y){
		curtop += obj.y;
	}
	return curtop;
}

function rgb2hex(red, green, blue)
{
    var decColor = red + 256 * green + 65536 * blue;
    var clr = decColor.toString(16);
	for(var i =clr.length;i<6;i++){
		clr = "0"+clr;
	}
	return "#"+clr;
}
</script>

<?php

	}

	function input_color($label, $var, $row) {
		print "<tr><td width=\"20%\" class=\"key\">";
		print "<label for=\"$var\">$label</label></td>";
		print "<td>";
		$color_val = $row->$var;
		if ($color_val!='') {
			print "<input name=\"$var\" type=\"text\" id=\"$var\" value=\"$color_val\" size=\"7\" maxlength=\"7\" style=\"background:$color_val\"/> ";
		} else {
			print "<input name=\"$var\" type=\"text\" id=\"$var\" value=\"$color_val\" size=\"7\" maxlength=\"7\" /> ";
		}
		print "<span onclick=\"openPicker('$var')\" class=\"picker_buttons\">Pick color...</span>";
		print "</td></tr>";
	}

	function hiddenfield($var, $value) {
		print "<input type=\"hidden\" name=\"$var\" value=\"$value\" />";
	}

}

?>