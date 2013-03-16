<?php
require_once (APPPATH . 'libraries/trait/app_parameters.php');
class App_trigger_model extends MY_Model
{
    const TABLE = 'app_trigger';
    
    /**
     *
     * @param array $parameters            
     * @return AppTriggerPeer
     */
    public function parseFormParameters($parameters)
    {
        $trigger_id = $this->getData ( $parameters, 'id' );
        $trigger = $this->getByPK ( $trigger_id );
        $trigger->praseParameters ( $parameters );
        return $trigger;
    }
    
    /**
     *
     * @param int $trigger_id            
     * @return AppTriggerPeer
     */
    public function getByPK($trigger_id)
    {
        if ($this->cache_pk->hasData ( $trigger_id ))
        {
            return $this->cache_pk->getData ( $trigger_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (AppTriggerPeer::PK => $trigger_id ) )->row_array ();
        $trigger = $raw ? $this->factory ( $raw ) : null;
        
        $this->cache_pk->setData ( $trigger_id, $trigger );
        
        return $trigger;
    }
    /**
     * 得到一个app的trigger们
     * 
     * @param int $app_id            
     * @param int $level
     *            0-9 0:normal 9:system
     * @return multitype:AppTriggerPeer
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
     * @param AppTriggerPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /**
     * 删除一个AppTriggerPeer
     * 
     * @param AppTriggerPeer $peer            
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
     * key应当对应着app_trigger表内的app_trigger_id
     * 
     * @var array
     */
    private static $APP_TRIGGER_MAP = array (
            null,     // 0
            'DateTimeHourlyAppTriggerPeer', 
            'DateTimeDailyAppTriggerPeer', 
            'DateTimeWeeklyAppTriggerPeer', 
            'DateTimeMonthlyAppTriggerPeer', 
            'DateTimeYearlyAppTriggerPeer', 
            'DateTimeStaticDateAppTriggerPeer', 
            'DateTimeStaticFestivalAppTriggerPeer', 
            'DateTimeMinutesCycleAppTriggerPeer', 
            'NobelMetalChangeAppTriggerPeer', 
            'NobelMetalUpToAppTriggerPeer',     // 10
            'NobelMetalDownToAppTriggerPeer',
            'WeatherTemperatureAppTriggerPeer',
            'WeatherConditionAppTriggerPeer',
            'WeatherTemperatureTomorrowAppTriggerPeer',
            'WeatherConditionTomorrowAppTriggerPeer',
            'WeatherAirQualityIndexAppTriggerPeer',
            'WeatherWindAppTriggerPeer',
            'WeatherWindTomorrowAppTriggerPeer',
            'WeatherRelativeHumidityAppTriggerPeer',
            'WeatherUltraVioletIndexAppTriggerPeer',    //20
            null
            );
    
    /**
     *
     * @param object|array $trigger_raw            
     * @return AppTriggerPeer
     */
    public function factory($trigger_raw)
    {
        $trigger_id = $this->getData ( $trigger_raw, 'trigger_id' );
        $trigger_map = self::$APP_TRIGGER_MAP;
        $class_name = @$trigger_map [$trigger_id];
        $peer = null;
        if ($class_name)
        {
            require_once (APPPATH . 'models/app_triggers/' . $class_name . '.php');
            $peer = new $class_name ( $trigger_raw );
        }
        return $peer;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'trigger_id',
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
 * @property int $trigger_id = null 应用触发条件id
 * @property int $app_id = null 应用ID
 * @property string $name = null 应用触发条件名字
 * @property string $description = null 应用触发条件描述
 * @property string $update_timestamp = null 更新时间戳
 * @property string $parameters = null 触发条件相关参数 被json_encode过
 * @property int $level = 0 条件等级 0:normal; 9: system
 * 
 * @method void praseParameters($data) 将$data解析进一个特定的AppTriggerPeer里面
 * 
 * @author zgldh
 *
 */
abstract class AppTriggerPeer extends BasePeer
{
    const PK = 'trigger_id';
    
    /**
     * 必须重载
     * 
     * @param obj/array $raw
     *            = null
     * @param string $className
     *            = null
     */
    function __construct($raw = null, $className = null)
    {
        if ($className === null)
        {
            $className = __CLASS__;
        }
        parent::__construct ( $raw, $className );
    }
    public function getPrimaryKeyName()
    {
        return self::PK;
    }
    public function getPrimaryKeyValue()
    {
        return $this->trigger_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp ();
        return self::model ()->save ( $this );
    }
    /**
     * 删除一个AppTriggerPeer。
     * 
     * @return boolean 成功true
     */
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return App_trigger_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->app_trigger_model;
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
     * 得到本trigger的配置用html
     * 
     * @throws Exception
     */
    abstract public function getDetailHTML();
    /**
     * 得到本trigger的完全描述。 需要先提供本trigger的参数
     * 
     * @throws Exception
     */
    abstract public function getFullDescription($parameters = null);
    
    /**
     * 返回详细参数数组 array('day','hour','minute');
     * 
     * @throws Exception
     */
    abstract public function getPrivateParameters();

    /**
     * 生成并返回本trigger的timing_process对象.
     * @return TimingProcessPeer
     */
    abstract public function generateTimingProcess();
    /**
     * 更新一个timing_process 的执行时间
     * @param TimingProcessPeer $timing_process
     */
    abstract public function setNextTimingProcess($timing_process);
    
    /**
     * 输出|渲染一个应用触发条件的html
     * 
     * @param string $view_name            
     * @param array $vars
     *            = array()
     * @param boolean $return
     *            = FALSE true:返回html结果
     * @return Ambigous <void, string>
     */
    protected function triggerView($view_name, $vars = array(), $return = FALSE)
    {
        $ci = & get_instance ();
        $html = $ci->load->view ( 'app_triggers/' . $view_name, $vars, $return );
        return $html;
    }
    
    /**
     * 生成一个数组， array(
     * 'trigger_id'=>$id,
     * 'description'=>$html )
     * 
     * @param string $html            
     * @return array
     */
    protected function getFullDescriptionArray($html)
    {
        $re = array ('trigger_id' => $this->trigger_id, 'description' => $html );
        return $re;
    }
    
    /**
     * 根据当前app trigger， 生成一个 task trigger
     * 
     * @return TaskTriggerPeer
     */
    public function generateTaskTrigger()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Task_trigger_model', 'task_trigger_model', true );
        
        $trigger = TaskTriggerPeer::model ()->generateByAppTrigger ( $this );
        return $trigger;
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
            $re [$name] = $this->getPrivateParameter($name);
        }
        $string = json_encode ( $re );
        return $string;
    }
}

?>