DROP TABLE IF EXISTS `#__googleSearch_cse_conf`;
CREATE TABLE `#__googleSearch_cse_conf` (
`id` int NOT NULL,
`google_id` CHAR(100) NOT NULL,
`width` int NOT NULL,
`width_searchfield` INT NOT NULL,
`search_button_label` VARCHAR(100) NOT NULL,
`channel` CHAR(10) NOT NULL,
`domain` VARCHAR( 255 ) NOT NULL ,
`domain_name` VARCHAR( 100 ) NOT NULL ,
`domain_as_default` CHAR( 1 ) NOT NULL,
`site_language` VARCHAR( 10 ) NOT NULL,
`site_encoding` VARCHAR( 36 ) NOT NULL,
`country` VARCHAR(255) NOT NULL,
`web_only` CHAR( 1 ) NOT NULL,
`safesearch` CHAR( 1 ) NOT NULL,
`display_last_search` CHAR( 1 ) NOT NULL,
`intitle` CHAR( 1 ) NOT NULL,

`title_color` VARCHAR(7) NOT NULL,
`bg_color` VARCHAR(7) NOT NULL,
`text_color` VARCHAR(7) NOT NULL,
`url_color` VARCHAR(7) NOT NULL,

`google_logo_pos` VARCHAR(20) NOT NULL,
`radio_pos` VARCHAR(20) NOT NULL,
`button_pos` VARCHAR(20) NOT NULL,
`google_logo_img` CHAR(2) NOT NULL,
`button_img` VARCHAR(255) NOT NULL,
`ad_pos` VARCHAR(20) NOT NULL,

`watermark_type` VARCHAR(10) NOT NULL,
`watermark_color_on_blur` VARCHAR(10) NOT NULL,
`watermark_color_on_focus` VARCHAR(10) NOT NULL,
`watermark_bg_color_on_blur` VARCHAR(10) NOT NULL,
`watermark_bg_color_on_focus` VARCHAR(10) NOT NULL,
`watermark_str` VARCHAR(255) NOT NULL,
`watermark_img` VARCHAR(255) NOT NULL,

`mod_width_searchfield` INT NOT NULL,
`display_searchform` CHAR( 1 ) NOT NULL,
`mod_display_last_search` CHAR( 1 ) NOT NULL,
`mod_google_logo_pos` VARCHAR(20) NOT NULL,
`mod_radio_pos` VARCHAR(20) NOT NULL,
`mod_button_pos` VARCHAR(20) NOT NULL,
`mod_google_logo_img` CHAR(2) NOT NULL,
`mod_button_img` VARCHAR(255) NOT NULL,

`mod_watermark_type` VARCHAR(10) NOT NULL,
`mod_watermark_color_on_blur` VARCHAR(10) NOT NULL,
`mod_watermark_color_on_focus` VARCHAR(10) NOT NULL,
`mod_watermark_bg_color_on_blur` VARCHAR(10) NOT NULL,
`mod_watermark_bg_color_on_focus` VARCHAR(10) NOT NULL,
`mod_watermark_str` VARCHAR(255) NOT NULL,
`mod_watermark_img` VARCHAR(255) NOT NULL
);
INSERT INTO `#__googleSearch_cse_conf` (id, google_id, width, width_searchfield, search_button_label, channel, domain, domain_name, domain_as_default, 
site_language, site_encoding, country, 
web_only, safesearch, display_last_search, intitle,
title_color, bg_color, text_color, url_color, 
google_logo_pos, radio_pos, button_pos, google_logo_img, button_img, ad_pos,
watermark_type, watermark_color_on_blur, watermark_color_on_focus, watermark_bg_color_on_blur, watermark_bg_color_on_focus, watermark_str, watermark_img,
mod_width_searchfield, display_searchform, mod_display_last_search, 
mod_google_logo_pos, mod_radio_pos, mod_button_pos, mod_google_logo_img, mod_button_img,
mod_watermark_type, mod_watermark_color_on_blur, mod_watermark_color_on_focus, mod_watermark_bg_color_on_blur, mod_watermark_bg_color_on_focus, mod_watermark_str, mod_watermark_img) 
VALUES (1, '', 600, 48, 'Search', '', 'kksou.com', 'kksou.com', 1, 
'en', 'ISO-8859-1', '', 
'2', '0', '1', '0',
'#0000FF', '#FFFFFF', '#000000', '#008000', 
'right', 'below', 'right', '1', '', 'top_bottom',
'google', '#AAAAAA', '#000000', '#FFFFFF', '#FFFFFF', 'search...', '',
16, '1', '0', 
'none', 'below', 'below', '2', '',
'google', '#AAAAAA', '#000000', '#FFFFFF', '#FFFFFF', 'search...', '');
