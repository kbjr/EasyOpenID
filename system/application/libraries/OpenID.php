<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/*
 * OpenID Directory Path
 *
 * @const  OPENID_DIRECTORY
 */
define('OPENID_DIRECTORY', dirname(__FILE__)."/php-openid/");

/**
 * CodeIgniter OpenID Class
 *
 * This class enables the easy use of OpenID authentication with auto-access
 * to several OpenID servers as well as a general OpenID URI entry ability.
 *
 * @package		CodeIgniter
 * @subpackage	EasyOpenID
 * @category	Libraries
 * @author		James Brumond
 * @link		http://code.kbjrweb.com/project/easyopenid
 */
class OpenID {

/*
 * Statics
 */
	
	protected static $CI = null;
	
	protected static function class_init()
	{
		if (self::$CI === null)
			self::$CI =& get_instance();
	}
	
	protected static function do_includes()
	{
		/**
		 * Require the OpenID consumer code.
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/Consumer.php';
		/**
		 * Require the needed "store" file.
		 */
		switch (self::$CI->config->item('store_method', 'openid')) {
			case 'file':
				require_once OPENID_DIRECTORY.'Auth/OpenID/FileStore.php';
			break;
			case 'mysql':
				require_once OPENID_DIRECTORY.'Auth/OpenID/MySQLStore.php';
			break;
			case 'postgresql':
				require_once OPENID_DIRECTORY.'Auth/OpenID/PostgreSQLStore.php';
			break;
			case 'sqlite':
				require_once OPENID_DIRECTORY.'Auth/OpenID/SQLiteStore.php';
			break;
			default:
				throw new Exception("OpenID store_method is invalid.");
			break;
		}
		/**
		 * Require the Simple Registration extension API.
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/SReg.php';
		/**
		 * Require the PAPE extension module.
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/PAPE.php';
	}

/*
 * Magic Methods
 */
	
	public function __construct()
	{
		self::class_init();
		$this->ci =& self::$CI;
		$this->ci->config->load('openid', true);
		self::do_includes();
	}

/*
 * Private Properties
 */
	
	protected $ci = null;

/*
 * Public Properties
 */
	
	//...

/*
 * Private Methods
 */
	
	//...

/*
 * Public Methods
 */
	
	//...

}















/* End of file CI_OpenID.php */
/* Location: ./system/application/libraries/CI_OpenID.php */
