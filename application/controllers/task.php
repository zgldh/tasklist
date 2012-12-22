<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends MY_Controller
{
	public function index()
	{
		header('Location: /task/list');
		exit();
	}

	public function edit($task_id)
	{
	    $this->needLoginOrExit('/task/edit/'.$task_id);

	    $this->loadTaskModel();
	    $this->loadConditionModel();
	    $this->loadCommandModel();
	    $task = $this->task_model->getByPK($task_id);

	    $this->setTitle("[编辑]".$task->getName()."--自动任务");
	    $this->navbar->setHeaderTitle("[编辑]".$task->getName());
	    $this->javascript_css_manager->addStyleFile('/css/tl-editor.css');
	    $this->javascript_css_manager->addJavascriptFile('/js/tl-editor.js');
	    
	    $ref = $this->inputGet('ref');
	    $this->navbar->setBackBtn($ref);
	    
	    $data = array('task'=>$task);

		$this->view('/task/editor', $data);
	}
	public function create()
	{
	    $this->needLoginOrExit('/task/create');

	    $this->loadTaskModel();
	    $this->loadConditionModel();
	    $this->loadCommandModel();
	    if($this->isPostRequest())
	    {
	        $form_data = $this->inputPost('Task');
	        $task = null;
	        $error = $this->task_model->saveForm($this->webuser->getUser(),$form_data,$task);
	        $re = new Response_JSON();
	        if($error)
	        {
	        	$re->setErrors($error);
	        	$re->output();
	        }
	        $re->setSuccess();
	        $re->output();
	    }

	    $task = $this->task_model->createOrGetPending($this->webuser->getUser());

	    $this->setTitle("新建任务--自动任务");
	    $this->navbar->setHeaderTitle('新建任务');
	    $this->javascript_css_manager->addStyleFile('/css/tl-editor.css');
	    $this->javascript_css_manager->addJavascriptFile('/js/tl-editor.js');


	    $data = array('task'=>$task);

		$this->view('/task/editor', $data);
	}
	/**
	 * 将一个任务暂停
	 * @param int $task_id
	 */
	public function pause($task_id)
	{
	    $this->needLoginOrExit();
	    
	    $response = new Response_JSON();
	    $this->loadTaskModel();
	    $task = $this->task_model->getByPK($task_id);
	    if(!$task)
	    {
	        $response->setErrors('您不能暂停不存在的任务。');
	        $response->output();
	    }
	    if($task->user_id != $this->webuser->getUserId())
	    {
	        $response->setErrors('您不能暂停不属于您的任务。');
	        $response->output();
	    }
	    if($task->setPause(true))
	    {
	        $response->setSuccess();
	    }
	    else
	    {
	        $response->setErrors('数据库错误，暂停失败。');
	    }
	    $response->output();
	}
	/**
	 * 将一个任务激活
	 * @param int $task_id
	 */
	public function active($task_id)
	{
	    $this->needLoginOrExit();
	    
	    $response = new Response_JSON();
	    $this->loadTaskModel();
	    $task = $this->task_model->getByPK($task_id);
	    if(!$task)
	    {
	        $response->setErrors('您不能激活不存在的任务。');
	        $response->output();
	    }
	    if($task->user_id != $this->webuser->getUserId())
	    {
	        $response->setErrors('您不能激活不属于您的任务。');
	        $response->output();
	    }
	    if($task->setActive(true))
	    {
	        $response->setSuccess();
	    }
	    else
	    {
	        $response->setErrors('数据库错误，激活失败。');
	    }
	    $response->output();
	}

	public function delete($task_id)
	{
		$this->needLoginOrExit();

		$response = new Response_JSON();
		$this->loadTaskModel();
		$task = $this->task_model->getByPK($task_id);
		if(!$task)
		{
			$response->setErrors('您不能删除不存在的任务。');
			$response->output();
		}
		if($task->user_id != $this->webuser->getUserId())
		{
			$response->setErrors('您不能删除不属于您的任务。');
			$response->output();
		}
		if($task->delete())
		{
			$response->setSuccess();
		}
		else
		{
			$response->setErrors('数据库错误，删除失败。');
		}
		$response->output();
	}
	public function mylist()
	{
	    $this->needLoginOrExit('/task/list');

        $this->loadTaskModel();
        $tasks = $this->task_model->getByUserId($this->webuser->getUserId());

	    $this->setTitle("我的任务--自动任务");
	    $this->navbar->setHeaderTitle('我的任务');
	    $this->javascript_css_manager->addStyleFile('/css/tl-mylist.css');
	    $this->javascript_css_manager->addJavascriptFile('/js/tl-mylist.js');

	    $data = array('tasks'=>$tasks);

		$this->view('/task/mylist', $data);
	}
	
	public function check($task_id)
	{
		$this->loadTimingProcessModel();
		$processes = TimingProcessPeer::model()->getByTaskId($task_id);
		
		foreach($processes as $process)
		{
			$process instanceof TimingProcessPeer;
			if($process->check())
			{
				printf("process id: %d  check OK<br />",$process->process_id);
			}
			else
			{
				printf("process id: %d  check FAIL<br />",$process->process_id);
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */