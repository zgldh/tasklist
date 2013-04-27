<?php
class WeatherFetchRecordAppCommandPeer extends AppCommandPeer
{
    use app_parameter;
    use can_to_next;
    
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->commandView ( 'WeatherFetchRecordAppCommandPeer', array ('command' => $this ), true );
        return $html;
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getFullDescription()
     */
    public function getFullDescription($parameters = null)
    {
        $this->praseParameters ( $parameters );
        $re = $this->getFullDescriptionArray ( '获取城市天气信息，并且激活trigger' );
        return $re;
    }
    /*
     * (non-PHPdoc) @see AppCommandPeer::execute()
     */
    public function execute($data = null)
    {
        if ($data)
        {
            $this->praseParameters ( $data );
        }
        
        $return = null;
        
        $ci = $this->getCI();
        $ci->load->model('Weather_city_model','weather_city_model',true);
        $ci->load->model('Weather_config_model','weather_config_model',true);
        
        //尝试得到尚未获取到天气信息的城市
        $city = WeatherCityPeer::model()->getCities(WeatherCityPeer::FETCHED_WAITING,true);
        if($city)
        {
            //有一个城市， 获取它的天气信息
            $record = $city->fetchWeatherRecord();
            if($record)
            {
                $record->save();
                $city->fetched = WeatherCityPeer::FETCHED_DONE;
                $city->save();
                $return = true;
            }
            else
            {
                //获取失败
                $return = false;
            }
            $this->setCanMoveToNext(false);
        }
        else
        {
            //没有要获取的城市
            //将被激活的的城市们设置为待获取 （weather_config）
            $city_configs = WeatherConfigPeer::model()->getAllCities();
            foreach($city_configs as $city_config)
            {
                $city_config instanceof WeatherConfigPeer;
                $city = $city_config->getWeatherCity();
                $city->fetched = WeatherCityPeer::FETCHED_WAITING;
                $city->save();
            }
            
            //然后转到下一个执行时间点
            $this->setCanMoveToNext(true);
            $return = true;
        }
        
        return $return;
    }
}
