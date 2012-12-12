<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller
{
	public function index()
	{
	    $this->navbar->hideNavBar();
	    $this->setTitle("首页--自动任务");

	    $data = array();

		$this->view('/home/homepage', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */