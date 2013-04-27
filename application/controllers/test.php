<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller
{
	public function index()
	{
		header('Location: /');
		exit();
	}


	/**
	 * 刷新app的trigger统计和command统计
	 */
	public function fetch_weather()
	{
		$this->loadWeatherCityModel();
		
		$city = WeatherCityPeer::model()->getByPK(101180101);
		$cities = array($city);
// 		$cities = WeatherCityPeer::model()->getCities();
		
		for($i=0;$i<10 && $i < count($cities);$i++)
		{
		    $city = $cities[$i];
		    $result = $city->fetchWeatherRecord();
		    $result->save();
		    print_r($result);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */