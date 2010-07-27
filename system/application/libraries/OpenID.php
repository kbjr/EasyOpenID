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

/*
 * No OpenID URL Given
 *
 * @const  OPENID_RETURN_NO_URL
 */
define('OPENID_RETURN_NO_URL', 10);

/*
 * Bad OpenID URL Given
 *
 * @const  OPENID_RETURN_BAD_URL
 */
define('OPENID_RETURN_BAD_URL', 20);

/*
 * Could not connect to verifying server
 *
 * @const  OPENID_RETURN_NO_CONNECT
 */
define('OPENID_RETURN_NO_CONNECT', 30);

/*
 * Verification canceled
 *
 * @const  OPENID_RETURN_CANCEL
 */
define('OPENID_RETURN_CANCEL', 40);

/*
 * Verification failure
 *
 * @const  OPENID_RETURN_FAILURE
 */
define('OPENID_RETURN_FAILURE', 50);

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
		'store_method'       => 'file',
		'store_path'         => '/tmp/_php_consumer_test',
		'associations_table' => 'oid_associations',
		'nonces_table'       => 'oid_nonces',
		'popup_auth'         => TRUE
	);
	
	/**
	* Initialize the class.
	*
	* @access  protected
	* @return  void
	*/
	protected static function class_init()
	{
		if (self::$CI === null)
			self::$CI =& get_instance();
	}
	
	/**
	* Reads an item from config
	*
	* @access  protected
	* @param   string   the item to read
	* @return  mixed
	*/
	protected static function read_config($item)
	{
		$conf = self::$CI->config->item($item, 'openid');
		$conf = ((! $conf && array_key_exists($item, self::$default_config)) ?
			self::$default_config[$item] : $conf);
		return $conf;
	}
	
	/**
	* Include needed files
	*
	* @access  protected
	* @return  void
	*/
	protected static function do_includes()
	{
		/**
		 * Require the OpenID consumer code.
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/Consumer.php';
		/**
		 * Require the needed "store" file.
		 */
		$store_type = self::read_config('store_method');
		switch ($store_type)
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
		 * Require the Simple Registration extension API. (just about everyone)
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/SReg.php';
		/**
		 * Require the AX extension API. (google and yahoo)
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/AX.php';
		/**
		 * Require the PAPE extension module.
		 */
		require_once OPENID_DIRECTORY.'Auth/OpenID/PAPE.php';
		/**
		 * Require the session class.
		 */
		require_once OPENID_DIRECTORY.'EasyOpenID_Session.php';
	}

/*
 * Magic Methods
 */
	
	/**
	* Constructor
	*
	* @access  public
	* @return  void
	*/
	public function __construct()
	{
		self::class_init();
		$this->ci =& self::$CI;
		$this->ci->config->load('openid', TRUE);
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
	
	protected $ax_properties = array(
		'email'     => 'http://axschema.org/contact/email',
		'firstname' => 'http://axschema.org/namePerson/first',
		'fname'     => 'http://axschema.org/namePerson/first',
		'lastname'  => 'http://axschema.org/namePerson/last',
		'lname'     => 'http://axschema.org/namePerson/last',
		'username'  => 'http://axschema.org/namePerson/friendly',
		'nickname'  => 'http://axschema.org/namePerson/friendly'
	);
	
	protected $ax_aliases = array(
		'http://axschema.org/contact/email'       => 'email',
		'http://axschema.org/namePerson/first'    => 'firstname',
		'http://axschema.org/namePerson/last'     => 'lastname',
		'http://axschema.org/namePerson/friendly' => 'nickname'
	);
	
	protected $providers = array(
		'openid'  => 'Sign in with OpenID',
		'google'  => 'Sign in using your Google Account',
		'yahoo'   => 'Sign in using Yahoo!',
		'blogger' => 'Sign in with your Blogger Account',
		'myspace' => 'Sign in using MySpaceID',
		'aol'     => 'Sign in with your AOL Account'
	);
	
	protected $_error = null;

/*
 * Public Properties
 */
	
	public $pape_policy_uris = null;

/*
 * Private Methods
 */
	
	/**
	* Read an item from config.
	*
	* @access  protected
	* @param   string   the item to read
	* @return  mixed
	*/
	protected function _read_config($item)
	{
		$conf = $this->ci->config->item($item, 'openid');
		$conf = ((! $conf && array_key_exists($item, self::$default_config)) ?
			self::$default_config[$item] : $conf);
		return $conf;
	}

	/**
	* Create a store object.
	*
	* @access  protected
	* @return  mixed
	*/
	protected function &_get_store()
	{
		$store_type = $this->_read_config('store_method');
		switch ($store_type)
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
				$conn = new EasyOpenID_Database();
				$r = new Auth_OpenID_SQLStore($conn,
					$this->_read_config('associations_table'),
					$this->_read_config('nonces_table'));
			break;
		}
		return $r;
	}
	
	protected function &_get_session()
	{
		$r = new OpenID_Session();
		return $r;
	}

	/**
	* Create a consumer object.
	*
	* @access  protected
	* @return  Auth_OpenID_Consumer
	*/
	protected function &_get_consumer()
	{
		$store = $this->_get_store();
		$sess = $this->_get_session();
		$r = new Auth_OpenID_Consumer($store, $sess);
		return $r;
	}

	/**
	* Create the current scheme (http or https).
	*
	* @access  protected
	* @return  string
	*/
	protected function _get_scheme()
	{
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on')
		{
			$scheme .= 's';
		}
		return $scheme;
	}

	/**
	* Create the base url.
	*
	* @access  protected
	* @return  string
	*/
	protected function _get_trust_root()
	{
		return $this->ci->config->item('base_url');
	}

	/**
	* Escape a string.
	*
	* @access  protected
	* @param   string   the string to escape
	* @return  string
	*/
	protected function _escape($str)
	{
		return htmlentities($str);
	}

	/**
	* Return a url to the current path.
	*
	* @access  protected
	* @return  string
	*/
	protected function _get_self()
	{
		return $this->ci->config->item('base_url').substr($this->ci->uri->uri_string(), 1);
	}

	/**
	* Turns an absolute path into a URL.
	*
	* @access  protected
	* @param   string   the path to convert
	* @return  string
	*/
	protected function _make_url($path)
	{
		return ((empty($_SERVER['HTTPS'])) ? "http" : "https") . "://" .
			$_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);
	}

	/**
	* Creates the <img /> tag for an OpenID icon
	*
	* @access  protected
	* @param   string   image url
	* @return  string
	*/
	protected function _build_icon($url)
	{
		return '<img src="'.$url.'" alt="" title="" width="16" height="16" />';
	}

/*
 * Public Methods
 */

	/**
	* Get the last occuring error.
	*
	* @access  public
	* @return  string
	*/
	public function last_error()
	{
		return $this->_error;
	}

	/**
	* Try to authenticate a user on Google accounts
	*
	* @access  public
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @return  string
	*/
	public function try_auth_google($return_to, $policy_uris = array())
	{
		return $this->try_auth_ax('https://www.google.com/accounts/o8/id', $return_to, $policy_uris,
			array('email'), array('firstname', 'lastname'));
	}

	/**
	* Try to authenticate a user on Yahoo! accounts
	*
	* @access  public
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @return  string
	*/
	public function try_auth_yahoo($return_to, $policy_uris = array())
	{
		return $this->try_auth_ax('https://www.yahoo.com', $return_to, $policy_uris);
	}

	/**
	* Try to authenticate a user on MySpaceID accounts
	*
	* @access  public
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @return  string
	*/
	public function try_auth_myspace($return_to, $policy_uris = array())
	{
		return $this->try_auth_sreg('http://www.myspace.com', $return_to, $policy_uris);
	}

	/**
	* Try to authenticate a user on Blogger/Blogspot accounts
	*
	* @access  public
	* @param   string   the *.blogspot.com subdomain for the user's blog
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @return  string
	*/
	public function try_auth_blogger($blog_name, $return_to, $policy_uris = array())
	{
		return $this->try_auth_sreg('http://'.$blog_name.'.blogspot.com', $return_to, $policy_uris);
	}

	/**
	* Try to authenticate a user on AOL accounts
	*
	* @access  public
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @return  string
	*/
	public function try_auth_aol($return_to, $policy_uris = array())
	{
		return $this->try_auth_sreg('openid.aol.com', $return_to, $policy_uris);
	}

	/**
	* Try to authenticate a user using AX.
	*
	* @access  public
	* @param   string   the openid url
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @param   array    required data
	* @param   array    optional data
	* @return  void
	*/
	public function try_auth_ax($openid, $return_to, $policy_uris = array(),
		$required = array('nickname', 'email'), $optional = array('fname', 'lname'))
	{
		if ($return_to[0] == '/')
			$return_to = substr($return_to, 1);
		$return_to = $this->_get_trust_root().$return_to;
		
		if (empty($openid))
		{
			return OPENID_RETURN_NO_URL;
		}
		
		// Create OpenID consumer
		$consumer = $this->_get_consumer();

		// Create an authentication request to the OpenID provider
		$auth = $consumer->begin($openid);

		// Create AX fetch request
		$ax = new Auth_OpenID_AX_FetchRequest;

		// Create attribute request object
		// See http://code.google.com/apis/accounts/docs/OpenID.html#Parameters for parameters
		foreach ($required as $item)
		{
			if (array_key_exists($item, $this->ax_properties))
			{
				$ax->add(Auth_OpenID_AX_AttrInfo::make($this->ax_properties[$item], 1, 1, $item));
			}
			else
			{
				throw new Exception('AX property "'.$item.'" is not registered in the library', E_USER_NOTICE);
			}
		}
		foreach ($optional as $item)
		{
			if (array_key_exists($item, $this->ax_properties))
			{
				$ax->add(Auth_OpenID_AX_AttrInfo::make($this->ax_properties[$item], 1, 0, $item));
			}
			else
			{
				throw new Exception('AX property "'.$item.'" is not registered in the library', E_USER_NOTICE);
			}
		}

		// Add AX fetch request to authentication request
		$auth->addExtension($ax);

		// Redirect to OpenID provider for authentication
		$url = $auth->redirectURL($this->_get_trust_root(), $return_to);
		
		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($auth->shouldSendRedirect())
		{
			$redirect_url = $auth->redirectURL($this->_get_trust_root(), $return_to);

			// If the redirect URL can't be built, die.
			if (Auth_OpenID::isFailure($redirect_url))
			{
				return OPENID_RETURN_NO_CONNECT;
			}
			else
			{
				// Send redirect.
				header("Location: ".$redirect_url);
			}
		}
		else
		{
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth->htmlMarkup(
				$this->_get_trust_root(), $return_to, FALSE, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html))
			{
				return OPENID_RETURN_NO_CONNECT;
			}
			else
			{
				echo $form_html;
			}
		}
	}

	/**
	* Try to authenticate a user using SReg.
	*
	* @access  public
	* @param   string   the openid url
	* @param   string   the path to return to after authenticating
	* @param   array    a list of PAPE policies to request from the server
	* @param   array    required data
	* @param   array    optional data
	* @return  void
	*/
	public function try_auth_sreg($openid, $return_to, $policy_uris = array(),
		$required = array('nickname', 'email'), $optional = array('fullname'))
	{
		if ($return_to[0] == '/')
			$return_to = substr($return_to, 1);
		$return_to = $this->_get_trust_root().$return_to;
		
		if (empty($openid))
		{
			return OPENID_RETURN_NO_URL;
		}
		
		$consumer = $this->_get_consumer();

		// Begin the OpenID authentication process.
		$auth_request = $consumer->begin($openid);

		// No auth request means we can't begin OpenID.
		if (! $auth_request)
		{
			return OPENID_RETURN_BAD_URL;
		}

		$sreg_request = Auth_OpenID_SRegRequest::build($required, $optional);

		if ($sreg_request)
		{
			$auth_request->addExtension($sreg_request);
		}

		$policy_uris = null;

		$pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
		if ($pape_request)
		{
			$auth_request->addExtension($pape_request);
		}

		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.

		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($auth_request->shouldSendRedirect())
		{
			$redirect_url = $auth_request->redirectURL($this->_get_trust_root(), $return_to);

			// If the redirect URL can't be built, die.
			if (Auth_OpenID::isFailure($redirect_url))
			{
				return OPENID_RETURN_NO_CONNECT;
			}
			else
			{
				// Send redirect.
				header("Location: ".$redirect_url);
			}
		}
		else
		{
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->htmlMarkup(
				$this->_get_trust_root(), $return_to, FALSE, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html))
			{
				return OPENID_RETURN_NO_CONNECT;
			}
			else
			{
				echo $form_html;
			}
		}
	}

	/**
	* Finish up authentication.
	*
	* @access  public
	* @return  string
	*/
	public function finish_auth()
	{
		$msg = $error = $success = '';
		$consumer = $this->_get_consumer();

		// Complete the authentication process using the server's response.
		$response = $consumer->complete($this->_get_self());

		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL)
		{
			$this->_error = 'request was canceled';
			return OPENID_RETURN_CANCEL;
		}
		else if ($response->status == Auth_OpenID_FAILURE)
		{
			$this->_error = $response->message;
			return OPENID_RETURN_FAILURE;
		}
		else if ($response->status == Auth_OpenID_SUCCESS)
		{
			// if AX
			if ($response->endpoint->used_yadis)
			{
				$ax_resp = new Auth_OpenID_AX_FetchResponse();
				$data = $ax_resp->fromSuccessResponse($response)->data;
				$new_data = array();
				foreach ($data as $i => $item)
				{
					if (array_key_exists($i, $this->ax_aliases))
					{
						$new_data[$this->ax_aliases[$i]] = $item;
					}
					else
					{
						$new_data[$i] = $item;
					}
				}
				$data = $new_data;
			}
			// if SReg
			else
			{
				$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
				$data = $sreg_resp->contents();
			}

			return $data;
		}
		
		return OPENID_RETURN_FAILURE;
	}

	/**
	* Build an OpenID authentication markup.
	*
	* @access  public
	* @param   string   where should the links go
	* @param   array    a list of providers to offer
	* @param   bool     authenticate with popups
	* @return  string
	*/
	public function build_openid_auth($handler = null, $providers = array('openid', 'google', 'yahoo'),
		$icon_loader = null, $popups = null)
	{
		if (! is_string($handler) || empty($handler))
		{
			throw new Exception('redirect url is required');
		}
		
		if ($handler[0] == '/')
		{
			$handler = substr($handler, 1);
		}
		
		if (is_string($icon_loader))
		{
			if($icon_loader[0] == '/')
			{
				$icons = substr($icons, 1);
			}
			$icons = $this->_get_trust_root().$icon_loader.'/';
		}
		else
		{
			$icons = $this->_make_url(OPENID_DIRECTORY.'EasyOpenID_Icons/');
		}
		
		if ($popups === null)
		{
			$popups = $this->_read_config('popup_auth');
		}
		$popups =!! $popups;
		
		$links = array();
		foreach ($providers as $provider)
		{
			if (array_key_exists($provider, $this->providers))
			{
				$icon = $icons.$provider.(($icon_loader) ? '' : '.png');
				$link = (object) array(
					'provider' => $provider,
					'href'     => $handler.'/'.$provider,
					'rel'      => 'openid',
					'text'     => $this->providers[$provider],
					'icon'     => $this->_build_icon($icon),
					'anchor'   => null
				);
				$link->anchor = '<a href="'.$link->href.'" rel="'.$link->rel.'">'.$link->icon.
					'<span>'.$link->text.'</span></a>';
				$links[] = $link;
				unset($link);
			}
			else
			{
				throw new Exception('provider "'.$provider.'" does not exist in the EasyOpenID system', E_USER_NOTICE);
			}
		}
		return $links;
	}

}















/* End of file OpenID.php */
/* Location: ./system/application/libraries/OpenID.php */
