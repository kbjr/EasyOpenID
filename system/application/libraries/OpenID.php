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
	
	protected static $default_config = array(
		'store_method' => 'file',
		'store_path'   => '/tmp/_php_consumer_test'
	);
	
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
		$store_type = self::$CI->config->item('store_method', 'openid');
		$this->store_type = ((! $store_type) ? self::$default_config['store_method'] : $store_type);
		switch ($this->store_type)
		{
			case 'file':
				require_once OPENID_DIRECTORY.'Auth/OpenID/FileStore.php';
			break;
			case 'database':
				require_once OPENID_DIRECTORY.'EasyOpenID_Database.php';
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
		$this->ci->load->library('session');
		self::do_includes();
		$this->pape_policy_uris = array(
			PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
			PAPE_AUTH_MULTI_FACTOR,
			PAPE_AUTH_PHISHING_RESISTANT
		);
	}

/*
 * Private Properties
 */
	
	protected $ci = null;
	
	protected $store_type = null;

/*
 * Public Properties
 */
	
	public $pape_policy_uris = null;

/*
 * Private Methods
 */
	
	protected function _read_config($item)
	{
		$conf = $this->ci->config->item($item, 'openid');
		$conf = ((! $conf && array_key_exists($item, self::$default_config)) ?
			self::$default_config[$item] : $conf);
		return $conf;
	}

/*
 * Public Methods
 */
	
	public function &getStore()
	{
		switch ($this->store_type)
		{
			case 'file':
				$store_path = $this->_read_config('store_path');
				if (!file_exists($store_path) && !mkdir($store_path))
				{
					throw new Exception("Could not create the FileStore directory '$store_path'. ".
					" Please check the effective permissions.");
				}
				$r = new Auth_OpenID_FileStore($store_path);
			break;
			case 'database':
				
			break;
		}
		return $r;
	}

}















/* End of file OpenID.php */
/* Location: ./system/application/libraries/OpenID.php */
