<?php
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
        
        $this->cache_pk->setData ( $process_id, $process );
        
        return $process;
    }
    /**
     * 得到一个任务的TimingProcess列表
     * 
     * @param int $task_id
     *            任务id
     * @param DB_Cache $limitObj
     *            = null
     * @param boolean $executed
     *            = null true只取出执行过的, false只取出没执行过的, null忽略
     * @return multitype:TimingProcessPeer
     */
    public function getByTaskId($task_id, $limitObj = null, $executed = null)
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
        
        $rows = $this->db->get_where ( self::TABLE, array ('task_id' => $task_id ) )->result ();
        foreach ( $rows as $row )
        {
            $re [] = new TimingProcessPeer ( $row );
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
        $this->db->order_by('plan_time','DESC');
        $rows = $this->db->get ( )->result ();
        foreach ( $rows as $row )
        {
            $re [] = new TimingProcessPeer ( $row );
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
        $peer->skip = 0;
        $peer->executed = 0;
        $peer->gen_time = self::getTimeStamp ();
        $peer->exec_time = null;
        $peer->plan_time = $plan_time;
        return $peer;
    }
    /**
     * 某任务在某时刻是否已经存在TimingProcessPeer
     * @param int $task_id
     * @param string $plan_time '2012-12-12 12:12:12'
     * @return boolean
     */
    public function exist($task_id, $plan_time)
    {
        $this->db->where('task_id',$task_id);
        $this->db->where('plan_time',$plan_time);
        $num_rows = $this->db->get(self::TABLE)->num_rows();
        if($num_rows == 0)
        {
            return false;
        }
        return true;
    }
}
class TimingProcessPeer extends BasePeer
{
    const PK = 'process_id';
    
    /**
     * 处理id
     * 
     * @var int
     */
    public $process_id = 0;
    /**
     * 任务id
     * 
     * @var int
     */
    public $task_id = 0;
    /**
     * 0不跳过，1跳过
     * 
     * @var int
     */
    public $skip = 0;
    /**
     * 0没执行过,1执行过
     * 
     * @var int
     */
    public $executed = 0;
    /**
     * 生成时间
     * 
     * @var string
     */
    public $gen_time = '';
    /**
     * 实际执行时间
     * 
     * @var string
     */
    public $exec_time = '';
    /**
     * 计划执行时间
     * 
     * @var string
     */
    public $plan_time = '';
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
     * @return TaskPeer
     */
    public function getTask()
    {
        $CI = & get_instance ();
        $CI->load->model('Task_model','task_model',true);
        $task = TaskPeer::model()->getByPK($this->task_id);
        return $task;
    }
    /**
     * 得到当前Process的计划执行时间
     * @return TaskPeer
     */
    public function getPlanTime()
    {
        $date = date_parse($this->plan_time);
        $str = "{$date['year']}年{$date['month']}月{$date['day']}日 {$date['hour']}点";
        return $str;
    }
    /**
     * 得到当前Process的实际执行时间
     * @return TaskPeer
     */
    public function getExecTime()
    {
        $str = '尚未执行';
        if($this->exec_time)
        {
            $str = date('Y-m-d H:i:s',strtotime($this->exec_time));
        }
        return $str;
    }
    
    /**
     * 跳过本条Process
     */
    public function setSkip()
    {
    	$this->skip = 1;
    	return $this->save();
    }
    /**
     * 恢复本条Process
     */
    public function setRestore()
    {
    	$this->skip = 0;
    	return $this->save();
    }
    
    /**
     * 该用户是否可以编辑该Process
     * @param UserPeer $author
     */
    public function isEditable($author)
    {
    	//TODO 将来可能加上管理员权限
    	$task = $this->getTask();
    	if($task->user_id == $author->user_id)
    	{
    		return true;
    	}
    	return false;
    }
    
    /**
     * 当前process是否是跳过
     */
    public function isSkip()
    {
    	return ($this->skip == 1) ?true:false;
    }
}

?>