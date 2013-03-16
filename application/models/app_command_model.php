<?php
require_once (APPPATH . 'libraries/trait/app_parameters.php');
class App_command_model extends MY_Model
{
    const TABLE = 'app_command';
    
    /**
     *
     * @param array $parameters            
     * @return AppCommandPeer
     */
    public function parseFormParameters($parameters)
    {
        $command_id = $this->getData ( $parameters, 'id' );
        $command = $this->getByPK ( $command_id );
        $command->praseParameters ( $parameters );
        return $command;
    }
    
    /**
     *
     * @param int $command_id            
     * @return AppCommandPeer
     */
    public function getByPK($command_id)
    {
        if ($this->cache_pk->hasData ( $command_id ))
        {
            return $this->cache_pk->getData ( $command_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (AppCommandPeer::PK => $command_id ) )->row_array ();
        $task = $raw ? $this->factory ( $raw ) : null;
        
        $this->cache_pk->setData ( $command_id, $task );
        
        return $task;
    }
    /**
     * 得到一个app的command们
     * 
     * @param int $app_id            
     * @param int $level
     *            0-9 0:normal 9:system
     * @return multitype:AppCommandPeer
     */
    public function getByAppId($app_id, $level = null)
    {
        $re = array ();
        
        $this->db->where ( 'app_id', $app_id );
        if ($level !== null)
        {
            $this->db->where ( 'level', $level );
        }
        
        $rows = $this->db->get ( self::TABLE )->result ();
        foreach ( $rows as $row )
        {
            $peer = $this->factory ( $row );
            $re [] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param AppCommandPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /**
     * 删除一个AppCommandPeer
     * 
     * @param AppCommandPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /**
     *
     * @param int $app_id            
     * @return Number
     */
    public function countByAppId($app_id)
    {
        $this->db->where ( 'app_id', $app_id );
        $num = $this->db->count_all_results ( self::TABLE );
        return $num;
    }
    
    /**
     * key应当对应着app_command表内的app_command_id
     * 
     * @var array 
     *      1	3	发送电子邮件	会向一个指定的电子邮箱发送电子邮件	2013-02-03 17:53:54		0
     *      2	2	发送文字微博	用您的微博账号发送一条文字微博	2013-02-03 17:56:56		0
     *      3	2	发送图片微博	用您的微博账号发送一条图片微博	2013-02-03 17:57:02		0
     *      4	4	获取黄金当前价格	抓取kitco.cn的金价数据并且保存。然后激活app_id=4的应用的所有trigger	2013-02-03 18:33:53		9
     *     
     */
    private static $APP_COMMAND_MAP = array (
            null,     // 0
            'EmailSendMailAppCommandPeer',
            'SinaWeiboTextStatusAppCommandPeer', 
            'SinaWeiboPictureStatusAppCommandPeer', 
            'NobelMetalFetchPriceAppCommandPeer', 
            null, null, null, null, null, null,     // 10
    null );
    
    /**
     *
     * @param object|array $command_raw            
     * @return AppCommandPeer
     */
    public function factory($command_raw)
    {
        $command_id = $this->getData ( $command_raw, 'command_id' );
        $command_map = self::$APP_COMMAND_MAP;
        $class_name = @$command_map [$command_id];
        $peer = null;
        if ($class_name)
        {
            require_once (APPPATH . 'models/app_commands/' . $class_name . '.php');
            $peer = new $class_name ( $command_raw );
        }
        return $peer;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'command_id',
                'app_id',
                'name',
                'description',
                'update_timestamp',
                'parameters',
                'level'
                );
    }
}
/**
 * @property int $command_id = null 应用命令id
 * @property int $app_id = null 应用ID
 * @property string $name = null 应用命令名字
 * @property string $description = null 应用命令描述
 * @property string $update_timestamp = null 更新时间戳
 * @property string $parameters = null 应用命令相关参数
 * @property int $level = 0 应用命令等级 0:normal; 9: system
 * 
 * @method void praseParameters($data) 将$data解析进一个特定的AppCommandPeer里面
 * 
 * @author zgldh
 *
 */
abstract class AppCommandPeer extends BasePeer
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
    /**
     * 删除一个AppCommandPeer。
     * 
     * @return boolean 成功true
     */
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return App_command_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->app_command_model;
    }
    
    /**
     * 得到 触发条件 的名字
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
     * 得到本command的配置用html
     * 
     * @throws Exception
     */
    abstract public function getDetailHTML();
    /**
     * 得到本command的完全描述。 需要先提供本command的参数
     * 
     * @throws Exception
     */
    abstract public function getFullDescription($parameters = null);
    /**
     * 执行本命令
     * @param object/array $data = null
     */
    abstract public function execute($data = null);

    /**
     * 返回详细参数数组 array('day','hour','minute');
     * 本函数应当被重载。 否则返回空数组
     * @return array();
     */
    public function getPrivateParameters()
    {
        return array();
    }
    
    /**
     * 输出|渲染一个应用命令的html
     * 
     * @param string $view_name            
     * @param array $vars
     *            = array()
     * @param boolean $return
     *            = FALSE true:返回html结果
     * @return Ambigous <void, string>
     */
    protected function commandView($view_name, $vars = array(), $return = FALSE)
    {
        $ci = & get_instance ();
        $html = $ci->load->view ( 'app_commands/' . $view_name, $vars, $return );
        return $html;
    }
    
    /**
     * 生成一个数组， array(
     * 'command_id'=>$id,
     * 'description'=>$html )
     * 
     * @param string $html            
     * @return array
     */
    protected function getFullDescriptionArray($html)
    {
        $re = array ('command_id' => $this->command_id, 'description' => $html );
        return $re;
    }
    
    /**
     * 根据当前app command， 生成一个 task command
     * 
     * @return TaskCommandPeer
     */
    public function generateTaskCommand()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_command_model', 'task_command_model', true );
        
        $command = TaskCommandPeer::model()->generateByAppTrigger ( $this );
        return $command;
    }

    /**
     * 序列化当前参数
     */
    public function serializeParameters()
    {
        $private_parameters = $this->getPrivateParameters ();
        $re = array ();
        foreach ( $private_parameters as $name )
        {
            $re [$name] = $this->$name;
        }
        $string = json_encode ( $re );
        return $string;
    }
}

?>