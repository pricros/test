<?php
/*
 * @component AlphaContent
 * @copyright Copyright (C) 2005 - 2011 Bernard Gilly. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * @package AlphaContent
 */
class configurationController extends JController
{
	/**
	 * Custom Constructor
	 */
 	function __construct()	{
		parent::__construct( );
	}	

	
	/**
	* Show/Edit Configuration
	*/
	function edit() {

		$model 			= &$this->getModel( 'alphacontent' );
		$modelUpdate 	= &$this->getModel( 'upgrade' );
		
		$_params 		= &JComponentHelper::getParams( 'com_alphauserpoints' );
		
		$view  			= $this->getView( 'alphacontent','html');
		
		$model->_set_configuration ();		
		
		// cache subfolder(group) 'rssconnector', cache method: callback
		$cache= & JFactory::getCache('com_alphacontent');
		// save configured lifetime		 
		@$lifetime=$cache->lifetime; 
		$cache->setLifeTime(15 * 60); // 15 minutes to seconds		 
		// save cache conf		 
		$conf =& JFactory::getConfig();		 
		// check if cache is enabled in configuration		 
		$cacheactive = $conf->getValue('config.caching');		 
		$cache->setCaching(1); //enable caching		 
		// if the cache expired, the method will be called again and the result will be stored for 'lifetime' seconds
		$_check = $cache->call( array( $modelUpdate, '_getUpdate') );
		// revert configuration
		$cache->setCaching($cacheactive);
		
		$view->assign('check', $_check);
		$view->assign('alphacontent_configuration', $model->_configuration);

		$view->edit();
	}

	/**
	* Save the configuration file
	*/
	function save() {		
		// get the model
		$model = &$this->getModel('alphacontent');
		if ( $model->_save_configuration() ) {
			$this->setRedirect('index.php?option=com_alphacontent', JTEXT::_('AC_CONFIGURATION_SAVED'));
		} else {
			$this->setRedirect('index.php?option=com_alphacontent', JTEXT::_('AC_CONFIGURATION_ERROR'));
		}
	}

}

?>