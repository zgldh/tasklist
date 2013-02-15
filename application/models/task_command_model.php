<?php
require_once ('commands/UrlRequestCommandPeer.php');
require_once ('commands/SendEmailCommandPeer.php');
class Task_command_model extends MY_Model
{
    const TABLE = 'task_command';
    private $_error = null;
    public function getByPK($command_id)
    {
        if ($this->cache_pk->hasData ( $command_id ))
        {
            return $this->cache_pk->getData ( $command_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (TaskCommandPeer::PK => $command_id ) )->row_array ();
        $command = $raw ? $this->makeCommandPeer ( $raw ) : null;
        
        $this->cache_pk->setData ( $command );
        
        return $command;
    }
    
    /**
     *
     *
     *
     * 得到某任务的所有命令
     *
     * @param int $task_id            
     * @param DB_Limit $limitObj
     *            = null 查询limit
     * @param string $type
     *            = null 条件的类型
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
            $re [] = $this->makeCommandPeer ( $row );
        }
        return $re;
    }
    
    /**
     * 更新数据 或 插入数据
     *
     * @param TaskCommandPeer $user            
     */
    public function save(& $user)
    {
        return parent::base_save ( self::TABLE, $user );
    }
    
    /**
     * 删除一个 CommandPeer
     *
     * @param TaskCommandPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    /**
     * 返回最后的错误数据
     *
     * @return
     *
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
        $types = array (TaskCommandPeer::TYPE_SEND_EMAIL, TaskCommandPeer::TYPE_URL_REQUEST );
        return in_array ( $type, $types );
    }
    /**
     *
     *
     * 依照Type生成对应的CommandPeer
     * 
     * @param string $type            
     * @param array $data            
     * @return TaskCommandPeer
     */
    public function makeCommandPeer($data)
    {
        $peer = new TaskCommandPeer($data);
        return $peer;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'command_id',
                'task_id',
                'app_command_id',
                'parameters',
                'update_timestamp'
                );
    }
    
    /**
     * 
     * @param AppCommandPeer $app_command
     * @return TaskCommandPeer
     */
    public function generateByAppTrigger($app_command)
    {
        $command = new TaskCommandPeer();
        $command->app_command_id = $app_command->command_id;
        $command->parameters = $app_command->serializeParameters ();
        return $command;
    }
}
/**
 * @property int $command_id = 0; 
 * @property int $task_id = 0 命令所属的任务
 * @property int $app_command_id = 0 本命令所属的应用命令Id
 * @property string $parameters = '' 本命令的参数
 * @property string $update_timestamp = '' 更新时间戳
 * @author zgldh
 *
 */
class TaskCommandPeer extends BasePeer
{
    const PK = 'command_id';
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
     * @return Task_command_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->task_command_model;
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
     * 得到本命令所属的TaskPeer
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
     * 得到所对应的AppCommandPeer
     * @return AppCommandPeer
     */
    public function getAppCommand()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_command_model', 'app_command_model', true );
        $app_command = AppCommandPeer::model()->getByPK($this->app_command_id);
        return $app_command;
    }
    /**
     * 执行该命令
     */
    public function execute()
    {
        echo "execute 1\n";
        $app_command = $this->getAppCommand();
        echo "execute 2\n";
        $app_command->praseParameters($this->getParameters());
        echo "execute 3\n";
        $result = $app_command->execute();
        echo "execute 4\n";
        return $result;
    }
}

?>