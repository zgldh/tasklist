<?php
class Weibo_link_model extends MY_Model
{
	const TABLE = 'weibo_link';
	public function getByPK($user_id)
	{
		if ($this->cache_pk->hasData ( $user_id ))
		{
			return $this->cache_pk->getData ( $user_id );
		}
		
		$raw = $this->db->get_where ( self::TABLE, array (WeiboLinkPeer::PK => $user_id ) )->row_array ();
		$user = $raw ? new WeiboLinkPeer ( $raw ) : null;
		
		$this->cache_pk->setData ( $user_id, $user );
		
		return $user;
	}
	/**
	 * 更新数据 或 插入数据
	 *
	 * @param WeiboLinkPeer $user        	
	 */
	public function save(& $user)
	{
		parent::base_save( self::TABLE, $user );
	}
}
class WeiboLinkPeer extends BasePeer
{
	const PK = 'user_id';
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
	public $token = '';
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
		return $this->user_id;
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
}

?>