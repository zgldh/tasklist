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
    
    /**
     *
     * @param string $table            
     * @param BasePeer $base_peer            
     */
    public function base_save($table, $base_peer)
    {
        $pkValue = $base_peer->getPrimaryKeyValue ();
        if ($pkValue)
        {
            foreach ( $base_peer as $key => $val )
            {
                $this->db->set ( $key, $val );
            }
            $this->db->where ( $base_peer->getPkWhere (), null, false );
            $this->db->update ( $table );
        }
        else
        {
            foreach ( $base_peer as $key => $val )
            {
                $this->db->set ( $key, $val );
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
    public function setData($key, $val)
    {
        $this->_data [$key] = $val;
    }
    public function unsetData($key)
    {
        unset ( $this->_data [$key] );
    }
}
class BasePeer
{
    function __construct($raw = null, $className = null)
    {
        if ($raw == null || $className == null)
        {
            return;
        }
        if (is_array ( $raw ) || is_object ( $raw ) || get_class ( $raw ) == $className)
        {
            foreach ( $raw as $key => $item )
            {
                if (property_exists ( $className, $key ))
                {
                    $this->$key = $item;
                }
            }
        }
        else
        {
            throw new Exception ( 'Bad raw data for ' . $className );
        }
    }
    /**
     *
     * @param Array|Object $raw            
     * @param string $key            
     * @return Ambigous <NULL, unknown>
     */
    protected function getRawItemValue($raw, $key, $default = null)
    {
        $re = $default;
        if (is_array ( $raw ))
        {
            if (isset ( $raw [$key] ))
            {
                $re = $raw [$key];
            }
        }
        elseif (is_object ( $raw ))
        {
            if (property_exists ( $raw, $key ))
            {
                $re = $raw->$key;
            }
        }
        
        return $re;
    }
    public function getPrimaryKeyValue()
    {
        throw new Exception ( 'You have to re-write this function: BasePeer::getPrimaryKeyValue' );
    }
    public function getPrimaryKeyName()
    {
        throw new Exception ( 'You have to re-write this function: BasePeer::getPrimaryKeyName' );
    }
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
}

/* End of file Controller.php */
/* Location: ./application/core/MY_Model.php */