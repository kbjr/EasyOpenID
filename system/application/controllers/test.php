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
	
	function try_auth()
	{
		$this->openid->try_auth('test/finish_auth', $_POST['openid_identifier'], $_POST['policies']);
	}
	
	function finish_auth()
	{
		$this->openid->finish_auth();
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
