<?php

class Test extends Controller {

	function Test()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->library('OpenID');
		$this->load->view('test');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
