<?php
class Weather_record_model extends MY_Model
{
    const TABLE = 'weather_record';
    
    /**
     * 根据主键获取一条
     * @param int $weather_config_id
     * @return Ambigous <NULL, WeatherRecordPeer>
     */
    public function getByPK($weather_config_id)
    {
        if ($this->cache_pk->hasData ( $weather_config_id ))
        {
            return $this->cache_pk->getData ( $weather_config_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (WeatherRecordPeer::PK => $weather_config_id ) )->row_array ();
        $peer = $raw ? new WeatherRecordPeer($raw ) : null;
        
        $this->cache_pk->setData ( $peer );
        
        return $peer;
    }
    
    /**
     * 根据用户id得到一条
     * @param int $user_id 
     * @return Ambigous <NULL, WeatherRecordPeer>
     */
    public function getByUserId($user_id)
    {
    	$raw = $this->db->get_where ( self::TABLE, array ('weather_city_id'=> $user_id ) )->row_array ();
    	$peer = $raw ? new WeatherRecordPeer($raw ) : null;

    	$this->cache_pk->setData ( $peer );
    	
    	return $peer;
    }
    
    /**
     * 更新数据 或 插入数据
     *
     * @param WeatherRecordPeer $user            
     */
    public function save(& $user)
    {
        return parent::base_save ( self::TABLE, $user );
    }
    
    /**
     * 删除一个 WeatherRecordPeer
     *
     * @param WeatherRecordPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
        		'weather_record_id',
                'weather_city_id',
                'record_timestamp',
                'update_timestamp',
                'temperature',
                'wind_direction',
                'wind_speed',
                'relative_humidity'
                );
    }
}
/**
 * @property int $weather_record_id = null	主键
 * @property string $weather_city_id = null	城市数字id
 * @property string $record_timestamp = null	本天气记录的时间戳
 * @property string $update_timestamp = null	本条记录的入库时间戳
 * @property string $temperature = null	气温， 摄氏度
 * @property string $wind_direction = null	风向： 东南风
 * @property string $wind_speed = null	风力， 用int表示
 * @property string $relative_humidity = null 相对湿度， 用int表示。 45既45%相对湿度
 * @author zgldh
 */
class WeatherRecordPeer extends BasePeer
{
    const PK = 'weather_record_id';
    
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
        return $this->weather_record_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp();
        return self::model ()->save ( $this );
    }
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return Weather_record_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->weather_record_model;
    }
    
    public function getWeatherCity()
    {
    	$CI = & get_instance ();
    	$CI->load->model ( 'Weather_city_model', 'weather_city_model', true );
    	
    	$city = WeatherCityPeer::model()->getByPK($this->weather_city_id);
    	return $city;
    }
}

?>