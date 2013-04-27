<?php
require_once (APPPATH . 'libraries/trait/can_to_next.php');
/**
 * Timing_Progress_model 处理模型<br />
 * 其实是基于时间的消息钩子。<br />
 * 系统定时取出当前时刻需要执行的ProgressPeer, 然后(同时依据其他条件)执行
 * @author zgldh
 *
 */
class Timing_process_model extends MY_Model
{
    const TABLE = 'timing_process';
    public function getByPK($process_id)
    {
        if ($this->cache_pk->hasData ( $process_id ))
        {
            return $this->cache_pk->getData ( $process_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (TimingProcessPeer::PK => $process_id ) )->row_array ();
        $process = $raw ? new TimingProcessPeer ( $raw ) : false;
        
        $this->cache_pk->setData ( $process );
        
        return $process;
    }
    /**
     * 得到一个任务的TimingProcess列表
     *
     * @param int $task_id
     *            任务id
     * @param DB_Cache $limitObj
     *            = null
     * @param boolean $status
     *            = null true只取出执行过的, false只取出没执行过的, null忽略
     * @return multitype:TimingProcessPeer
     */
    public function getByTaskId($task_id, $limitObj = null, $status = null)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        
        if ($status !== null)
        {
            $this->db->where ( "status", ( int ) $status );
        }
        
        $rows = $this->db->get_where ( self::TABLE, array ('task_id' => $task_id ) )->result ();
        foreach ( $rows as $row )
        {
            $peer = new TimingProcessPeer ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    /**
     * 得到一个用户的TimingProcess列表
     *
     * @param int $user_id
     *            用户id
     * @param DB_Cache $limitObj
     *            = null
     * @param boolean $executed
     *            = null true只取出执行过的, false只取出没执行过的, null忽略
     * @return multitype:TimingProcessPeer
     */
    public function getByUserId($user_id, $limitObj = null, $executed = null)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        
        if ($executed === true)
        {
            $this->db->where ( "executed", 1 );
        }
        elseif ($executed === false)
        {
            $this->db->where ( 'executed', 0 );
        }
        
        $this->db->select ( self::TABLE . '.*' );
        $this->db->from ( self::TABLE );
        $this->db->join ( 'task', 'task.task_id = ' . self::TABLE . '.task_id' );
        $this->db->where ( 'task.user_id', $user_id );
        $this->db->order_by ( 'plan_time', 'DESC' );
        $rows = $this->db->get ()->result ();
        foreach ( $rows as $row )
        {
            $peer = new TimingProcessPeer ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    /**
     * 得到可以执行的 TimingProcessPeer
     * XXX 数量一多，可能导致有的timing process一直处于队尾无法被执行。
     *
     * @param string $last_datetime
     *            = null '2012-12-12 12:12:12', 为null则自动为当前时间
     * @param DB_Limit $limitObj
     *            = null
     * @return multitype:TimingProcessPeer
     */
    public function getRunnableBefore($last_datetime = null, $limitObj = null)
    {
        $re = array ();
        
        if ($last_datetime == null)
        {
            $last_datetime = $this->getTimeStamp ();
        }
        
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        $this->db->select ( self::TABLE . '.*' );
        $this->db->from ( self::TABLE );
        $this->db->where ( 'status > ', TimingProcessPeer::STATUS_IGNORE );
        $this->db->where ( 'plan_time <= ', $last_datetime );
        $this->db->order_by ( 'plan_time', 'ASC' );
        $rows = $this->db->get ()->result ();
        foreach ( $rows as $row )
        {
            $peer = new TimingProcessPeer ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    /**
     * 得到某一小时内的TimingProcessPeer<br />
     * skip = 0
     *
     * @param string $start_hour
     *            开始小时 '2012-12-12 12:00:00'
     * @param DB_Cache $limitObj
     *            = null
     * @param boolean $executed
     *            = null true只取出执行过的, false只取出没执行过的, null忽略
     * @return multitype:TimingProcessPeer
     */
    public function getInOneHour($start_hour, $limitObj = null, $executed = null)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        
        if ($executed === true)
        {
            $this->db->where ( "executed", 1 );
        }
        elseif ($executed === false)
        {
            $this->db->where ( 'executed', 0 );
        }
        
        $start_timestamp = strtotime ( $start_hour );
        $end_hour = date ( 'Y-m-d H:00:00', strtotime ( '+1 hour', $start_timestamp ) );
        
        $this->db->select ( self::TABLE . '.*' );
        $this->db->from ( self::TABLE );
        $this->db->where ( 'skip', 0 );
        $this->db->where ( 'plan_time >= ', $start_hour );
        $this->db->where ( 'plan_time < ', $end_hour );
        $this->db->order_by ( 'plan_time', 'DESC' );
        $rows = $this->db->get ()->result ();
        foreach ( $rows as $row )
        {
            $peer = new TimingProcessPeer ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param TimingProcessPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /**
     * 创造一个新的TimingProcessPeer
     *
     * @param int $task_id
     *            任务id
     * @param string $plan_time
     *            计划执行时间 '2012-12-12 12:12:12'
     * @return TimingProcessPeer
     */
    public function create($task_id, $plan_time)
    {
        $peer = new TimingProcessPeer ();
        $peer->task_id = $task_id;
        $peer->status = TimingProcessPeer::STATUS_IGNORE;
        $peer->update_time = self::getTimeStamp ();
        $peer->plan_time = $plan_time;
        return $peer;
    }
    /**
     * 某任务在某时刻是否已经存在TimingProcessPeer
     *
     * @param int $task_id            
     * @param string $plan_time
     *            '2012-12-12 12:12:12'
     * @return boolean
     */
    public function exist($task_id, $plan_time)
    {
        $this->db->where ( 'task_id', $task_id );
        $this->db->where ( 'plan_time', $plan_time );
        $num_rows = $this->db->get ( self::TABLE )->num_rows ();
        if ($num_rows == 0)
        {
            return false;
        }
        return true;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                    'process_id',
                    'task_id',
                    'status',
                    'update_time',
                    'plan_time'
                );
    }
}
/**
 * @property int $process_id = 0 处理id
 * @property int $task_id = 0 任务id
 * @property int $status = 0  执行状态:  0忽略， 1条件判断， 2命令执行
 * @property string $update_time = '' 更新时间
 * @property string $plan_time = '' 计划执行时间
 * @author zgldh
 *
 */
class TimingProcessPeer extends BasePeer
{
	use can_to_next;
	
    const PK = 'process_id';
    
    /**
     * @var int 忽略状态
     */
    const STATUS_IGNORE = 0;
    /**
     * @var int 条件判断状态
     */
    const STATUS_TRIGGER = 1;
    /**
     * @var int 命令执行状态
     */
    const STATUS_COMMAND = 2;
    
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
        return $this->process_id;
    }
    public function save()
    {
        return self::model ()->save ( $this );
    }
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return Timing_process_Model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->timing_process_model;
    }
    /**
     * 得到当前Process对应的TaskPeer
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
     * 得到当前Process的计划执行时间
     *
     * @return TaskPeer
     */
    public function getPlanTime()
    {
        $date = date_parse ( $this->plan_time );
        $str = "{$date['year']}年{$date['month']}月{$date['day']}日 {$date['hour']}点";
        return $str;
    }
    /**
     * 设置状态为忽略本条
     */
    public function setStatusIgnore()
    {
        $this->status = self::STATUS_IGNORE;
        return $this->save ();
    }
    /**
     * 设置状态为条件判断
     */
    public function setStatusCondition()
    {
        $this->status = self::STATUS_TRIGGER;
        return $this->save ();
    }
    /**
     * 设置状态为执行命令
     */
    public function setStatusCommand()
    {
        $this->status = self::STATUS_COMMAND;
        return $this->save ();
    }
    
    /**
     * 该用户是否可以编辑该Process
     *
     * @param UserPeer $author            
     */
    public function isEditable($author)
    {
        // TODO 将来可能加上管理员权限
        $task = $this->getTask ();
        if ($task->user_id == $author->user_id)
        {
            return true;
        }
        return false;
    }
    
    /**
     * 处于忽略状态
     *
     * @return boolean
     */
    public function isStatusIgnore()
    {
        return ($this->status == self::STATUS_IGNORE) ? true : false;
    }
    /**
     * 处于条件判断状态
     */
    public function isStatusTrigger()
    {
        return ($this->status == self::STATUS_TRIGGER) ? true : false;
    }
    /**
     * 处于执行命令状态
     */
    public function isStatusCommand()
    {
        return ($this->status == self::STATUS_COMMAND) ? true : false;
    }
    
    /**
     * 执行本process对应的task的命令
     */
    public function runCommand()
    {
        $task = $this->getTask ();
        $task->runCommands ();
        $this->setCanMoveToNext($task->canMoveToNext());
    }
    
    /**
     * 对本process对应的task进行条件判断
     */
    public function runTrigger()
    {
        $task = $this->getTask ();
        $result = $task->triggersCheck ();
        
        if ($result)
        {
            // 判断成功， 则设置为执行命令状态
            $this->setStatusCommand ();
        }
        else
        { // 判断失败， 则设置为忽略状态
            $this->setStatusIgnore ();
        }
    }
    /**
     * 更新这个timing process的参数。 不会自动保存
     * @param string|int $plan_time 可以是时间戳，或者时间字符串 '2012-12-12 12:12:12'
     * @param int $status TimingProcessPeer::STATUS_XX
     */
    public function updateParameters($plan_time, $status)
    {
        $this->status = $status;
        if(is_numeric($plan_time))
        {
            $plan_time = $this->timestampToDatetimeString($plan_time);
        }
        $this->plan_time = $plan_time;
        $this->update_time = self::getTimeStamp ();
    }
    
    /**
     * 设置执行时间到下一个时间点
     */
    public function next()
    {
        $task = $this->getTask();
        $trigger = $task->getTrigger();
        $trigger->setNextTimingProcess($this);
        $this->save();
    }
    
}

?>