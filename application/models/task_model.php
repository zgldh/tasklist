<?php
class Task_model extends MY_Model
{
    const TABLE = 'task';
    
    /**
     *
     * @param int $task_id            
     * @return TaskPeer
     */
    public function getByPK($task_id)
    {
        if ($this->cache_pk->hasData ( $task_id ))
        {
            return $this->cache_pk->getData ( $task_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (TaskPeer::PK => $task_id ) )->row_array ();
        $task = $raw ? new TaskPeer ( $raw ) : null;
        
        $this->cache_pk->setData ( $task_id, $task );
        
        return $task;
    }
    /**
     * 得到某用户的所有任务
     * 
     * @param int $user_id            
     * @param DB_Limit $limitObj
     *            = null 查询limit
     * @param boolean $no_pending
     *            = true 不要处于pending的任务
     * @return multitype:TaskPeer
     */
    public function getByUserId($user_id, $limitObj = null, $no_pending = true)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        if ($no_pending)
        {
            $this->db->where ( 'status != ', TaskPeer::STATUS_PENDING );
        }
        
        $rows = $this->db->get_where ( self::TABLE, array ('user_id' => $user_id ) )->result ();
        foreach ( $rows as $row )
        {
            $peer = new TaskPeer ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
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
        return parent::base_save ( self::TABLE, $peer );
    }
    /**
     * 删除一个TaskPeer
     * 
     * @param TaskPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    /**
     * 删除所有处于pending状态的TaskPeer
     * 
     * @param ini $user_id
     *            = null
     */
    public function deletePending($user_id = null)
    {
        $tasks = $this->getAllPending ( $user_id );
        foreach ( $tasks as $task )
        {
            $task instanceof TaskPeer;
            $task->delete ();
        }
    }
    
    /**
     * 保存表单(/task/editor.php)数据。 新建一个task。 出错返回错误数组
     * 
     * @param UserPeer $creater_user            
     * @param array $form_data            
     * @param TaskPeer $task
     *            = null 将返回生成的TaskPeer对象
     */
    public function saveForm($creater_user, $form_data, & $task = null)
    {
       return false;
    }
    
    /**
     * 得到最后一个处于pending状态的TaskPeer
     * 
     * @param int $user_id
     *            = null
     */
    public function getLastPending($user_id = null)
    {
        if ($user_id)
        {
            $this->db->where ( 'user_id', $user_id );
        }
        $this->db->where ( 'status', TaskPeer::STATUS_PENDING );
        $this->db->limit ( 1 );
        $this->db->order_by ( 'task_id', 'DESC' );
        $raw = $this->db->get ( self::TABLE )->row_array ();
        $task = $raw ? new TaskPeer ( $raw ) : false;
        
        return $task;
    }
    /**
     * 得到全部的pending状态的TaskPeer
     * 
     * @param int $user_id
     *            = null
     * @return multitype:TaskPeer
     */
    public function getAllPending($user_id = null)
    {
        $re = array ();
        if ($user_id)
        {
            $this->db->where ( 'user_id', $user_id );
        }
        $this->db->where ( 'status', TaskPeer::STATUS_PENDING );
        $rows = $this->db->get ( self::TABLE )->result ();
        foreach ( $rows as $row )
        {
            $re [] = new TaskPeer ( $row );
        }
        return $re;
    }
    
    /**
     * 创建一个新的TaskPeer或者得到当前用户最后一个处于Pending状态的TaskPeer
     * 
     * @param UserPeer $user            
     */
    public function createOrGetPending($user)
    {
        $task = $this->getLastPending ( $user->user_id );
        if (! $task)
        {
            $task = new TaskPeer ();
            $task->user_id = $user->user_id;
            $task->save ();
        }
        return $task;
    }
    
    /**
     * TODO createByAppTriggerAndCommand
     * 
     * @param UserPeer $user            
     * @param string $task_name            
     * @param AppTriggerPeer $trigger            
     * @param AppCommandPeer $command            
     * @return TaskPeer
     */
    public function createByAppTriggerAndCommand($user, $task_name, $trigger, $command)
    {
        $task = new TaskPeer ();
        $task->status = TaskPeer::STATUS_PENDING;
        $task->name = $task_name;
        $task->user_id = $user->user_id;
        $task->create_date = $this->getTimeStamp ();
        $task->limit = 0;
        $task->times = 0;
        $task->save ();
        
        $task_trigger = $trigger->generateTaskTrigger ();
        $task_trigger->task_id = $task->task_id;
        $task_trigger->save ();
        
        $task_command = $command->generateTaskCommand ();
        $task_command->task_id = $task->task_id;
        $task_command->save ();
        
        return $task;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'task_id',
                'user_id',
                'status',
                'name',
                'limit',
                'times',
                'create_date'
                );
    }
}
/**
 * @property int $task_id = 0 任务id
 * @property int $user_id = 0 创建者id
 * @property string $status = 'pending' 任务状态 : TaskPeer::STATUS_xxx pending, active, pause, prevent
 * @property string $name = '' 任务名字
 * @property int $limit = 0 计划总共执行多少次, 0为无限次
 * @property int $times = 0 已经执行了多少次
 * @property string $create_date = '' 任务创建时间
 * @property string $alter_date = '' 任务修改时间
 * @author zgldh
 *
 */
class TaskPeer extends BasePeer
{
    const PK = 'task_id';
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSE = 'pause';
    const STATUS_PREVENT = 'prevent';
    
    function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
        if (! $this->create_date)
        {
            $this->create_date = $this->getTimeStamp ();
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
        return self::model ()->save ( $this );
    }
    /**
     * 删除一个TaskPeer。 包括所有的 Triggers 和 Commands
     * 
     * @return boolean 成功true
     */
    public function delete()
    {
        $triggers = $this->getTriggers ();
        $commands = $this->getCommands ();
        
        foreach ( $triggers as $trigger )
        {
            $trigger instanceof TaskTriggerPeer;
            $trigger->delete ();
        }
        foreach ( $commands as $command )
        {
            $command instanceof TaskCommandPeer;
            $command->delete ();
        }
        
        self::model ()->delete ( $this );
		return true;
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
        if ($name == null && $this->task_id)
        {
            $this->name = 'Task ' . $this->task_id;
        }
        else
        {
            $this->name = $name;
        }
    }
    public function setLimit($limit = -1)
    {
        $limit = ( int ) $limit;
        if ($limit >= - 1)
        {
            $this->limit = $limit;
        }
    }
    /**
     * 根据表单数据，设置本任务的条件
     * 返回可能的错误条件信息,或null
     * 
     * @param UserPeer $creater_user            
     * @param array $data            
     */
    public function setConditionsFromForm($data)
    {
        $error = array ();
        $old_conditions = $this->getTriggers ();
        if (is_array ( $data ))
        {
            // 首先将老条件更新
            foreach ( $old_conditions as $old_condition )
            {
                $old_condition instanceof TaskTriggerPeer;
                if (array_key_exists ( $old_condition->type, $data ))
                {
                    // 这个老条件，会被新配置修改
                    $condition_data = $data [$old_condition->type];
                    unset ( $condition_data ['condition_id'] );
                    $temp_error = $old_condition->setParameters ( $condition_data );
                    
                    if ($temp_error)
                    {
                        $error [$old_condition->type] = $temp_error;
                    }
                    $old_condition->save ();
                    unset ( $data [$old_condition->type] );
                }
                else
                {
                    // 新配置中不存在这个老条件，就删除掉这个老条件。 T_T
                    $old_condition->delete ();
                }
            }
            
            // 剩下的配置数据，就是新增的了.
            // TODO 未来可能添加条件数量限制
            foreach ( $data as $key => $condition_data )
            {
                unset ( $condition_data ['condition_id'] );
                $condition = TaskTriggerPeer::model ()->createNew ( $key, $condition_data, $this->task_id );
                if ($condition)
                {
                    $condition->save ();
                }
                else
                {
                    $error [$key] = TaskTriggerPeer::model ()->getLastError ();
                }
            }
        }
        if (! count ( $error ))
        {
            $error = null;
        }
        return $error;
    }
    
    /**
     * 将本任务置为'活动' status = active
     * 
     * @param boolean $create_processes
     *            = false 是否顺带创建未来的Process
     */
    public function setActive($create_processes = false)
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save ();
        
        if ($create_processes)
        {
            $this->updateProcess ();
        }
    }
    /**
     * 将本任务置为'暂停' status = pause
     * 
     * @param boolean $remove_processes
     *            = false 是否顺带删除尚未执行的Process
     * @return 成功设置为暂停则返回true,否则返回false
     */
    public function setPause($remove_processes = false)
    {
        $this->status = self::STATUS_PAUSE;
        $this->save ();
        
        if ($remove_processes)
        {
            $timing_processes = $this->getTimingProcesses ( false );
            foreach ( $timing_processes as $process )
            {
                $process instanceof TimingProcessPeer;
                $process->delete ();
            }
        }
    }
    
    /**
     * 得到task的名字
     * 
     * @param boolean $htmlspecialchars
     *            = true 是否要经过htmlspecialchars函数的处理
     * @return string
     */
    public function getName($htmlspecialchars = true)
    {
        $re = $this->name;
        if ($htmlspecialchars)
        {
            $re = htmlspecialchars ( $re );
        }
        return $re;
    }
    
    /**
     * 得到本任务的所有命令
     * 
     * @return multitype:CommandPeer
     */
    public function getCommands()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_command_model', 'task_command_model', true );
        $commands = TaskCommandPeer::model ()->getByTaskId ( $this->task_id );
        return $commands;
    }
    
    /**
     * 得到特定类型的一个CommandPeer
     * 
     * @param string $type
     *            CommandPeer::TYPE_xxx
     * @return TaskCommandPeer
     */
    public function getCommand($type)
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_command_model', 'task_command_model', true );
        
        $command = null;
        if (TaskCommandPeer::model ()->isValidType ( $type ))
        {
            $limit = new DB_Limit ( 1 );
            $commands = TaskCommandPeer::model ()->getByTaskId ( $this->task_id, $limit, $type );
            $command = array_pop ( $commands );
        }
        return $command;
    }
    /**
     *
     *
     * 得到本任务的所有条件
     * 
     * @return multitype:< NULL, DateStaticTaskTriggerPeer >
     */
    public function getTriggers()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_trigger_model', 'task_trigger_model', true );
        $triggers = TaskTriggerPeer::model ()->getByTaskId ( $this->task_id );
        return $triggers;
    }
    
    /**
     * 得到特定类型的一个TaskTriggerPeer
     * 
     * @param string $type=null
     *            TaskTriggerPeer::TYPE_xxx 为null则不做限制
     * @return TaskTriggerPeer
     */
    public function getTrigger($type = null)
    {
        if ($type)
        {
            $CI = & get_instance ();
            $CI->load->model ( 'Task_trigger_model', 'task_trigger_model', true );
            
            $trigger = null;
            if (TaskTriggerPeer::model ()->isValidType ( $type ))
            {
                $limit = new DB_Limit ( 1 );
                $triggers = TaskTriggerPeer::model ()->getByTaskId ( $this->task_id, $limit, $type );
                $trigger = array_pop ( $triggers );
            }
        }
        else
        {
            $triggers = $this->getTriggers ();
            $trigger = array_pop ( $triggers );
        }
        return $trigger;
    }
    /**
     *
     *
     * 得到当前task的创建者
     * 
     * @return UserPeer
     */
    public function getUser()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'User_model', 'user_model', true );
        
        $user = UserPeer::model ()->getByPK ( $this->user_id );
        return $user;
    }
    /**
     * 得到所有属于本任务的 TimingProcess
     * 
     * @param boolean $executed
     *            = null true只取出执行过的, false只取出没执行过的, null忽略
     */
    public function getTimingProcesses($executed = null)
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $processes = TimingProcessPeer::model ()->getByTaskId ( $this->task_id, null, $executed );
        return $processes;
    }
    /**
     *
     *
     * 得到属于本任务的 唯一一个TimingProcess
     * 
     * @return TimingProcessPeer
     */
    public function getTimingProcess()
    {
        $peers = $this->getTimingProcesses ();
        $peer = array_pop ( $peers );
        return $peer;
    }
    
    /**
     * 根据本任务现有条件，更新 timing_process
     */
    public function updateProcess()
    {
        $timing_process = $this->getTimingProcess();
        if($timing_process)
        {
            $timing_process->next();
        }
        else
        {
            $timing_process = $this->generateTimingProcess();
        }
    }
    public function generateTimingProcess()
    {
        $trigger = $this->getTrigger();
        $timing_process = $trigger->generateAndSaveProcesses();
        return $timing_process;
    }
    /**
     * 是否是激活状态
     * 
     * @return boolean
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }
    /**
     * 是否是暂停状态
     * 
     * @return boolean
     */
    public function isPause()
    {
        return $this->status == self::STATUS_PAUSE;
    }
    /**
     * 是否是准备状态
     * 
     * @return boolean
     */
    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }
    /**
     * 是否是被屏蔽了
     * 
     * @return boolean
     */
    public function isPrevent()
    {
        return $this->status == self::STATUS_PREVENT;
    }
    
    /**
     * 本任务是不是已经达到了执行次数上限。
     * 
     * @return boolean
     */
    public function isOverExecuted()
    {
        if ($this->limit > 0 && $this->times >= $this->limit)
        {
            return true;
        }
        return false;
    }
    
    /**
     * 判断当前任务是否满足触发条件
     */
    public function triggersCheck()
    {
        $triggers = $this->getTriggers ();
        $re = true;
        foreach ( $triggers as $trigger )
        {
            $trigger instanceof TaskTriggerPeer;
            $result = $trigger->check ();
            if ($result == false)
            {
                $re = false;
                break;
            }
        }
        return $re;
    }
    
    /**
     * 执行当前命令
     */
    public function runCommands()
    {
        $commands = $this->getCommands ();
        foreach ( $commands as $command )
        {
            $command instanceof TaskCommandPeer;
            $command->execute ();
        }
        
        $this->times ++;
        return $this->save ();
    }
    public function setupNextTimingProcess($timing_process = null)
    {
        if ($timing_process === null)
        {
            $timing_process = $this->getTimingProcess ();
        }
        
        $trigger = $this->getTrigger ();
        $trigger->generateAndSaveProcesses ( $timing_process );
    }
}

?>