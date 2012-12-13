<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Process extends MY_Controller
{
	public function index()
	{
		header('Location: /process/list');
		exit();
	}
	
	/**
	 * process / mylist
	 */
	public function mylist()
	{
	    $this->needLoginOrExit('/process/list');

        $this->loadTimingProcessModel();
        $processes = $this->timing_process_model->getByUserId($this->webuser->getUserId());

	    $this->setTitle("任务执行历史和计划管理--自动任务");
	    $this->navbar->setHeaderTitle('任务执行历史和计划管理');
	    $this->javascript_css_manager->addStyleFile('/css/tl-process-mylist.css');
	    $this->javascript_css_manager->addJavascriptFile('/js/tl-process-mylist.js');

	    $data = array('processes'=>$processes);

		$this->view('/process/mylist', $data);
	}
	
	/**
	 * 跳过一个Process
	 * @param int $process_id
	 */
	public function skip($process_id)
	{
		$this->needLoginOrExit();
		 
		$response = new Response_JSON();
		$this->loadTimingProcessModel();
		$timing_process = $this->timing_process_model->getByPK($process_id);
		if(!$timing_process)
		{
			$response->setErrors('您不能跳过不存在的计划。');
			$response->output();
		}
		if(!$timing_process->isEditable($this->webuser->getUser()) )
		{
			$response->setErrors('您不能跳过不属于您的计划。');
			$response->output();
		}
		if($timing_process->setSkip())
		{
			$response->setSuccess();
		}
		else
		{
			$response->setErrors('数据库错误，跳过计划失败。');
		}
		$response->output();
	}
	/**
	 * 恢复一个Process
	 * @param int $process_id
	 */
	public function restore($process_id)
	{
		$this->needLoginOrExit();
		 
		$response = new Response_JSON();
		$this->loadTimingProcessModel();
		$timing_process = $this->timing_process_model->getByPK($process_id);
		if(!$timing_process)
		{
			$response->setErrors('您不能恢复不存在的计划。');
			$response->output();
		}
		if(!$timing_process->isEditable($this->webuser->getUser()) )
		{
			$response->setErrors('您不能恢复不属于您的计划。');
			$response->output();
		}
		if($timing_process->setRestore())
		{
			$response->setSuccess();
		}
		else
		{
			$response->setErrors('数据库错误，恢复计划失败。');
		}
		$response->output();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */