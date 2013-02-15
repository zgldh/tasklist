<?php
class Kitco_gold_model extends MY_Model
{
    const TABLE = 'kitco_gold';
    public function getByPK($command_id)
    {
        if ($this->cache_pk->hasData ( $command_id ))
        {
            return $this->cache_pk->getData ( $command_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (KitcoGoldPeer::PK => $command_id ) )->row_array ();
        $command = $raw ? $this->makeKitcoGoldPeer ( $raw->type, $raw ) : null;
        
        $this->cache_pk->setData ( $command_id, $command );
        
        return $command;
    }
    
    /**
     * 更新数据 或 插入数据
     *
     * @param KitcoGoldPeer $user            
     */
    public function save(& $user)
    {
        return parent::base_save ( self::TABLE, $user );
    }
    
    /**
     * 删除一个 KitcoGoldPeer
     *
     * @param KitcoGoldPeer $peer            
     * @return boolean
     */
    public function delete(& $peer)
    {
        return parent::base_delete ( self::TABLE, $peer );
    }
    
    /**
     * 从 kitco.cn 获取最新价格并保存
     */
    public function fetch()
    {
        echo "KitcoGoldModel::fetch 1";
        
        $request_url_rmb = 'http://www.kitco.cn/KitcoDynamicSite/RequestHandler?requestName=getFileContent&AttributeId=PreciousMetalsSpotPricesCNY';
        $request_url = 'http://www.kitco.cn/KitcoDynamicSite/RequestHandler?requestName=getFileContent&AttributeId=PreciousMetalsSpotPrices';
        
        $peer = new KitcoGoldPeer ();
        
        $content = file_get_contents ( $request_url_rmb );
        $content = mb_convert_encoding ( $content, 'UTF-8', 'gbk' );

        echo "KitcoGoldModel::fetch 2";        
        
        if ($content)
        {
            $gold_start = '黄金</td>';
            $gold_end = '</tr>';
            $gold_html = $this->getBetween ( $content, $gold_start, $gold_end );
            $gold_html = trim ( preg_replace ( '/\h/', '', strip_tags ( $gold_html ) ) );
            $gold_array = explode ( "\n", $gold_html );
            
            $peer->rmb_gram_buy = 0 + $gold_array [2];
            $peer->rmb_gram_sell = 0 + $gold_array [3];
            $peer->rmb_gram_change_value = 0 + trim ( $gold_array [4] );
            $peer->rmb_gram_change_rate = 0 + trim ( $gold_array [5] );
        }
        
        $content = file_get_contents ( $request_url );
        $content = mb_convert_encoding ( $content, 'UTF-8', 'gbk' );
        
        if ($content)
        {
            $gold_start = '黄金</td>';
            $gold_end = '</tr>';
            $gold_html = $this->getBetween ( $content, $gold_start, $gold_end );
            $gold_html = trim ( preg_replace ( '/\h/', '', strip_tags ( $gold_html ) ) );
            $gold_array = explode ( "\n", $gold_html );
            
            $peer->dollar_ounce_buy = 0 + $gold_array [2];
            $peer->dollar_ounce_sell = 0 + $gold_array [3];
            $peer->dollar_ounce_change_value = 0 + trim ( $gold_array [4] );
            $peer->dollar_ounce_change_rate = 0 + trim ( $gold_array [5] );
        }

        echo "KitcoGoldModel::fetch 3";
        $peer->fetch_date = $this->getTimeStamp ();
        $peer->save ();

        echo "KitcoGoldModel::fetch 4";
    }
    private function getBetween($string, $start, $end, & $offset = 0)
    {
        if ($offset == 0)
        {
            $start_pos = strpos ( $string, $start );
        }
        else
        {
            $start_pos = strpos ( $string, $start, $offset );
        }
        
        $end_offset = $start_pos + strlen ( $start );
        $end_pos = strpos ( $string, $end, $end_offset );
        
        $result_start = $end_offset;
        $result_end = $end_pos;
        $result_length = $end_pos - $result_start;
        
        $result = substr ( $string, $result_start, $result_length );
        return $result;
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'id',
                'fetch_date',
                'rmb_gram_buy',
                'rmb_gram_sell',
                'rmb_gram_change_rate',
                'rmb_gram_change_value',
                'dollar_ounce_buy',
                'dollar_ounce_sell',
                'dollar_ounce_change_rate',
                'dollar_ounce_change_value'
                );
    }
}
/**
 * @property int $id = null 主键
 * @property string $fetch_date = null;
 * @property number $rmb_gram_buy = 0;
 * @property number $rmb_gram_sell = 0;
 * @property number $rmb_gram_change_rate = 0;
 * @property number $rmb_gram_change_value = 0;
 * @property number $dollar_ounce_buy = 0;
 * @property number $dollar_ounce_sell = 0;
 * @property number $dollar_ounce_change_rate = 0;
 * @property number $dollar_ounce_change_value = 0;
 * @author zgldh
 *
 */
class KitcoGoldPeer extends BasePeer
{
    const PK = 'id';
    
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
        return self::model ()->save ( $this );
    }
    public function delete()
    {
        return self::model ()->delete ( $this );
    }
    /**
     *
     * @return Kitco_gold_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->kitco_gold_model;
    }
}

?>