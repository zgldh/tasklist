<?php
class Invitation_model extends MY_Model
{
    const TABLE = 'user';
    public function getByPK($invitation_id)
    {
        if ($this->cache_pk->hasData ( $invitation_id ))
        {
            return $this->cache_pk->getData ( $invitation_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (InvitationPeer::PK => $invitation_id ) )->row_array ();
        $invitation = $raw ? new InvitationPeer ( $raw ) : false;
        
        $this->cache_pk->setData ( $invitation_id, $invitation );
        
        return $invitation;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param InvitationPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'invitation_id',
                'code',
                'user_id',
                'used',
                'creator_id'
                );
    }
}
/**
 * @property int $invitation_id = 0 邀请码id
 * @property string $code = '' 邀请码
 * @property int $user_id = 0 用户id
 * @property int $used = 0 该邀请码是否用过了: 0没用过,1用过了
 * @property int $creator_id = 0 生成邀请码的人的id
 * @author zgldh
 *
 */
class InvitationPeer extends BasePeer
{
    const PK = 'user_id';
 
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
        return $this->invitation_id;
    }
    public function save()
    {
        InvitationPeer::model ()->save ( $this );
    }
    /**
     *
     * @return Invitation_Model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->invitation_model;
    }
}

?>