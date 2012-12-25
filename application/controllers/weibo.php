<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weibo extends MY_Controller
{
	public function index()
	{
		header('Location: /');
		exit();
	}

	/**
	 * TODO weibo callback
	 */
	public function callback()
	{
		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/weibo.php */