<?php
class App_active_model extends MY_Model
{
    const TABLE = 'app_active';
    
    /**
     *
     * @param int $active_id            
     * @return AppActivePeer
     */
    public function getByPK($active_id)
    {
        if ($this->cache_pk->hasData ( $active_id ))
        {
            return $this->cache_pk->getData ( $active_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (AppActivePeer::PK => $active_id ) )->row_array ();
        $app = $raw ? new AppActivePeer ( $raw ) : null;
        
        $this->cache_pk->setData ( $active_id, $app );
        
        return $app;
    }
    /**
     *
     * @param int $app_id            
     * @return AppActivePeer
     */
    public function getActivedStatus($app_id, $user_id)
    {
        $this->db->where ( 'app_id', $app_id );
        $this->db->where ( 'user_id', $user_id );
        $raw = $this->db->get ( self::TABLE )->row_array ();
        $peer = $raw ? new AppActivePeer ( $raw ) : null;
        
        $this->cache_pk->setData ( $peer );
        return $peer;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param AppActivePeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /**
     * 删除一个AppActivePeer
     * 
     * @param AppActivePeer $peer            
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
                'active_id',
                'app_id',
                'user_id',
                'actived',
                'update_timestamp'
                );
    }
    
    /**
     * 
     * @param int $app_id
     * @param int $user_id
     * @return AppActivePeer
     */
    public function create($app_id, $user_id)
    {
        $peer = new AppActivePeer();
        $peer->app_id = $app_id;
        $peer->user_id = $user_id;
        $peer->actived = AppActivePeer::ACTIVED_YES;
        $peer->update_timestamp = $this->getTimeStamp();
        return $peer;
    }
}
/**
 * @property int $active_id = null 应用激活id
 * @property int $app_id = null 应用id
 * @property int $user_id = null 用户id
 * @property int $actived = 0 激活状态 0: not actived; 1: actived
 * @property string $update_timestamp = null 更新时间戳
 * @author zgldh
 *
 */
class AppActivePeer extends BasePeer
{
    const PK = 'active_id';
    
    /**
     * active状态
     * 
     * @var int
     */
    const ACTIVED_YES = 1;
    /**
     * un-active状态
     * 
     * @var int
     */
    const ACTIVED_NO = 0;
    
    
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
        return $this->active_id;
    }
    public function save()
    {
        $this->update_timestamp = $this->getTimeStamp ();
        return self::model ()->save ( $this );
    }
    /**
     * 删除一个AppActivePeer。
     * 
     * @return boolean 成功true
     */
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return App_active_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->app_active_model;
    }
    
    /**
     *
     *
     * 得到 用户
     * 
     * @return UserPeer
     */
    public function getUser()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'User_model', 'user_model', true );
        
        $user = UserPeer::model ()->getByPK ( $this->user_id );
        return $user;
    }
    /**
     *
     *
     * 得到 用户
     * 
     * @return AppPeer
     */
    public function getApp()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_model', 'app_model', true );
        
        $app = AppPeer::model ()->getByPK ( $this->app_id );
        return $app;
    }
    /**
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->actived == self::ACTIVED_YES;
    }
    /**
     *
     * @return boolean
     */
    public function isUnActive()
    {
        return $this->actived == self::ACTIVED_NO;
    }
}

?>