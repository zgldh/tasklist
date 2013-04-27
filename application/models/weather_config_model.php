<?php
class Weather_config_model extends MY_Model
{
    const TABLE = 'weather_config';
    
    /**
     * 根据主键获取一条
     * @param int $weather_config_id
     * @return Ambigous <NULL, WeatherConfigPeer>
     */
    public function getByPK($weather_config_id)
    {
        if ($this->cache_pk->hasData ( $weather_config_id ))
        {
            return $this->cache_pk->getData ( $weather_config_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (WeatherConfigPeer::PK => $weather_config_id ) )->row_array ();
        $peer = $raw ? new WeatherConfigPeer($raw ) : null;
        
        $this->cache_pk->setData ( $peer );
        
        return $peer;
    }
    
    /**
     * 根据用户id得到一条
     * @param int $user_id 
     * @return Ambigous <NULL, WeatherConfigPeer>
     */
    public function getByUserId($user_id)
    {
    	$raw = $this->db->get_where ( self::TABLE, array ('weather_city_id'=> $user_id ) )->row_array ();
    	$peer = $raw ? new WeatherConfigPeer($raw ) : null;

    	$this->cache_pk->setData ( $peer );
    	
    	return $peer;
    }
    
    /**
     * @method得到全部激活的城市
     * @return array<WeatherConfigPeer>
     */
    public function getAllCities()
    {
        $sql = "SELECT * FROM ".self::TABLE.' ';
        $sql.= "GROUP BY weather_city_id ";
        
        $query = $this->db->query($sql);
        $rows = $query->result();
        
        $re = array();
        foreach ($rows as $row)
        {
            $peer = new WeatherConfigPeer($row );
            $re[] = $peer;
            $this->cache_pk->setData ( $peer );
        }
        return $re;
    }
    
    
    /**
     * 更新数据 或 插入数据
     *
     * @param WeatherConfigPeer $user            
     */
    public function save(& $user)
    {
        return parent::base_save ( self::TABLE, $user );
    }
    
    /**
     * 删除一个 WeatherConfigPeer
     *
     * @param WeatherConfigPeer $peer            
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
        		'weather_config_id',
        		'user_id',
        		'weather_city_id',
        		'update_datetime'
                );
    }
}
/**
 * @property int $weather_config_id = null	主键
 * @property int $user_id = null	用户id
 * @property string $weather_city_id = null	城市id
 * @property string $update_datetime = null	更新时间戳
 * @author zgldh
 */
class WeatherConfigPeer extends BasePeer
{
    const PK = 'weather_config_id';
    
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
        return $this->weather_config_id;
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
     * @return Weather_config_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->weather_config_model;
    }
    
    public function getUser()
    {
    	$CI = & get_instance ();
    	$CI->load->model ( 'User_model', 'user_model', true );
    	
    	$user = UserPeer::model ()->getByPK ( $this->user_id );
    	return $user;
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