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
 * CodeIgniter EasyOpenID Class
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

require_once dirname(__FILE__).'/OpenID.php';

class Eoid {

	public $provider       = null;
	public $provider_data  = null;
	public $return_to      = null;
	public $required       = array( );
	public $optional       = array( );
	public $policies       = array(
		PAPE_AUTH_MULTI_FACTOR_PHYSICAL => 0,
		PAPE_AUTH_MULTI_FACTOR          => 0,
		PAPE_AUTH_PHISHING_RESISTANT    => 0
	);

}

class EasyOpenID extends OpenID {
	
	protected $eoid = null;

	public function __construct()
	{
		parent::__construct();
		$this->eoid = new Eoid();
	}
	
	public function reset()
	{
		$this->eoid = new Eoid();
		return $this;
	}
	
	public function set_policy($policy, $flag = true)
	{
		$flag = ($flag) ? 1 : 0;
		if (array_key_exists($policy, $this->eoid->policies))
		{
			$this->eoid->policies[$policy] = $flag;
		}
		return $this;
	}
	
	public function multi_factor($flag = true)
	{
		$this->set_policy(PAPE_AUTH_MULTI_FACTOR, $flag);
		return $this;
	}
	
	public function multi_factor_physical($flag = true)
	{
		$this->set_policy(PAPE_AUTH_MULTI_FACTOR_PHYSICAL, $flag);
		return $this;
	}
	
	public function phishing_resistant($flag = true)
	{
		$this->set_policy(PAPE_AUTH_PHISHING_RESISTANT, $flag);
		return $this;
	}	
	
	public function provider($provider, $provider_data = null)
	{
		if (is_string($provider))
			$this->eoid->provider = $provider;
		if (is_string($provider_data))
			$this->eoid->provider_data = $provider_data;
		return $this;
	}
	
	public function return_to($route)
	{
		if (is_string($route))
			$this->eoid->return_to = $route;
		return $this;
	}
	
	public function required($value)
	{
		if (is_string($value))
			$this->eoid->required[] = $value;
		if (is_array($value))
			$this->eoid->required = array_merge($value, $this->eoid->required);
		$this->eoid->required = array_unique($this->eoid->required);
		return $this;
	}
	
	public function optional($value)
	{
		if (is_string($value))
			$this->eoid->optional[] = $value;
		if (is_array($value))
			$this->eoid->optional = array_merge($value, $this->eoid->optional);
		$this->eoid->optional = array_unique($this->eoid->optional);
		return $this;
	}
	
	protected $types = array(
		'google'  => 'ax',
		'yahoo'   => 'ax',
		'myspace' => 'sreg',
		'blogger' => 'sreg',
		'aol'     => 'sreg'
	);
	
	protected function parse_sreg($from, &$to)
	{
		foreach ($from as $field)
		{
			switch ($field)
			{
				case 'fullname':
				case 'name':
					$to[] = 'fname';
					$to[] = 'lname';
				break;
				case 'nickname':
				case 'username':
					$to[] = 'nickname';
				break;
				case 'email':
					$to[] = 'email';
				break;
			}
		}
		$to = array_unique($to);
	}
	
	protected function parse_ax($from, &$to)
	{
		foreach ($from as $field)
		{
			switch ($field)
			{
				case 'fname':
				case 'firstname':
					$to[] = 'fname';
				break;
				case 'lname':
				case 'lastname':
					$to[] = 'lname';
				break;
				case 'fullname':
				case 'name':
					$to[] = 'fname';
					$to[] = 'lname';
				break;
				case 'nickname':
				case 'username':
					$to[] = 'nickname';
				break;
				case 'email':
					$to[] = 'email';
				break;
			}
		}
		$to = array_unique($to);
	}
	
	protected function parse_request($type)
	{
		$required = $optional = array( );
		if ($type = 'sreg')
		{
			$this->parse_sreg($this->eoid->required, $required);
			$this->parse_sreg($this->eoid->optional, $optional);
		}
		else
		{
			$this->parse_ax($this->eoid->required, $required);
			$this->parse_ax($this->eoid->optional, $optional);
		}
		return array($required, $optional);
	}
	
	protected function parse_policies()
	{
		$policies = array( );
		foreach ($this->eoid->policies as $policy => $flag)
		{
			if ($flag) $policies[] = $policy;
		}
		return $policies;
	}
	
	public function make_request($type = 'sreg')
	{
		if (isset($this->types[$this->eoid->provider]))
		{
			$type = $this->types[$this->eoid->provider];
		}
		if (is_string($this->eoid->provider) && is_string($this->eoid->return_to))
		{
			$return = $this->eoid->return_to;
			$policies = $this->parse_policies();
			list($required, $optional) = $this->parse_request($type);
			switch ($this->eoid->provider)
			{
				case 'google':
					$result = $this->try_auth_google($return, $policies, $required, $optional);
				break;
				case 'yahoo':
					$result = $this->try_auth_yahoo($return, $policies, $required, $optional);
				break;
				case 'myspace':
					$result = $this->try_auth_myspace($return, $policies, $required, $optional);
				break;
				case 'blogger':
					$result = $this->try_auth_blogger($this->eoid->provider_data, $return, $policies, $required, $optional);
				break;
				case 'aol':
					$result = $this->try_auth_aol($return, $policies, $required, $optional);
				break;
				default:
					if (strtolower($type) == 'sreg')
					{
						$result = $this->try_auth_sreg($this->eoid->provider, $return, $policies, $required, $optional);
					}
					elseif (strtolower($type) == 'ax')
					{
						$result = $this->try_auth_ax($this->eoid->provider, $return, $policies, $required, $optional);
					}
					else
					{
						$this->_error = 'Invalid provider type "'.$type.'"';
						return OPENID_RETURN_FAILURE;
					}
				break;
			}
		}
		else
		{
			return OPENID_RETURN_NO_URL;
		}
	}
	
	public function fetch_result()
	{
		return $this->finish_auth();
	}

}




/* End of file EasyOpenID.php */
/* Location: ./system/application/libraries/EasyOpenID.php */
