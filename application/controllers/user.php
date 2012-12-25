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

	    $current_user = $this->webuser->getUser();
	    $weibo_link = $current_user->getWeiboLink();
	    $this->loadSaeTAuthV2();
	    if($weibo_link)
	    {
	    }
	    else
	    {
	    	$state = uniqid( 'weibo_', true);
	    	$this->webuser->setSessData('weibo_state',$state);
	    	$weibo_oauth_url = $this->sae_oauth->getAuthorizeURL(WB_CALLBACK_URL,'code',$state);
	    }
	    
	    $this->setTitle("用户中心--自动任务");
	    $this->navbar->setHeaderTitle('用户中心');
	    $this->javascript_css_manager->addStyleFile('/css/tl-user-hub.css');
	    
	    $data = compact('current_user','weibo_link','weibo_oauth_url');
	    
	    $this->view('/user/hub',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/user.php */