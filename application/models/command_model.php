<?php
require_once ('commands/UrlRequestCommandPeer.php');
require_once ('commands/SendEmailCommandPeer.php');

class Command_model extends MY_Model
{
	const TABLE = 'command';
	private $_error = null;

	public function getByPK($command_id)
	{
		if ($this->cache_pk->hasData ( $command_id ))
		{
			return $this->cache_pk->getData ( $command_id );
		}
		
		$raw = $this->db->get_where ( self::TABLE, array (CommandPeer::PK => $command_id ) )->row_array ();
		$command = $raw ?$this->makeCommandPeer($raw->type, $raw) : null;
		
		$this->cache_pk->setData ( $command_id, $command );
		
		return $command;
	}
	
	/**
	 *
	 *
	 * 得到某任务的所有命令
	 * 
	 * @param int $task_id        	
	 * @param DB_Limit $limitObj
	 *        	= null 查询limit
	 * @param string $type
	 *        	= null 条件的类型
	 * @return multitype:CommandPeer
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
			$re [] = $this->makeCommandPeer($row->type, $row);
		}
		return $re;
	}
	
	/**
	 * 更新数据 或 插入数据
	 *
	 * @param CommandPeer $user        	
	 */
	public function save(& $user)
	{
		return parent::base_save ( self::TABLE, $user );
	}
	
	/**
	 * 删除一个 CommandPeer
	 * 
	 * @param CommandPeer $peer        	
	 * @return boolean
	 */
	public function delete(& $peer)
	{
		return parent::base_delete ( self::TABLE, $peer );
	}
	/**
	 * 创建一个新的CommandPeer.<br />
	 * 创建失败返回false
	 * 创建成功返回CommandPeer<br />
	 * 失败的时候会生成一条Last error： array('type'=>'错误的命令类型: '.$type)
	 * 
	 * @param string $type
	 *        	命令类型 CommandPeer::TYPE_xxxx
	 * @param array $data
	 *        	命令参数
	 * @param int $task_id
	 *        	= null 所属任务id
	 * @return boolean CommandPeer
	 */
	public function createNew($type, $data, $task_id = null)
	{
		if (! $this->isValidType ( $type ))
		{
			$this->setLastError ( array ('type' => '错误的条件类型: ' . $type ) );
			return false;
		}
		$command = $this->makeCommandPeer($type, null);
		if ($task_id)
		{
			$command->task_id = $task_id;
		}
		$command->type = $type;
		$parameters_error = $command->setParameters ( $data );
    	if($parameters_error)
    	{
    		$this->setLastError(array('parameters'=>$parameters_error));
    		return false;
    	}
		return $command;
	}
	/**
	 * 返回最后的错误数据
	 * 
	 * @return
	 *
	 */
	public function getLastError()
	{
		return $this->_error;
	}
	/**
	 * 设置错误
	 * 
	 * @return
	 *
	 */
	public function setLastError($data)
	{
		$this->_error = $data;
	}
	
	/**
	 * 是否是合法的 Command Type 字符串
	 * 
	 * @param string $type        	
	 * @return boolean
	 */
	public function isValidType($type)
	{
		$types = array (CommandPeer::TYPE_SEND_EMAIL, CommandPeer::TYPE_URL_REQUEST );
		return in_array ( $type, $types );
	}
	/**
	 * 
     * 依照Type生成对应的CommandPeer
	 * @param string $type
	 * @param array $data
	 * @return CommandPeer
	 */
	public function makeCommandPeer($type, $data)
    {
        $peer = null;
        switch($type)
        {
            case CommandPeer::TYPE_URL_REQUEST:
                $peer = new UrlRequestCommandPeer($data);
                break;
            case CommandPeer::TYPE_SEND_EMAIL:
                $peer = new SendEmailCommandPeer($data);
                break;
            default:
                break;
        }
        return $peer;
    }
}
class CommandPeer extends BasePeer
{
	const PK = 'command_id';
	
	/**
	 * 命令类型 访问URL
	 * 
	 * @var string
	 */
	const TYPE_URL_REQUEST = 'url-request';
	/**
	 * 命令类型 发送电子邮件
	 * 
	 * @var string
	 */
	const TYPE_SEND_EMAIL = 'send-email';
	
	/**
	 *
	 * @var int
	 */
	public $command_id = 0;
	/**
	 * 命令所属的任务
	 *
	 * @var int
	 */
	public $task_id = 0;
	/**
	 * 命令类型
	 *
	 * @var string
	 */
	public $type = '';
	/**
	 * 本命令的参数
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
		return $this->command_id;
	}
	public function save()
	{
		$this->update_timestamp = $this->getTimeStamp ();
		return self::model ()->save ( $this );
	}
	public function delete()
	{
		return self::model ()->delete ( $this );
	}
	/**
	 *
	 * @return Command_model
	 */
	public static function model()
	{
		$CI = & get_instance ();
		return $CI->command_model;
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
	 * 设置parameters<br />
	 * 本函数保证data里面对应字段都存在，不负责对应字段的值的正确性<br />
	 * 字段的值的正确性应该由特定子类的setupParameters函数负责
	 * @param mixed $data
	 *        	本参数会被 json_encode
     * @return null|string 正常返回null， 出错返回错误信息
	 */
	public function setParameters($data)
	{
		$this->parameters = json_encode ( $data );
    	return null;
	}
	
	/**
	 * 得到本命令所属的TaskPeer
	 * 
	 * @return Ambigous <boolean, TaskPeer>
	 */
	public function getTask()
	{
		$CI = & get_instance ();
		$CI->load->model ( 'Task_model', 'task_model', true );
		$task = TaskPeer::model ()->getByPK ( $this->task_id );
		return $task;
	}
	/**
	 * 检查本命令是否可以被目标用户编辑
	 * TODO 未来可能加上管理员权限
	 * 
	 * @param UserPeer $user        	
	 */
	public function isEditableByUser($user)
	{
		$task = $this->getTask ();
		if ($task->user_id == $user->user_id)
		{
			return true;
		}
		return false;
	}
}

?>