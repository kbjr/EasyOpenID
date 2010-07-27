<?php

class Test extends Controller {

	function Test()
	{
		parent::Controller();
		$this->load->library('OpenID');
		$this->load->helper('form');
	}
	
	function index()
	{
		$this->load->view('test');
	}
	
	function third_party()
	{
		$which = $this->uri->segment(3);
		$allowed = array('google', 'yahoo', 'myspace', 'aol', 'openid', 'openid-form');
		if (in_array($which, $allowed, true))
		{
			switch ($which)
			{
				case 'google':
					$this->openid->try_auth_google('test/finish_auth');
				break;
				case 'yahoo':
					$this->openid->try_auth_yahoo('test/finish_auth');
				break;
				case 'aol':
					$this->openid->try_auth_aol('test/finish_auth');
				break;
				case 'openid':
					$this->load->view('test', array('openid' => true));
				break;
				case 'openid-form':
					$this->openid->try_auth_sreg($_POST['id'], 'test/finish_auth');
				break;
				default:
					$this->load->view('test', array('data' => 'fail'));
				break;
			}
		}
		else
		{
			$this->load->view('test', array('data' => 'fail'));
		}
	}
	
	function finish_auth()
	{
		$result = $this->openid->finish_auth();
		if (is_int($result))
		{
			$data = array('data' => $this->openid->last_error());
		}
		else
		{
			$data = array('data' => $result);
		}
		$this->load->view('test', $data);
	}
	
	function build()
	{
		header('Content-Type: text/plain');
		$this->openid->build_openid_auth('test/build');
	}

}

/* End of file test.php */
/* Location: ./system/application/controllers/test.php */
