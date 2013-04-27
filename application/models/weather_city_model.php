<?php
class Weather_city_model extends MY_Model
{
    const TABLE = 'weather_city';
    
    /**
     * 根据主键获取一条
     * @param int $weather_city_id 比如 北京的 city_id 是 101010100
     * @return Ambigous <NULL, WeatherCityPeer>
     */
    public function getByPK($weather_city_id)
    {
        if ($this->cache_pk->hasData ( $weather_city_id ))
        {
            return $this->cache_pk->getData ( $weather_city_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (WeatherCityPeer::PK => $weather_city_id ) )->row_array ();
        $peer = $raw ? new WeatherCityPeer($raw ) : null;
        
        $this->cache_pk->setData ( $weather_city_id, $peer );
        
        return $peer;
    }
    
    /**
     * 
     * @param int $feched = null WeatherCityPeer::FETCHED_xxx
     * @param boolean $last_one = false
     * @return Ambigous <NULL, WeatherCityPeer>|multitype:Ambigous <WeatherCityPeer, NULL>
     */
    public function getCities($feched = null, $last_one = false)
    {
        if($feched !== null)
        {
            $this->db->where('fetched', $feched);
        }
        
        if($last_one)
        {
            $raw = $this->db->get(self::TABLE,1)->row_array ();
            $peer = $raw ? new WeatherCityPeer($raw ) : null;
            $this->cache_pk->setData ( $peer );
            return $peer;
        }
        else
        {
            $re = array();
            $raws = $this->db->get(self::TABLE)->result();
            foreach($raws as $raw)
            {
                $peer = new WeatherCityPeer ( $raw );
                $re [] = $peer;
                $this->cache_pk->setData ( $peer );
            }
            return $re;
        }
    }
    
    /**
     * @method 更新数据 或 插入数据
     * @param WeatherCityPeer $user            
     */
    public function save(& $user)
    {
        return parent::base_save ( self::TABLE, $user );
    }
    
    /**
     * @method 删除一个 WeatherCityPeer
     * @param WeatherCityPeer $peer            
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
        		'weather_city_id',
        		'city_name',
        		'fetched'
                );
    }
}
/**
 * @property int $weather_city_id = null 主键
 * @property string $city_name = null	城市名字
 * @property int $fetched = 0   0忽略， 1等待获取， 2获取完毕  WeatherCityPeer::FETCHED_xxx
 * @author zgldh
 */
class WeatherCityPeer extends BasePeer
{
    const PK = 'weather_city_id';

    /**
     * @var int 忽略状态
     */
    const FETCHED_IGNORE = 0;
    /**
     * @var int 等待获取
     */
    const FETCHED_WAITING = 1;
    /**
     * @var int 获取完毕
     */
    const FETCHED_DONE = 2;
    
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
        return $this->weather_city_id;
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
     * @return Weather_city_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->weather_city_model;
    }
    
    /**
     * @method获取本城市的天气信息
     * @return WeatherRecordPeer
     */
    public function fetchWeatherRecord()
    {
        require_once(APPPATH.'libraries/Snoopy.class.php');
        
        $this->weather_city_id; 
        $snoopy = new Snoopy();
//         $snoopy->referer = "http://ext.weather.com.cn/p.html";
//         if(!$snoopy->fetch("http://ext.weather.com.cn/".$this->weather_city_id.".json"))
        if(!$snoopy->fetch("http://www.weather.com.cn/data/sk/".$this->weather_city_id.".html"))
        {
            return false;
        }
        $json = $snoopy->results;
        $result = json_decode($json);
        $weatherinfo = $result->weatherinfo;

        $ci = $this->getCI();
        $ci->load->model('Weather_record_model','weather_record_model',true);
        
        $record = new WeatherRecordPeer();
        $record->weather_city_id = $this->weather_city_id;
        $record->record_timestamp = date("Y-m-d ".$weatherinfo->time);
        $record->temperature = $weatherinfo->temp;
        $record->wind_direction = $weatherinfo->WD;
        $record->wind_speed = $weatherinfo->WSE;
        $record->relative_humidity = trim($weatherinfo->SD,'%');
        
        return $record;
    }
}

?>