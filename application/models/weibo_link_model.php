<?php
class Weibo_link_model extends MY_Model
{
	const TABLE = 'weibo_link';
	
	private $cache_user_id = null;
	
	public function __construct()
	{
	    parent::__construct();
	    $this->cache_user_id = new DB_Cache();
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
		parent::base_save( self::TABLE, $link );
	}
}
/**
 * sina weibo 的链接对象。<br /> 
 * TODO 本地服务器缓存用户名等基础信息。<br />
 * 每次生成会检查多久没更新了， 超过3天就请求一次sina服务器刷新基础信息<br />
 * @author zgldh
 *
 */
class WeiboLinkPeer extends BasePeer
{
	const PK = 'id';
	public $id = 0;
	/**
	 * 用户ID
	 *
	 * @var int
	 */
	public $user_id = 0;
	/**
	 * 访问token
	 * @var string
	 */
    public $access_token = '';
	/**
	 * 刷新token， sina 暂时不提供
	 * @var string
	 * @ignore
	 */
    public $refresh_token = null;
    /**
     * 提醒时间
     * @var string
     */
    public $remind_in = '';
    /**
     * 过期时间
     * @var string
     */
    public $expries_in = '';
    /**
     * sina微博id号
     * @var int
     */
    public $uid = 0;
	/**
	 * 更新时间戳
	 * @var string
	 */
	public $update_datetime = '';
	

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
	 * @return UserPeer
	 */
	public function getUser()
	{
		$CI = & get_instance ();
		$CI->load->model('User_model','user_model',true);
		
		$user = UserPeer::model()->getByPK($this->user_id);
		return $user;
	}
	
	public static function createAndSave($user_id,$uid,$access_token,$expires_in,$remind_in)
	{
	    $link = new WeiboLinkPeer();
	    $link->user_id = $user_id;
	    $link->uid = $uid;
	    $link->access_token = $access_token;
	    $link->expries_in = $expires_in;
	    $link->remind_in = $remind_in;
	    $link->update_datetime = $link->getTimeStamp();
	    $link->save();
	    return $link;
	}
}

?>