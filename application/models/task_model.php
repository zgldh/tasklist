<?php
class Task_model extends MY_Model
{
	const TABLE = 'task';

	public function getByPK($task_id)
	{
		if($this->cache_pk->hasData($task_id))
		{
			return $this->cache_pk->getData($task_id);
		}

		$raw = $this->db->get_where ( self::TABLE, array (TaskPeer::PK => $task_id ) )->row_array ();
		$task = $raw ? new TaskPeer( $raw ) : false;

		$this->cache_pk->setData($task_id, $task);

		return $task;
	}
	/**
	 * 得到某用户的所有任务
	 * @param int $user_id
	 * @param DB_Limit $limitObj = null 查询limit
	 * @param boolean $no_pending = true 不要处于pending的任务
	 * @return multitype:TaskPeer
	 */
	public function getByUserId($user_id, $limitObj = null,$no_pending = true)
	{
	    $re = array();
	    if($limitObj)
	    {
	        $limitObj->setLimit($this->db);
	    }
	    if($no_pending)
	    {
	    	$this->db->where("status != 'pending'");
	    }
	    
	    $rows = $this->db->get_where ( self::TABLE, array ('user_id'=> $user_id) )->result();
	    foreach($rows as $row)
	    {
	        $re[] = new TaskPeer( $row);
	    }
	    return $re;	    
	}
	/**
	 * 更新数据 或 插入数据
	 *
	 * @param TaskPeer $peer
	 */
	public function save(& $peer)
	{
		return parent::base_save(self::TABLE, $peer);
	}
	/**
	 * 删除一个TaskPeer
	 * @param TaskPeer $peer
	 * @return boolean
	 */
	public function delete(& $peer)
	{
		return parent::base_delete(self::TABLE, $peer);
	}
	/**
	 * 删除所有处于pending状态的TaskPeer
	 * @param ini $user_id = null
	 */
	public function deletePending($user_id = null)
	{
		$tasks = $this->getAllPending($user_id);
		foreach($tasks as $task)
		{
			$task instanceof TaskPeer;
			$task->delete();
		}
	}
	
	/**
	 * 保存表单(/task/editor.php)数据。 新建一个task。 出错返回错误数组
	 * @param UserPeer $creater_user
	 * @param array $form_data
	 * @param TaskPeer $task = null 将返回生成的TaskPeer对象
	 */
	public function saveForm($creater_user,$form_data, & $task = null)
	{
		$this->load->model('Condition_model','condition_model',true);
		$this->load->model('Command_model','command_model',true);
		$this->load->model('Timing_process_model','timing_process_model',true);

		$error = array();
	
		$peer = $this->getByPK($this->getData($form_data, 'task_id'));
		// 未来可能会加上管理员权限判断
		if(!$peer)
		{
		    $error['system']='您要修改的任务不存在。';
		    return $error;
		    
		}
	    if($peer->user_id != $creater_user->user_id)
	    {
	        $error['system']='您无权修改本任务。';
	        return $error;
	    }
	    
		$this->db->trans_start();
		
	    $peer->setName($this->getData($form_data, 'name'));
	    $peer->setLimit($this->getData($form_data, 'limit'));
	    $conditions_error = $peer->setConditionsFromForm($this->getData($form_data, 'Conditions'));
	    $commands_error = $peer->setCommandsFromForm($this->getData($form_data, 'Commands'));
	    $peer->setActive();
	    $generate_process_error = $peer->updateProcess();
	    $this->deletePending($creater_user->user_id);
	    
	    if($conditions_error)
	    {
	    	$error['Conditions'] = $conditions_error;
	    }
	    if($commands_error)
	    {
	    	$error['Commands'] = $commands_error;
	    }
	    if($generate_process_error)
	    {
	    	$error['process'] = $generate_process_error;
	    }

	    $this->db->trans_complete();

	    if ($this->db->trans_status() === FALSE)
	    {
	    	$error = array('system'=>'数据库内部错误。无法保存任务。');
	    	$task = null;
	    }
	    else
	    {
	    	$task = $peer;
	    }
	    
	    if(count($error) == 0)
	    {
	    	$error = null;
	    }
	    return $error;
	}
	
	/**
	 * 得到最后一个处于pending状态的TaskPeer
	 * @param int $user_id = null
	 */
	public function getLastPending($user_id = null)
	{
		if($user_id)
		{
			$this->db->where('user_id',$user_id);
		}
		$this->db->where('status','pending');
		$this->db->limit(1);
		$this->db->order_by('task_id','DESC');
		$raw = $this->db->get( self::TABLE)->row_array ();
		$task = $raw ? new TaskPeer( $raw ) : false;

		return $task;
	}
	/**
	 * 得到全部的pending状态的TaskPeer
	 * @param int $user_id = null
	 * @return multitype:TaskPeer
	 */
	public function getAllPending($user_id = null)
	{
		$re = array();
		if($user_id)
		{
			$this->db->where('user_id',$user_id);
		}
		$this->db->where('status','pending');
		$rows = $this->db->get ( self::TABLE )->result();
		foreach($rows as $row)
		{
			$re[] = new TaskPeer( $row);
		}
		return $re;
	}
	
	/**
	 * 创建一个新的TaskPeer或者得到当前用户最后一个处于Pending状态的TaskPeer
	 * @param UserPeer $user
	 */
	public function createOrGetPending($user)
	{
		$task = $this->getLastPending($user->user_id);
		if(!$task)
		{
			$task = new TaskPeer();
			$task->user_id = $user->user_id;
			$task->save();
		}
		return $task;
	}
}

class TaskPeer extends BasePeer
{
	const PK = 'task_id';

	/**
	 * 任务id
	 * @var int
	 */
	public $task_id = 0;
	/**
	 * 创建者id
	 * @var int
	 */
	public $user_id = 0;
    /**
     * 任务状态. pending, active, pause, prevent
     * @var string
     */
	public $status = 'pending';
	/**
	 * 任务名字
	 * @var string
	 */
	public $name = '';
	/**
	 * 计划总共执行多少次, 0为无限次
	 * @var int
	 */
	public $limit = 0;
	/**
	 * 已经执行了多少次
	 * @var int
	 */
	public $times = 0;
	/**
	 * 任务创建时间
	 * @var string
	 */
	public $create_date = '';
	/**
	 * 任务修改时间
	 * @var string
	 */
	public $alter_date = '';



	function __construct($raw = null)
	{
		parent::__construct ( $raw, __CLASS__ );
		if(!$this->create_date)
		{
		    $this->create_date = $this->getTimeStamp();
		}
	}
	public function getPrimaryKeyName()
	{
		return self::PK;
	}
	public function getPrimaryKeyValue()
	{
		return $this->task_id;
	}
	public function save()
	{
	    $this->alter_date = $this->create_date;
		TaskPeer::model()->save($this);
	}
	/**
	 * 删除一个TaskPeer。 包括所有的 Conditions 和 Commands
	 * @return boolean 成功true
	 */
	public function delete()
	{
		TaskPeer::model()->db->trans_start();
		
		$conditions = $this->getConditions();
		$commands = $this->getCommands();

		foreach($conditions as $condition)
		{
			$condition instanceof ConditionPeer;
			$condition->delete();
		}
		foreach($commands as $command)
		{
			$command instanceof CommandPeer;
			$command->delete();
		}
		
		TaskPeer::model()->delete($this);
		TaskPeer::model()->db->trans_complete();
		

		if (TaskPeer::model()->db->trans_status() === FALSE)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	/**
	 *
	 * @return Task_Model
	 */
	public static function model()
	{
		$CI = & get_instance ();
		return $CI->task_model;
	}
	
	public function setName($name = null)
	{
		if($name == null && $this->task_id)
		{
			$this->name = 'Task '.$this->task_id;
		}
		else
		{
			$this->name = $name;
		}
	}
	
	public function setLimit($limit = -1)
	{
		$limit = (int)$limit;
		if($limit >= -1)
		{
			$this->limit = $limit;
		}
	}
	/**
	 * 根据表单数据，设置本任务的条件
	 * 返回可能的错误条件信息,或null
	 * @param UserPeer $creater_user
	 * @param array $data
	 */
	public function setConditionsFromForm($data)
	{
		$error = array();
		$old_conditions = $this->getConditions();
		if(is_array($data))
		{
		    //首先将老条件更新
		    foreach($old_conditions as $old_condition)
		    {
		        $old_condition instanceof ConditionPeer;
		        if(array_key_exists($old_condition->type,$data))
		        {
		            //这个老条件，会被新配置修改
		            $condition_data = $data[$old_condition->type];
		            unset($condition_data['condition_id']);
		            $old_condition->setParameters($condition_data);
		            $old_condition->save();
		            unset($data[$old_condition->type]);
		        }
		        else
		        {
		            //新配置中不存在这个老条件，就删除掉这个老条件。 T_T
		            $old_condition->delete();
		        }
		    }
		    
            //剩下的配置数据，就是新增的了.
            //TODO 未来可能添加条件数量限制
			foreach($data as $key=>$condition_data)
			{
			    unset($condition_data['condition_id']);
	    		$condition = ConditionPeer::model()->createNew($key,$condition_data,$this->task_id);
				if($condition)
				{
					$condition->save();
				}
				else
				{
					$error[$key] = ConditionPeer::model()->getLastError();
				}
			}
		}
		if(!count($error))
		{
			$error = null;
		}
		return $error;
	}
	/**
	 * 根据表单数据，设置本任务的命令
	 * 返回可能的错误条件信息,或null
	 * @param UserPeer $creater_user
	 * @param array $data
	 */
	public function setCommandsFromForm($data)
	{
	    $error = array();
	    $old_commands = $this->getCommands();
	    if(is_array($data))
	    {
	        //首先将老命令更新
	        foreach($old_commands as $old_command)
	        {
	            $old_command instanceof CommandPeer;
	            if(array_key_exists($old_command->type,$data))
	            {
	                //这个老命令，会被新配置修改
	                $command_data = $data[$old_command->type];
	                unset($command_data['command_id']);
	                $old_command->setParameters($command_data);
	                $old_command->save();
	                unset($data[$old_command->type]);
	            }
	            else
	            {
	                //新配置中不存在这个老命令，就删除掉这个老命令。 T_T
	                $old_command->delete();
	            }
	        }
	        
	        //剩下的配置数据，就是新增的了.
	        //TODO 未来可能添加命令数量限制
	        foreach($data as $key=>$command_data)
	        {
	            $command = CommandPeer::model()->createNew($key,$command_data,$this->task_id);
	            if($command)
	            {
	                $command->save();
	            }
	            else
	            {
	                $error[$key] = CommandPeer::model()->getLastError();
	            }
	        }
	    }
	    if(!count($error))
	    {
	        $error = null;
	    }
	    return $error;
	}

	/**
	 * 将本任务置为'活动'  status = active
	 */
	public function setActive()
	{
		$this->status = 'active';
		$this->save();
		
		return null;
	}
	/**
	 * 将本任务置为'暂停'  status = pause
	 */
	public function setPause()
	{
		$this->status = 'pause';
		$this->save();
		
		return null;
	}
	
	/**
	 * 得到task的名字
	 * @param boolean $htmlspecialchars = true 是否要经过htmlspecialchars函数的处理
	 * @return string
	 */
	public function getName($htmlspecialchars = true)
	{
		$re = $this->name;
		if($htmlspecialchars)
		{
			$re = htmlspecialchars($re);
		}
		return $re;
	}
	
	/**
	 * 得到本任务的所有命令
	 * @return multitype:CommandPeer
	 */
	public function getCommands()
	{
		$CI = & get_instance ();
		$CI->load->model('Command_model','command_model',true);
		$commands = CommandPeer::model()->getByTaskId($this->task_id);
		return $commands;
	}
	
	/**
	 * 得到特定类型的一个CommandPeer
	 * @param string $type CommandPeer::TYPE_xxx
	 * @return CommandPeer
	 */
	public function getCommand($type)
	{
		$CI = & get_instance ();
		$CI->load->model('Command_model','command_model',true);
		
		$command = null;
		if(CommandPeer::model()->isValidType($type))
		{
			$limit = new DB_Limit(1);
			$commands = CommandPeer::model()->getByTaskId($this->task_id,$limit,$type);
			$command = array_pop($commands);
		}
		return $command;
	}
	/**
	 * 
	 * 得到本任务的所有条件
	 * @return multitype:< NULL, DateStaticConditionPeer >
	 */
	public function getConditions()
	{
		$CI = & get_instance ();
		$CI->load->model('Condition_model','condition_model',true);
		$conditions = ConditionPeer::model()->getByTaskId($this->task_id);
		return $conditions;
	}
	
	/**
	 * 得到特定类型的一个ConditionPeer
	 * @param string $type ConditionPeer::TYPE_xxx
	 * @return ConditionPeer
	 */
	public function getCondition($type)
	{
		$CI = & get_instance ();
		$CI->load->model('Condition_model','condition_model',true);
		
		$condition = null;
		if(ConditionPeer::model()->isValidType($type))
		{
			$limit = new DB_Limit(1);
			$conditions = ConditionPeer::model()->getByTaskId($this->task_id,$limit,$type);
			$condition = array_pop($conditions);
		}
		return $condition;
	}
	/**
	 * 得到所有属于本任务的 TimingProcess 
	 * @param boolean $executed = null true只取出执行过的, false只取出没执行过的, null忽略
	 */
	public function getTimingProcesses($executed = null)
	{
	    $CI = & get_instance ();
	    $CI->load->model('Timing_process_model','timing_process_model',true);

	    $processes = TimingProcessPeer::model()->getByTaskId($this->task_id,null,$executed);
	    return $processes;
	}
	
	/**
	 * 根据本任务现有条件，更新所有可能的Process<br />
	 * 比如更新 timing_process等<br />
	 * 尚未运行的旧的Process都会被清除
	 * @return array|null 没有错误则返回null,有错误则返回错误数组
	 */
	public function updateProcess()
	{
	    $errors = array();
	    //未执行过的TimingProcess
	    $unexecuted_processes = $this->getTimingProcesses(false);
	    //TODO 未来可能删除更多其他种类的Process
	    
	    foreach($unexecuted_processes as $process)
	    {
	        $process instanceof TimingProcessPeer;
	        $process->delete();
	    }
	    

	    $CI = & get_instance ();
	    $CI->load->model('Condition_model','condition_model',true);
	    
	    //生成一系列新的TimingProcess
	    $conditions = $this->getConditions();
	    foreach($conditions as $condition)
	    {
	    	$error = $condition->generateAndSaveProcesses();
	    	if($error)
	    	{
	    		$errors[] = $error;
	    	}
	    }
	    //TODO 未来可能生成更多其他种类的Process
	    
	    
	    if(!count($errors))
	    {
	    	return null;
	    }
	    return $errors;
	}
}

?>