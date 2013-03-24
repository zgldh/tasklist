<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends MY_Controller
{
	public function index()
	{
		header('Location: /');
		exit();
	}


	/**
	 * 刷新app的trigger统计和command统计
	 */
	public function flush_count()
	{
		$this->needLoginOrExit();

		$this->loadAppModel();
		$this->loadAppTriggerModel();
		$this->loadAppCommandModel();
		$apps = $this->app_model->getAll();
		foreach($apps as $app)
		{
		    $app instanceof AppPeer;
		    $app->triggers_count = $this->app_trigger_model->countByAppId($app->app_id);
		    $app->commands_count = $this->app_command_model->countByAppId($app->app_id);
		    $app->save();
		}
		
	}
	
	/**
	 * 
	 * @param int $type  null: all; triggers: has triggers; commands: has commands
	 */
	public function ajax_all($type = null)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$user_id = $this->getWebUser()->getUserId();
		$this->loadAppModel();
// 		$apps = $this->app_model->getAll($type);		//全部app
		$apps = $this->app_model->getAll($type,null,0);	//只有normal app
		$data = array();
		foreach($apps as $app)
		{
		    $app instanceof AppPeer;
		    $item = $app->getVars();
		    $item['active'] = $app->getActivedStatusByUser($user_id);
		    $data[] = $item;
		}
		
		$response->setData($data);
		$response->setSuccess();
		$response->output();
	}
	
	/**
	 * 点击一个 triger app 以后的处理。 1：可能返回trigger列表， 2： 可能返回"需要激活"页面
	 * @param int $app_id
	 */
	public function ajax_app_triggers($app_id)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$app_id = (int)$app_id;
		$this->loadAppModel();
		$app = $this->app_model->getByPK($app_id);
		
		$actived = $app->getActivedStatusByUser($this->webuser->getUserId());
		
		if($actived)
		{
		    //已经激活了。可以输出trigger列表
    		$triggers = $app->getTriggers();
    		
    		$data = array();
    		foreach($triggers as $trigger)
    		{
    		    $trigger instanceof AppTriggerPeer;
    		    $item = $trigger->getVars();
    		    $data[] = $item;
    		}
    		$response->setData($data);
    		$response->setSuccess();
    		$response->output();
		}
		else
		{
    		$data = array(
    		        'un_actived'=>true,
    		        'active_form'=>$app->getActiveForm()
    		        );
    		$response->setData($data);
    		$response->setSuccess();
    		$response->output();
		}
	}
	
	public function ajax_app_trigger_detail($app_trigger_id)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$app_trigger_id = (int)$app_trigger_id;
		$this->loadAppTriggerModel();
		$trigger = $this->app_trigger_model->getByPK($app_trigger_id);
		
		$data = $trigger->getVars();
		$data['detial_html'] = $trigger->getDetailHTML();
		
		$response->setData($data);
		$response->setSuccess();
		$response->output();
	}
	
	public function ajax_app_trigger_submit()
	{
		$this->needLoginOrExit();
		$trigger_parameters = $this->inputPost('trigger');
		$response = new Response_JSON();
		
		$app_trigger_id = (int)$trigger_parameters['id'];
		$this->loadAppTriggerModel();
		$trigger = $this->app_trigger_model->getByPK($app_trigger_id);
		$data = $trigger->getFullDescription($trigger_parameters);
		
		$response->setData($data);
		$response->setSuccess();
		$response->output();
	}
	
	public function ajax_app_commands($app_id)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$app_id = (int)$app_id;
		$this->loadAppCommandModel();
		$commands = $this->app_command_model->getByAppId($app_id);	//能得到全部trigger
// 		$triggers = $this->app_command_model->getByAppId($app_id,0);//只能得到normal 的 trigger
		$data = array();
		foreach($commands as $command)
		{
		    $command instanceof AppCommandPeer;
		    $item = $command->getVars();
		    $data[] = $item;
		}
		
		$response->setData($data);
		$response->setSuccess();
		$response->output();
	}
	public function ajax_app_command_detail($app_command_id)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$app_command_id = (int)$app_command_id;
		$this->loadAppCommandModel();
		$command = $this->app_command_model->getByPK($app_command_id);
		
		$data = $command->getVars();
		$data['detial_html'] = $command->getDetailHTML();

		$response->setData($data);
		$response->setSuccess();
		$response->output();
	}
	public function ajax_app_command_submit()
	{
	    $this->needLoginOrExit();
	    $command_parameters = $this->inputPost('command');
	    $response = new Response_JSON();
	    
	    $app_command_id = (int)$command_parameters['id'];
	    $this->loadAppCommandModel();
	    $command = $this->app_command_model->getByPK($app_command_id);
	    $data = $command->getFullDescription($command_parameters);
	    
	    $response->setData($data);
	    $response->setSuccess();
	    $response->output();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */