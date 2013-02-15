<?php 
trait app_parameter
{
    /**
     * 用 data 给当前对象的属性赋值
     * @param unknown_type $data
     */
	public function praseParameters($data)
	{
	    $private_parameters = $this->getPrivateParameters();
	    if(is_array($data))
	    {
    		foreach($private_parameters as $parameter_name)
    		{
    		    if(isset($data[$parameter_name]))	
                {
                	$this->$parameter_name = $data[$parameter_name];
                }
    		}
	    }
	    elseif(is_object($data))
	    {
    		foreach($private_parameters as $parameter_name)
    		{
    		    if(isset($data->$parameter_name))	
                {
                	$this->$parameter_name = $data->$parameter_name;
                }
    		}
	    }
	}
	
	/**
     * 得到当前对象的私有属性名字的数组 array('foo','bar')
	 */
	public function getPrivateParameters()
	{
        return array();
	}
	/**
     * 得到当前对象的私有属性的值
     * @param string $name 属性名
	 */
	public function getPrivateParameter($name)
	{
	    if(isset($this->$name))
	    {
	        return $this->$name;
	    }
	    return null;
	}
}