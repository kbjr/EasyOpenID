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

/**
 * CodeIgniter OAuth Class
 *
 * Adds various authentication abilities for using APIs such as Facebook
 * Connect and Twitter Signin.
 *
 * @package		CodeIgniter
 * @subpackage	EasyOpenID
 * @category	Libraries
 * @author		James Brumond
 * @link		http://code.kbjrweb.com/project/easyopenid
 */

class OAuth {

	public function __construct()
	{
		$this->facebook = new OAuth_Facebook();
		$this->twitter  = new OAuth_Twitter();
	}
	
	public $facebook;
	public $twitter;

}


/**
 * Does facebook authentication
 *
 * @class   OAuth_Facebook
 * @parent  OAuth
 */

class OAuth_Facebook { 
	
	public function __construct()
	{
		
	}
	
	// Application info
	protected $app_id;
	protected $secret;
	
	/**
	 * Initializes the class.
	 *
	 * @access  public
	 * @param   string    the app ID
	 * @param   string    the "secret"
	 * @return  self
	 */
	public function initialize($app_id, $secret)
	{
		$this->app_id = $app_id;
		$this->secret = $secret;
		return $this;
	}
	
	/**
	 * Returns the needed markup to include the Facebook SDK into
	 * the website. This should not be put in the <head> of a document
	 * as it contains a <div> element.
	 *
	 * @access  public
	 * @return  string
	 */
	public function javascript_sdk()
	{
		return '<div id="fb-root"></div>'."\n".'<script src="http://connect.facebook.net/en_US/all.js"></script>';
	}
	
	/**
	 * Creates and returns both the markup and javascript required
	 * to run a facebook authentication sequence.
	 *
	 * @access  public
	 * @param   
	 */
	public function create_connect_button($perms = '', $on_login = null, $on_logout = null, $needs_sdk = true)
	{
		// Make sure this has been initialized
		if (! $this->app_id || ! $this->secret)
		{
			throw new OAuth_Exception
		}
		// Build perms string from array
		if (is_array($perms))
		{
			$perms = implode(',', $perms);
		}
		// Make sure we have a valid perms string
		if (! is_string($perms)) return false;
		// Build the button markup
		$button = '<fb:login-button perms="'.$perms.'"></fb:login-button>';
		// Build the initialization code
		$init_code = ($needs_sdk) ? $this->javascript_sdk()."\n" : '';
		$init_code .= implode("\n", array(
			'<script>',
			'  FB.init({appId: \'your app id\', status: true, cookie: true, xfbml: true});',
			'  FB.Event.subscribe(\'auth.sessionChange\', function(response) {',
			'	if (response.session) {',
			'		'.(($on_login) ? $on_login.';' : '// A user has logged in, and a new cookie has been saved'),
			'	} else {',
			'		'.(($on_logout) ? $on_logout.';' : '// The user has logged out, and the cookie has been cleared'),
			'	}',
			'  });',
			'</script>'
		));
	}
	
}



// The unique exception class for OAuth errors; nothing special,
// just to seperate their's from our's.
class OAuth_Exception extends Exception { }



/* End of file OAuth.php */
/* Location: ./system/libraries/OAuth.php */
