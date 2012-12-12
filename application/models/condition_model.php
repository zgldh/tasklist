<?php
require_once('conditions/DateStaticConditionPeer.php');

class Condition_model extends MY_Model
{
    const TABLE = 'condition';
    private $_error = null;
    public function getByPK($condition_id)
    {
        if ($this->cache_pk->hasData ( $condition_id ))
        {
            return $this->cache_pk->getData ( $condition_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (ConditionPeer::PK => $condition_id ) )->row_array ();
        $condition = $raw ? $this->makeConditionPeer($raw->type,$raw): false;
        
        $this->cache_pk->setData ( $condition_id, $condition );
        
        return $condition;
    }

    /**
     * 
	 * 得到某任务的所有条件
	 * @param int $task_id
	 * @param DB_Limit $limitObj = null 查询limit
	 * @param string $type = null 条件的类型
     * @return multitype:Ambigous <Ambigous, NULL, DateStaticConditionPeer>
     */
	public function getByTaskId($task_id, $limitObj = null,$type = null)
	{
	    $re = array();
	    if($limitObj)
	    {
	        $limitObj->setLimit($this->db);
	    }
	    if($type)
	    {
	    	$this->db->where('type',$type);
	    }
	    $this->db->where('task_id',$task_id);
	    $rows = $this->db->get(self::TABLE)->result();
	    foreach($rows as $row)
	    {
	        $condition = $this->makeConditionPeer($row->type, $row);
	        $re[] = $condition;
	    }
	    return $re;	    
	}
    /**
     * 更新数据 或 插入数据
     *
     * @param ConditionPeer $user            
     */
    public function save(& $user)
    {
        parent::base_save( self::TABLE, $user );
    }
	/**
	 * 删除一个 ConditionPeer
	 * @param ConditionPeer $peer
	 * @return boolean
	 */
	public function delete(& $peer)
	{
		return parent::base_delete(self::TABLE, $peer);
	}
    
    /**
     * 创建一个新的ConditionPeer.<br />
     * 创建失败返回false
     * 创建成功返回ConditionPeer<br />
     * 失败的时候会生成一条Last error：  array('type'=>'错误的条件类型: '.$type)
     * @param string $type 条件类型  ConditionPeer::TYPE_xxxx
     * @param array $parameters 条件配置数据
     * @param int $task_id = null 所属任务id
     * @return boolean|ConditionPeer
     */
    public function createNew($type, $parameters,$task_id = null)
    {
    	if(!$this->isValidType($type))
    	{
    		$this->setLastError(array('type'=>'错误的条件类型: '.$type));
    		return false;
    	}
    	$condition = $this->makeConditionPeer($type, null);
    	if($task_id)
    	{
    		$condition->task_id = $task_id;
    	}
    	$condition->type = $type;
    	$condition->setParameters($parameters);
    	return $condition;
    }
    /**
     * 返回最后的错误数据
     * @return 
     */
    public function getLastError()
    {
    	return $this->_error;
    }
    /**
     * 设置错误
     * @return 
     */
    public function setLastError($data)
    {
    	$this->_error = $data;
    }
    
    /**
     * 是否是合法的ConditionType字符串
     * @param string $type
     * @return boolean
     */
    public function isValidType($type)
    {
    	$types = array(ConditionPeer::TYPE_DATE_STATIC);
    	return in_array($type, $types);
    }
    
    /**
     * 
     * 依照Type生成对应的ConditionPeer
     * @param string $type ConditionPeer::TYPE_xxx
     * @param array|obj $data
     * @return Ambigous <NULL, DateStaticConditionPeer>
     */
    public function makeConditionPeer($type, $data)
    {
        $peer = null;
        switch($type)
        {
            case ConditionPeer::TYPE_DATE_STATIC:
                $peer = new DateStaticConditionPeer($data);
                break;
            default:
                break;
        }
        return $peer;
    }
}

class ConditionPeer extends BasePeer
{
    const PK = 'condition_id';
    
    /**
     * 条件类型： 特定日期
     * @var string
     */
    const TYPE_DATE_STATIC = 'date-static';
    
    /**
     *
     * @var int
     */
    public $condition_id = 0;
    /**
     * 条件所属的任务
     * 
     * @var int
     */
    public $task_id = 0;
    /**
     * 条件类型
     * 
     * @var string
     */
    public $type = '';
    /**
     * 本条件的参数, json格式
     * 
     * @var string
     */
    public $parameters = '';
    /**
     * 更新时间戳
     * 
     * @var string
     */
    public $update_timestamp = '';
    
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
        return $this->condition_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp();
        ConditionPeer::model ()->save ( $this );
    }
    public function delete()
    {
        ConditionPeer::model ()->delete($this);
    }
    /**
     *
     * @return Condition_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->condition_model;
    }
    /**
     * 得到json_decode后的parameters
     * @return mixed
     */
    public function getParameters()
    {
    	return json_decode($this->parameters);
    }
    /**
     * 设置parameters  
     * @param mixed $data 本参数会被 json_encode
     */
    public function setParameters($data)
    {
    	$this->parameters = json_encode($data);
    }
    /**
     * 
     * 得到本条件所属的TaskPeer
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
     * 检查本条件是否可以被目标用户编辑
     * TODO 未来可能加上管理员权限
     * @param UserPeer $user
     */
    public function isEditableByUser($user)
    {        
        $task = $this->getTask();
        if($task->user_id == $user->user_id)
        {
            return true;
        }
        return false;
    }
    /**
     * 生成并且储存对应的一系列Process<br />
     * 这些Process有可能在将来被遍历、判断
     */
    public function generateAndSaveProcesses()
    {
        throw new Exception('ConditionPeer::generateAndSaveProcesses 必须被重载!');
    }
}

?>