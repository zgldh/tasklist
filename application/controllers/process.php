<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Process extends MY_Controller
{
	public function index()
	{
		header('Location: /process/list');
		exit();
	}
	
	/**
	 * TODO NEXT process / mylist
	 */
	public function mylist()
	{
	    $this->needLoginOrExit('/process/list');

        $this->loadTimingProcessModel();
        $processes = $this->timing_process_model->getByUserId($this->webuser->getUserId());

	    $this->setTitle("任务执行历史和计划管理--自动任务");
	    $this->navbar->setHeaderTitle('任务执行历史和计划管理');
// 	    $this->javascript_css_manager->addStyleFile('/css/tl-mylist.css');
// 	    $this->javascript_css_manager->addJavascriptFile('/js/tl-mylist.js');

	    $data = array('processes'=>$processes);

		$this->view('/process/mylist', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */