<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends MY_Controller
{

	/**
	 * 注册页面控制器
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/register
	 *	- or -
	 * 		http://example.com/index.php/register/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$errors = '';
		if($this->webuser->isLogin())
		{
			$this->load->helper('url');
			redirect('/user/hub');
			exit();
		}
		if($this->isPostRequest())
		{
			$form = $this->inputPost('Register');
			$user_name = $form['user_name'];
			$password = $form['password'];
			$re_password = $form['re_password'];
			$email = $form['email'];

			$this->loadUserModel();
			$errors = UserPeer::model()->register($user_name,$password,$re_password,$email);
			if(!$errors)
			{
				$this->webuser->login($user_name, $password);
				$this->load->helper('url');
				redirect('/register/ok');
				exit();
			}
		}
	    $this->navbar->setCurrentItem(NavBar::ITEM_REGISTER);
	    $this->navbar->setHeaderTitle('注册会员');
	    $this->setTitle("注册会员--自动任务");

	    $data = compact('errors','form');
		$this->view('/register/register',$data);
	}

	public function ok()
	{
		$this->navbar->hideSignIn();
	    $this->navbar->setCurrentItem(NavBar::ITEM_REGISTER);
	    $this->setTitle("注册成功--自动任务");

		$this->view('/register/register_ok');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */