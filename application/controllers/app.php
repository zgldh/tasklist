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
	
	public function ajax_app_triggers($app_id)
	{
		$this->needLoginOrExit();
		$response = new Response_JSON();
		
		$app_id = (int)$app_id;
		$this->loadAppTriggerModel();
		$triggers = $this->app_trigger_model->getByAppId($app_id);	//能得到全部trigger
// 		$triggers = $this->app_trigger_model->getByAppId($app_id,0);//只能得到normal 的 trigger
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