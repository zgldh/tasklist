<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
/**
 *
 * @author zgldh
 * @property CI_DB_active_record $db
 */
class MY_Model extends CI_Model
{
    /**
     * 主键缓存
     * 
     * @var DB_Cache
     */
    protected $cache_pk = null;
    function __construct()
    {
        $this->cache_pk = new DB_Cache ();
        parent::__construct ();
    }
    public static function getTimeStamp($string = true, $format = 'Y-m-d H:i:s')
    {
        $re = time ();
        if ($string == true)
        {
            $re = date ( $format, $re );
        }
        return $re;
    }
    public static function timestampToDatetimeString($timestamp, $format = 'Y-m-d H:i:s')
    {
        $str = null;
        if($timestamp)
        {
            $str = date($format,$timestamp);
        }
        return $str;
    }
    
    /**
     *
     * @param string $table            
     * @param BasePeer $base_peer            
     */
    public function base_save($table, $base_peer)
    {
        $pkValue = $base_peer->getPrimaryKeyValue ();
        $columns = $this->columns();
        if ($pkValue)
        {
            foreach ( $columns as $column_name)
            {
                $this->db->set ( $column_name, $base_peer->$column_name );
            }
            $this->db->where ( $base_peer->getPkWhere (), null, false );
            $this->db->update ( $table );
        }
        else
        {
            foreach ( $columns as $column_name)
            {
                $this->db->set ( $column_name, $base_peer->$column_name );
            }
            $this->db->insert ( $table );
            $base_peer->setPrimaryKeyvalue ( $this->db->insert_id () );
        }
        
        return true;
    }
    
    /**
     *
     * @param string $table            
     * @param BasePeer $base_peer            
     */
    protected function base_delete($table, $base_peer)
    {
        $where = $base_peer->getPkWhere ();
        if ($where)
        {
            $this->db->where ( $where, null, false );
            return $this->db->delete ( $table );
        }
    }
    
    /**
     * @return array 例如: array('task_id','create_date');
     */
    protected function columns()
    {
        throw new Exception('MY_Model.columns must be implement');
    }
    
    /**
     * 得到一个数组/对象里的一个值
     * 
     * @param Array|Object $arr            
     * @param string $key_name            
     * @param any $default
     *            = null 默认值
     * @return any
     */
    public function getData($arr, $key_name, $default = null)
    {
        if (is_array ( $arr ) && isset ( $arr [$key_name] ))
        {
            return $arr [$key_name];
        }
        if (is_object ( $arr ) && property_exists ( $arr, $key_name ))
        {
            return $arr->$key_name;
        }
        return $default;
    }
}
// END Model class

/**
 * 用来给 $this->db 做limit限制的对象<br />
 *
 * @author Zhangwb
 *        
 */
class DB_Limit
{
    public $offset = null;
    public $limit = null;
    
    /**
     * 必须要有 $limit
     *
     * @param int $limit            
     * @param int $offset
     *            = null
     */
    function __construct($limit = null, $offset = null)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }
    
    /**
     * 如果limit为空 则什么也不做。<br />
     * 如果offset为空， 则只限制limit<br />
     * 否则将同时加上偏移量和limit
     *
     * @param CI_DB_active_record $db            
     */
    public function setLimit($db)
    {
        if ($this->limit == null)
        {
            return;
        }
        if ($this->offset == null)
        {
            $limit = ( int ) $this->limit;
            $db->limit ( $limit );
        }
        else
        {
            $offset = ( int ) $this->offset;
            $limit = ( int ) $this->limit;
            $db->limit ( $limit, $offset );
        }
    }
}
class DB_Cache
{
    private $_data = array ();
    function __construct()
    {
    }
    public function hasData($key)
    {
        return array_key_exists ( $key, $this->_data );
    }
    public function getData($key)
    {
        return @$this->_data [$key];
    }
    /**
     * useage: <br />$cache->setData(12, $peer); <br />
     * or <br />
     * $cache->setData($peer);  
     * @param int|BasePeer $key
     * @param BasePeer $val = null
     */
    public function setData($key, $val = null)
    {
    	if($val != null)
    	{
    		$this->_data [$key] = $val;
    	}
    	elseif(is_object($key) && method_exists($key,'getPrimaryKeyValue'))
    	{
    		$pk = $key->getPrimaryKeyValue();
    		$this->_data[$pk] = $key;
    	}
    }
    public function unsetData($key)
    {
        unset ( $this->_data [$key] );
    }
}
abstract class BasePeer
{
    protected $_vars = array();
    
    public function getVars()
    {
        return $this->_vars;
    }
    
    public function __get($name)
    {
        if (array_key_exists($name, $this->_vars)) {
            return $this->_vars[$name];
        }

//         $trace = debug_backtrace();
//         trigger_error('Undefined property via __get(): ' . $name .' in ' . $trace[0]['file'] .' on line ' . $trace[0]['line'],E_USER_NOTICE);
        return null;
    }
    public function __set($name,$value)
    {
        $this->_vars[$name] = $value;
    }
    
    function __construct($raw = null, $className = null)
    {
        if ($raw == null)
        {
            return;
        }
        if(is_object ( $raw ))
        {
            if(get_class ( $raw ) == $className)
            {
                $this->_vars = $raw->getVars();
            }
            else
            {
                $this->_vars = (array)$raw;
            }
        }
        else
        {
            $this->_vars = $raw;
        }
//         if (is_array ( $raw ) || is_object ( $raw ) || get_class ( $raw ) == $className)
//         {
//             $this->_vars = $raw;
//             foreach ( $raw as $key => $item )
//             {
//                 if (property_exists ( $className, $key ))
//                 {
//                     $this->$key = $item;
//                 }
//             }
//         }
//         else
//         {
//             throw new Exception ( 'Bad raw data for ' . $className );
//         }
    }
    /**
     *
     * @param Array|Object $arr            
     * @param string $key_name            
     * @return Ambigous <NULL, unknown>
     */
    protected function getData($arr, $key_name, $default = null)
    {
        if (is_array ( $arr ) && isset ( $arr [$key_name] ))
        {
            return $arr [$key_name];
        }
        if (is_object ( $arr ) && property_exists ( $arr, $key_name ))
        {
            return $arr->$key_name;
        }
        return $default;
    }
    
    abstract public function getPrimaryKeyValue();
    abstract function getPrimaryKeyName();
    
    public function getPkWhere()
    {
        $pk_value = mysql_real_escape_string ( $this->getPrimaryKeyValue () );
        if ($pk_value)
        {
            $where = $this->getPrimaryKeyName () . "='" . $this->getPrimaryKeyValue () . "'";
            return $where;
        }
        else
        {
            throw new Exception ( "Bad PK Value: " . $this->getPrimaryKeyValue () . " in BasePeer::getPkWhere" );
        }
    }
    public function setPrimaryKeyvalue($value)
    {
        $pk = $this->getPrimaryKeyName ();
        $this->$pk = $value;
    }
    public function getTimeStamp($return_string = true, $format = null)
    {
        if ($format === null)
        {
            return MY_Model::getTimeStamp ( $return_string );
        }
        else
        {
            return MY_Model::getTimeStamp ( $return_string ,$format);
        }
    }
    public function timestampToDatetimeString($timestamp, $format = null)
    {
        if ($format === null)
        {
            return MY_Model::timestampToDatetimeString ( $timestamp );
        }
        else
        {
            return MY_Model::timestampToDatetimeString ( $timestamp ,$format);
        }
    }
}

/* End of file Controller.php */
/* Location: ./application/core/MY_Model.php */