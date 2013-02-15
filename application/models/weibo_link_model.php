<?php
class Weibo_link_model extends MY_Model
{
    const TABLE = 'weibo_link';
    private $cache_user_id = null;
    /**
     *
     * @var SaeTOAuthV2
     */
    public static $sae_oauth = null;
    public function __construct()
    {
        parent::__construct ();
        $this->cache_user_id = new DB_Cache ();
        
        require_once (APPPATH . 'libraries/SaeTOAuthV2.php');
        
        $config = array ('client_id' => WB_APP_KEY, 'client_secret' => WB_APP_SECRET, 'access_token' => null, 'refresh_token' => null );
        self::$sae_oauth = new SaeTOAuthV2 ( $config );
    }
    public function getByPK($id)
    {
        if ($this->cache_pk->hasData ( $id ))
        {
            return $this->cache_pk->getData ( $id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (WeiboLinkPeer::PK => $id ) )->row_array ();
        $user = $raw ? new WeiboLinkPeer ( $raw ) : null;
        
        $this->cache_pk->setData ( $id, $user );
        
        return $user;
    }
    public function getByUserId($user_id)
    {
        if ($this->cache_user_id->hasData ( $user_id ))
        {
            return $this->cache_user_id->getData ( $user_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array ('user_id' => $user_id ) )->row_array ();
        $user = $raw ? new WeiboLinkPeer ( $raw ) : null;
        
        $this->cache_user_id->setData ( $user_id, $user );
        
        return $user;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param WeiboLinkPeer $link            
     */
    public function save(& $link)
    {
        parent::base_save ( self::TABLE, $link );
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'id',
                'user_id',
                'access_token',
                'refresh_token',
                'remind_in',
                'expries_in',
                'uid',
                'update_datetime',
                'user_data'
                );
    }
}
/**
 * sina weibo 的链接对象。<br />
 * TODO 本地服务器缓存用户名等基础信息。<br />
 * 每次生成会检查多久没更新了， 超过3天就请求一次sina服务器刷新基础信息<br />
 * 
 * @author zgldh
 * 
 * @property int $id 
 * @property int $user_id = 0 用户ID
 * @property string $access_token = '' 访问token
 * @property string $refresh_token = null; 刷新token， sina 暂时不提供
 * @property string $remind_in = ''; 提醒时间
 * @property string $expries_in = ''; 过期时间
 * @property int $uid = 0; sina微博id号
 * @property string $update_datetime = ''; 更新时间戳
 * @property object $user_data = null; weibo用户信息
 */
class WeiboLinkPeer extends BasePeer
{
    const PK = 'id';

    private $_user_data = null;
    
    /**
     *
     * @var SaeTClientV2
     */
    private $_sae_client = null;
    function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
        if ($this->user_data)
        {
            $this->_user_data = json_decode ( $this->user_data );
        }
        if ($this->access_token)
        {
            $this->_sae_client = new SaeTClientV2 ( WB_APP_KEY, WB_APP_SECRET, $this->access_token );
        }
    }
    public function getPrimaryKeyName()
    {
        return self::PK;
    }
    public function getPrimaryKeyValue()
    {
        return $this->id;
    }
    public function save()
    {
        WeiboLinkPeer::model ()->save ( $this );
    }
    /**
     *
     * @return Weibo_link_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->weibo_link_model;
    }
    
    /**
     * 得到对应的用户
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
    public static function createAndSave($user_id, $uid, $access_token, $expires_in, $remind_in)
    {
        $link = new WeiboLinkPeer ();
        
        $link->_sae_client = new SaeTClientV2 ( WB_APP_KEY, WB_APP_SECRET, $access_token );
        $user_data = $link->_sae_client->show_user_by_id ( $uid );
        $link->_user_data = $user_data;
        
        $link->user_id = $user_id;
        $link->uid = $uid;
        $link->access_token = $access_token;
        $link->expries_in = $expires_in;
        $link->remind_in = $remind_in;
        $link->update_datetime = $link->getTimeStamp ();
        $link->user_data = json_encode ( $user_data );
        $link->save ();
        return $link;
    }
    
    /**
     * 是不是该刷新数据了
     * 
     * @return boolean
     */
    public function isTimeToUpdateData()
    {
        $t = 259200; // 3 天
        if (time () - strtotime ( $this->update_datetime ) > $t)
        {
            return true;
        }
        return false;
    }
    /**
     * 获取并更新用户数据
     */
    public function updateUserData()
    {
        $data = $this->_sae_client->show_user_by_id ( $this->uid );
        $this->_user_data = $data;
        
        $this->user_data = json_encode ( $data );
        $this->update_datetime = $this->getTimeStamp ();
        $this->save ();
    }
    public function getUserData()
    {
        return $this->_user_data;
    }
    public function get_sae_id()
    {
        return $this->getData ( $this->_user_data, 'id' );
    }
    public function get_sae_screen_name()
    {
        return $this->getData ( $this->_user_data, 'screen_name' );
    }
    public function get_sae_name()
    {
        return $this->getData ( $this->_user_data, 'name' );
    }
    public function get_sae_province()
    {
        return $this->getData ( $this->_user_data, 'province' );
    }
    public function get_sae_city()
    {
        return $this->getData ( $this->_user_data, 'city' );
    }
    public function get_sae_location()
    {
        return $this->getData ( $this->_user_data, 'location' );
    }
    public function get_sae_description()
    {
        return $this->getData ( $this->_user_data, 'description' );
    }
    public function get_sae_url()
    {
        return $this->getData ( $this->_user_data, 'url' );
    }
    public function get_sae_profile_image_url()
    {
        return $this->getData ( $this->_user_data, 'profile_image_url' );
    }
    public function get_sae_profile_url()
    {
        return $this->getData ( $this->_user_data, 'profile_url' );
    }
    public function get_sae_domain()
    {
        return $this->getData ( $this->_user_data, 'domain' );
    }
    public function get_sae_weihao()
    {
        return $this->getData ( $this->_user_data, 'weihao' );
    }
    public function get_sae_gender()
    {
        return $this->getData ( $this->_user_data, 'gender' );
    }
    public function get_sae_followers_count()
    {
        return $this->getData ( $this->_user_data, 'followers_count' );
    }
    public function get_sae_friends_count()
    {
        return $this->getData ( $this->_user_data, 'friends_count' );
    }
    public function get_sae_statuses_count()
    {
        return $this->getData ( $this->_user_data, 'statuses_count' );
    }
    public function get_sae_favourites_count()
    {
        return $this->getData ( $this->_user_data, 'favourites_count' );
    }
    public function get_sae_created_at()
    {
        return $this->getData ( $this->_user_data, 'created_at' );
    }
    public function get_sae_status()
    {
        return $this->getData ( $this->_user_data, 'status' );
    }
    public function get_sae_avatar_large()
    {
        return $this->getData ( $this->_user_data, 'avatar_large' );
    }
    public function get_sae_online_status()
    {
        return $this->getData ( $this->_user_data, 'online_status' );
    }
    public function get_sae_bi_followers_count()
    {
        return $this->getData ( $this->_user_data, 'bi_followers_count' );
    }
    public function get_sae_lang()
    {
        return $this->getData ( $this->_user_data, 'lang' );
    }
    public function get_sae_star()
    {
        return $this->getData ( $this->_user_data, 'star' );
    }
    public function get_sae_mbtype()
    {
        return $this->getData ( $this->_user_data, 'mbtype' );
    }
    public function get_sae_mbrank()
    {
        return $this->getData ( $this->_user_data, 'mbrank' );
    }
    public function get_sae_block_word()
    {
        return $this->getData ( $this->_user_data, 'block_word' );
    }
}

?>