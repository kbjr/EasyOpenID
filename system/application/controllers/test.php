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
		$policies = ((array_key_exists('policies', $_POST)) ? $_POST['policies'] : array());
		$result = $this->openid->try_auth($_POST['openid_identifier'], 'test/finish_auth', $policies);
		if (is_string($result)) echo $result;
	}
	
	function finish_auth()
	{
		$result = $this->openid->finish_auth();
		echo '<pre>';
		print_r($result);
		echo '</pre>';
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
