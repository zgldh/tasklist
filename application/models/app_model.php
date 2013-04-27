<?php
class App_model extends MY_Model
{
    const TABLE = 'app';
    
    /**
     *
     * @param int $app_id            
     * @return AppPeer
     */
    public function getByPK($app_id)
    {
        if ($this->cache_pk->hasData ( $app_id ))
        {
            return $this->cache_pk->getData ( $app_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (AppPeer::PK => $app_id ) )->row_array ();
        $task = $raw ? $this->factory ( $raw ) : null;
        
        $this->cache_pk->setData ( $app_id, $task );
        
        return $task;
    }
    /**
     *
     * @param int $type
     *            null: all; triggers: has triggers; commands: has commands
     * @param DB_Limit $limitObj            
     * @return multitype:AppPeer
     */
    public function getAll($type = null, $limitObj = null, $level = null)
    {
        $re = array ();
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        
        if ($type === 'triggers')
        {
            $this->db->where ( 'triggers_count > 0' );
        }
        elseif ($type === 'commands')
        {
            $this->db->where ( 'commands_count > 0' );
        }
        
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
     * @param AppPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /**
     * 删除一个AppPeer
     * 
     * @param AppPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /**
     * key应当对应着 app 表内的顺序
     * 
     * @var array
     */
    private static $APP_MAP = array (
            null, 
            'DateTimeAppPeer', 
            'SinaWeiboAppPeer', 
            'EmailAppPeer', 
            'NobelMetalAppPeer', 
            'WeatherAppPeer',
    		'WeixinAppPeer',
    		'RSSAppPeer'
    		);
    
    /**
     *
     * @param object|array $app_raw            
     * @return AppPeer
     */
    public function factory($app_raw)
    {
        $app_id = $this->getData ( $app_raw, 'app_id' );
        $app_map = self::$APP_MAP;
        $class_name = @$app_map [$app_id];
        $peer = null;
        if ($class_name)
        {
            require_once (APPPATH . 'models/apps/' . $class_name . '.php');
            $peer = new $class_name ( $app_raw );
        }
        return $peer;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'app_id',
                'name',
                'description',
                'update_timestamp',
                'triggers_count',
                'commands_count',
                'level'
                );
    }
}
/**
 * @property int $app_id = null 应用id
 * @property string $name = null 应用名字
 * @property string $description = null 应用描述
 * @property string $update_timestamp = null 更新时间戳
 * @property int $triggers_count = 0 有多少trigger
 * @property int $commands_count = 0 有多少命令
 * @property int $level = 0 应用等级 0:normal; 9: system
 * @author zgldh
 *
 */
abstract class AppPeer extends BasePeer
{
    const PK = 'app_id';
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
        return $this->app_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp ();
        return self::model ()->save ( $this );
    }
    /**
     * 删除一个AppPeer。
     * 
     * @return boolean 成功true
     */
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return App_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->app_model;
    }
    
    /**
     * 是否是自动active的app
     */
    abstract public function isAutoActive();
    /**
     * 将某用户的本应用状态设置为actived， 会在app_active表增加一条记录, 返回刚刚增加的peer
     * @return AppActivePeer
     */
    abstract public function autoActive($user_id);
    /**
     * 得到本应用的激活表单html 
     */
    abstract public function getActiveForm();
    
    /**
     * 得到 应用 的名字
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
    public function getActivedStatusByUser($user_id)
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_active_model', 'app_active_model', true );
        
        $active_peer = AppActivePeer::model ()->getActivedStatus ( $this->app_id, $user_id );
        
        if($active_peer == null && $this->isAutoActive())
        {
            $active_peer = $this->autoActive($user_id);
        }
        
        return $active_peer;
    }
    
    /**
     * 
     * @param int $trigger_level = null trigger等级
     * @return Ambigous <multitype:AppTriggerPeer>
     */
    public function getTriggers($trigger_level = null)
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_trigger_model', 'app_trigger_model', true );
        
        $triggers = AppTriggerPeer::model()->getByAppId($this->app_id,$trigger_level);
        return $triggers;
    }
    

    /**
     * 输出|渲染一个应用 的html
     *
     * @param string $view_name
     * @param array $vars
     *            = array()
     * @param boolean $return
     *            = FALSE 如果是true,则返回html结果
     * @return Ambigous <void, string>
     */
    protected function getAppView($view_name, $vars = array(), $return = FALSE)
    {
        $ci = & get_instance ();
        $html = $ci->load->view ( 'app/' . $view_name, $vars, $return );
        return $html;
    }
}

?>