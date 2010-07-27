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
		$allowed = array('google', 'yahoo', 'myspace', 'aol', 'openid');
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
				case 'openid':
					if (isset($_POST['openid']) && ! empty($_POST['openid']))
					{
						$this->openid->try_auth_sreg($_POST['openid'], 'test/finish_auth');
					}
					else
					{
						$this->load->view('test', array('openid' => true));
					}
				break;
				default:
					$this->load->view('test', array('data' => 'Invalid Provider'));
				break;
			}
		}
		else
		{
			$this->load->view('test', array('data' => 'Invalid Provider'));
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
	
	function load_icon()
	{
		$which = $this->uri->segment(3);
		$allowed = array('google', 'yahoo', 'myspace', 'blogger', 'aol', 'openid');
		if (in_array($which, $allowed))
		{
			$icon = OPENID_DIRECTORY.'EasyOpenID_Icons/'.$which.'.png';
			$icon = file_get_contents($icon);
			header('Content-Type: image/png');
			die($icon);
		}
	}

}

/* End of file test.php */
/* Location: ./system/application/controllers/test.php */
