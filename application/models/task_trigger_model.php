<?php
class Task_trigger_model extends MY_Model
{
    const TABLE = 'task_trigger';
    public function getByPK($task_trigger_id)
    {
        if ($this->cache_pk->hasData ( $task_trigger_id ))
        {
            return $this->cache_pk->getData ( $task_trigger_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (TaskTriggerPeer::PK => $task_trigger_id ) )->row_array ();
        $task_trigger = $raw ? $this->makeTriggerPeer ( $raw->type, $raw ) : null;
        
        $this->cache_pk->setData ( $task_trigger );
        
        return $task_trigger;
    }
    
    /**
     *
     *
     * 得到某任务的所有条件
     * 
     * @param int $task_id            
     * @param DB_Limit $limitObj
     *            = null 查询limit
     * @param string $type
     *            = null 条件的类型
     * @return multitype:Ambigous <Ambigous, NULL, DateStaticTaskTriggerPeer>
     */
    public function getByTaskId($task_id, $limitObj = null, $type = null)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        if ($type)
        {
            $this->db->where ( 'type', $type );
        }
        $this->db->where ( 'task_id', $task_id );
        $rows = $this->db->get ( self::TABLE )->result ();
        foreach ( $rows as $row )
        {
            $task_trigger = $this->makeTriggerPeer ( $row );
            $re [] = $task_trigger;
            $this->cache_pk->setData ( $task_trigger );
        }
        return $re;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param TaskTriggerPeer $user            
     */
    public function save(& $user)
    {
        parent::base_save ( self::TABLE, $user );
    }
    /**
     * 删除一个 TaskTriggerPeer
     * 
     * @param TaskTriggerPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /**
     *
     * @param AppTriggerPeer $app_trigger            
     */
    public function generateByAppTrigger($app_trigger)
    {
        $trigger = new TaskTriggerPeer ();
        $trigger->app_trigger_id = $app_trigger->app_trigger_id;
        $trigger->parameters = $app_trigger->serializeParameters ();
        return $trigger;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'task_trigger_id',
                'task_id',
                'app_trigger_id',
                'parameters',
                'update_timestamp'
                );
    }
    
    /**
     * 
     * @param Array|Object $raw
     * @return TaskTriggerPeer
     */
    public function makeTriggerPeer($raw)
    {
        $peer = new TaskTriggerPeer($raw);
        return $peer;
    }
}

/**
 *
 * @property int $task_trigger_id = 0
 * @property int $task_id = 0 条件所属的任务
 * @property int $app_trigger_id = 0 本触发条件所属应用的触发条件的id
 * @property string $parameters = '' 本条件的参数, json格式
 * @property string $update_timestamp = '' 更新时间戳
 * @author zgldh
 *        
 */
class TaskTriggerPeer extends BasePeer
{
    const PK = 'task_trigger_id';
    function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrimaryKeyName()
    {
        return self::PK;
    }
    public function getPrimaryKeyValue()
    {
        return $this->task_trigger_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp ();
        TaskTriggerPeer::model ()->save ( $this );
    }
    public function delete()
    {
        return TaskTriggerPeer::model ()->delete ( $this );
    }
    /**
     *
     * @return Task_trigger_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->task_trigger_model;
    }
    /**
     * 得到json_decode后的parameters
     * 
     * @return mixed
     */
    public function getParameters()
    {
        return json_decode ( $this->parameters );
    }
    /**
     * 设置parameters
     * 
     * @param mixed $data
     *            本参数会被 json_encode
     * @return null string 出错返回错误信息
     */
    public function setParameters($data)
    {
        $this->parameters = json_encode ( $data );
        return null;
    }
    /**
     *
     *
     * 得到本条件所属的TaskPeer
     * 
     * @return TaskPeer
     */
    public function getTask()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_model', 'task_model', true );
        $task = TaskPeer::model ()->getByPK ( $this->task_id );
        return $task;
    }
    
    /**
     * 
     * @return AppTriggerPeer
     */
    public function getAppTrigger()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_trigger_model', 'app_trigger_model', true );
        $app_trigger = AppTriggerPeer::model()->getByPK($this->app_trigger_id);
        return $app_trigger;
    }
    
    /**
     * 生成并且储存对应的一系列Process<br />
     * 这些Process有可能在将来被遍历、判断
     */
    public function generateAndSaveProcesses($timing_process = null)
    {
        $app_trigger = $this->getAppTrigger();
        $app_trigger->praseParameters($this->getParameters());
        $timing_process = $app_trigger->generateTimingProcess();
        $timing_process->task_id = $this->task_id;
        $timing_process->save();
        return $timing_process;
    }
    /**
     * 更新一个timing_process 的执行时间
     * @param TimingProcessPeer $timing_process
     */
    public function setNextTimingProcess($timing_process)
    {
        $app_trigger = $this->getAppTrigger();
        $app_trigger->praseParameters($this->getParameters());
        $app_trigger->setNextTimingProcess($timing_process);
    }
    
    /**
     * TODO 当前条件是否满足
     */
    public function check()
    {
    }
}

?>