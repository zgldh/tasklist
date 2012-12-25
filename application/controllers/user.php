<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller
{
	public function index()
	{
		header('Location: /');
		exit();
	}

	public function hub()
	{
	    $this->needLoginOrExit('/user/hub');
	    
	    $this->setTitle("用户中心--自动任务");
	    $this->navbar->setHeaderTitle('用户中心');
	    $this->javascript_css_manager->addStyleFile('/css/tl-user-hub.css');
	    
	    $this->view('/user/hub');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/user.php */