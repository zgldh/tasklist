<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @author zgldh
 *
 * @property CI_DB_active_record $db
 */
class MY_Controller extends CI_Controller{

    
	/**
	 * 
	 * @var JavascriptCssManager
	 */
	public $javascript_css_manager = null;
    /**
     * 当前正在访问的用户。
     * @var WebUser
     */
    public $webuser = null;

    /**
     * 顶部导航栏
     * @var NavBar
     */
    public $navbar = null;
    
    /**
     * 
     * @var SaeTOAuthV2
     */
    public $sae_oauth = null;
    /**
     * 
     * @var SaeTClientV2
     */
    public $sae_client = null;

    private $_javascripts = array();
    private $_auto_javascript_codes = array();
    private $_styles = array();
    private $_style_codes = array();
    private $_title = null;

    private $_show_navbar = true;
    private $_current_navbar_item = 'home';

    function __construct()
    {
        parent::__construct();
        
        include_once(dirname(__FILE__).'/JavascriptCssManager.php');
        $this->javascript_css_manager = new JavascriptCssManager();
    }
    
    
    /***
     * load models functions start
     */
    
    /**
     * 
     * @var User_model
     */
    public $user_model = null;
    /**
     * 
     * @var Invitation_model
     */
    public $invitation_model = null;
    /**
     * 
     * @var Task_model
     */
    public $task_model = null;
    /**
     *
     * @var Task_trigger_model
     */
    public $task_trigger_model = null;
    /**
     * 
     * @var Task_command_model
     */
    public $task_command_model = null;
    /**
     * 
     * @var App_model
     */
    public $app_model = null;
    /**
     *
     * @var App_trigger_model
     */
    public $app_trigger_model = null;
    /**
     * 
     * @var App_command_model
     */
    public $app_command_model = null;
    /**
     * 
     * @var App_active_model
     */
    public $app_active_model = null;
    
    
    /**
     * 
     * @var Timing_process_model
     */
    public $timing_process_model = null;

    /**
     * 
     * @var Process_log_model
     */
    public $process_log_model = null;
    /**
     * 
     * @var Report_email_model
     */
    public $report_email_model = null;
    /**
     * 
     * @var Weibo_link_model
     */
    public $weibo_link_model = null;
    /**
     * 
     * @var Kitco_gold_model
     */
    public $kitco_gold_model = null;
    /**
     * 
     * @var Weather_city_model
     */
    public $weather_city_model = null;
    /**
     * 
     * @var Weather_config_model
     */
    public $weather_config_model = null;
    /**
     * 
     * @var Weather_record_model
     */
    public $weather_record_model = null;
    
    function loadUserModel()
    {
    	$this->load->model('User_model','user_model',true);
    }
    function loadInvitationModel()
    {
    	$this->load->model('Invitation_model','invitation_model',true);
    }
    function loadTaskModel()
    {
    	$this->load->model('Task_model','task_model',true);
    }
    function loadTaskTriggerModel()
    {
    	$this->load->model('Task_trigger_model','task_trigger_model',true);
    }
    function loadTaskCommandModel()
    {
    	$this->load->model('Task_command_model','task_command_model',true);
    }
    function loadAppActiveModel()
    {
    	$this->load->model('App_active_model','app_active_model',true);
    }
    function loadAppModel()
    {
    	$this->load->model('App_model','app_model',true);
    }
    function loadAppTriggerModel()
    {
    	$this->load->model('App_trigger_model','app_trigger_model',true);
    }
    function loadAppCommandModel()
    {
    	$this->load->model('App_command_model','app_command_model',true);
    }
    function loadTimingProcessModel()
    {
    	$this->load->model('Timing_process_model','timing_process_model',true);
    }

    function loadProcessLogModel()
    {
    	$this->load->model('Process_log_model','process_log_model',true);
    }
    function loadReportEmailModel()
    {
    	$this->load->model('Report_email_model','report_email_model',true);
    }
    function loadWeiboLinkModel()
    {
    	$this->load->model('Weibo_link_model','weibo_link_model',true);
    }
    function loadKitcoGoldModel()
    {
    	$this->load->model('Kitco_gold_model','kitco_gold_model',true);
    }
    
    function loadWeatherCityModel()
    {
    	$this->load->model('Weather_city_model','weather_city_model',true);
    }
    function loadWeatherConfigModel()
    {
    	$this->load->model('Weather_config_model','weather_config_model',true);
    }
    function loadWeatherRecordModel()
    {
    	$this->load->model('Weather_record_model','weather_record_model',true);
    }
    
    function loadSaeTAuthV2()
    {
    	$config = array(
    			'client_id'=>WB_APP_KEY,
    			'client_secret'=>WB_APP_SECRET,
    			'access_token'=>null,
    			'refresh_token'=>null);
    	$this->load->library('SaeTOAuthV2',$config,'sae_oauth');
    }
    
    /***
     * load models functions end 
     */


    /**
     *
     * @return WebUser
     */
    public function getWebUser()
    {
        return $this->webuser;
    }
    /**
     *
     * @param WebUser $webuser
     */
    public function setWebUser($webuser)
    {
        $this->webuser = $webuser;
    }

    /**
     * 设置页面标题, 在浏览器标题栏上显示
     * @param string $str
     */
    public function setTitle($str)
    {
        $this->_title = $str;
    }
    /**
     * 得到页面标题，在浏览器标题栏上显示
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * 添加一个Javascript文件
     * @param String $path
     * @return boolean true成功添加 false已经存在
     */
    public function addJavascriptFile($path)
    {
    	return $this->javascript_css_manager->addJavascriptFile($path);
    }
    /**
     * 添加一段自动执行的Javascript代码
     * @param String $path
     */
    public function addAutoRunJavascriptCode($code)
    {
    	return $this->javascript_css_manager->addAutoRunJavascriptCode($code);
    }
    /**
     * 添加一个css文件
     * @param String $path
     * @return boolean true成功添加 false已经存在
     */
    public function addStyleFile($path)
    {
    	return $this->javascript_css_manager->addStyleFile($path);
    }
    /**
     * 添加一段css代码
     * @param String css code
     */
    public function addStyleCode($code)
    {
    	return $this->javascript_css_manager->addStyleCode($code);
    }

    /**
     * 显示出一个模板来
     * @param string $view_path 模板路径
     * @param array $in_data 附加数据
     */
    protected function view($view_path, $in_data = array())
    {
        if($this->navbar->isDisplay())
        {
            $this->addAutoRunJavascriptCode("$('.dropdown-toggle').dropdown()");
        }

        $meta_data = array(
                            'title'=>$this->getTitle(),
                            'javascript_css_manager'=>$this->javascript_css_manager,
        );
        $data = array_merge($meta_data, $in_data);

        $this->load->view('common/header',$data);
        $this->load->view($view_path);
        $this->load->view('common/footer');
    }

    /**
     * 得到请求方法
     * @return string “GET”, “HEAD”，“POST”，“PUT”
     */
    protected function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否是post请求
     * @return boolean true|false
     */
    protected function isPostRequest()
    {
        return ($this->getRequestMethod() == 'POST')?true:false;
    }
    /**
     * 是否是get请求
     * @return boolean true|false
     */
    protected function isGetRequest()
    {
        return ($this->getRequestMethod() == 'GET')?true:false;
    }


	public function inputPost($key, $xss_filter = false)
	{
		return $this->input->post ( $key, $xss_filter );
	}

	public function inputGet($key, $xss_filter = false)
	{
		return $this->input->get ( $key, $xss_filter );
	}

	/**
	 * 跳转到登录页面， 登录后重定向到redirect_to_url
	 * @param string $redirect_to_url 登录后重定向到的位置
	 */
    public function signinAndRedirectTo($redirect_to_url = '/')
    {
        $redirect_to = urlencode($redirect_to_url);

        $this->load->helper('url');
        redirect('/signin?redirect_to='.$redirect_to);
        exit();
    }

    /**
     * 得到JSONP输出字符串
     * @param string $callback    回调javascript函数名字
     * @param any $parameter      回调参数. 会被json_encode
     * @param string $iframe_id = false  如果输出在iframe里面， 则需要在这里提供iframe的id
     * @return string 是一个&lt;script&gt;...js代码...&lt;/script&gt; 的字符串
     */
    public function getJSONP($callback,$parameter, $iframe_id = false)
    {
        $parameter = json_encode($parameter);
        $str = '<script>';
        if($iframe_id)
        {
            $str.= 'parent.'.$callback.'('.$parameter.',"'.$iframe_id.'");';
        }
        else
        {
            $str.= $callback.'('.$parameter.');';
        }
        $str .= '</script>';
        return $str;
    }
    
    /**
     * 需要登录，不然直接断掉
     * @param string $redirect = null 登录后跳转的地址。 为null则直接断掉链接
     */
    public function needLoginOrExit($redirect = null)
    {
    	if(!$this->webuser->isLogin())
    	{
    		if($redirect == null)
    		{
    			exit();
    		}
    		else
    		{
    			$this->signinAndRedirectTo($redirect);
    		}
    	}
    }
    /**
     * 需要命令行方式执行， 不然直接exit
     */
    public function needCliOrExit($out_string = null)
    {
    	if(!$this->input->is_cli_request())
    	{
			exit($out_string);
    	}
    	return true;
    }
    /**
     * 是否登录
     * @return boolean
     */
    public function isLogin()
    {
    	return $this->webuser->isLogin();
    }
    
    
    public function beginTransaction()
    {
        $this->db->trans_start();
    }
    public function commit()
    {
        return $this->db->trans_complete();
    }
    public function rollback()
    {
        $this->db->_trans_status = false;
        $this->db->trans_complete();
    }
}
// END Controller class

/**
 * 用于返回一个JSON响应<br />
 * success默认是false
 * @author Zhangwb
 *
 */
class Response_JSON
{
	public $success = false;
	public $msg = null;
	public $errors = null;
	public $data = null;
	
	/**
	 * 
	 * @param boolean $success = false <br />表示本响应是否成功。 前端可以根据这个状态做不同的处理。<br /> 一般来说success == false的时候， errors就要有东西
	 * @param string $msg = null <br />文本消息
	 * @param mix $data = null <br />数据
	 * @param array $errors = null <br />错误信息。 一般是数组形式 'error_code'=>'error_description'。 可以自己定义结构，以适应不同的前端。
	 */
	public function __construct($success = false,$msg = null,$data = null,$errors = null)
	{
		$this->success = $success;
		$this->msg = $msg;
		$this->errors = $errors;
		$this->data = $data;
	}
	
	public function setSuccess($boolean = true)
	{
		$this->success = $boolean;
	}
	public function setUnSuccess()
	{
		$this->success = false;
	}
	public function setErrors($errors)
	{
		$this->errors = $errors;
	}
	public function setMessage($message)
	{
		$this->msg = $message;
	}
	public function setData($data)
	{
	    if($data instanceof BasePeer)
	    {
	        $data instanceof BasePeer;
	        $this->data = $data->getVars();
	    }
	    else
	    {
		    $this->data = $data;
	    }
	}
	/**
	 * 得到本相应的json字符串
	 * @return string
	 */
	public function getJSON()
	{
		$obj = array();
		if($this->success)
		{
			$obj['success'] = true;
		}
		else
		{
			$obj['success'] = false;
		}
		
		if($this->msg)
		{
			$obj['msg'] = $this->msg;
		}
		if($this->errors)
		{
			$obj['errors'] = $this->errors;
		}
		if($this->data)
		{
			$obj['data'] = $this->data;
		}
		
		$string = json_encode($obj);
		return $string;
	}
	/**
	 * 以json字符串的形式输出该响应
	 * @param boolean $and_exit = true 是否输出后自动退出php程序
	 * @return string
	 */
	public function output($and_exit = true)
	{
		$string = $this->getJSON();
		echo $string;
		if($and_exit)
		{
			exit();
		}
		else
		{
			return $string;
		}
	}
}

/**
 * 是否是线上服务器
 * @return boolean
 */
function isLiveServer()
{
    if(BASEURL == 'http://tasklist.zgldh.com/')
    {
        return true;
    }
    return false;
}

/* End of file Controller.php */
/* Location: ./application/core/MY_Controller.php */