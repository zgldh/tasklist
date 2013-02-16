<?php
class NobelMetalFetchPriceAppCommandPeer extends AppCommandPeer
{
    use app_parameter;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->commandView ( 'NobelMetalFetchPriceAppCommandPeer', array ('command' => $this ), true );
        return $html;
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getFullDescription()
     */
    public function getFullDescription($parameters = null)
    {
        $this->praseParameters ( $parameters );
        $re = $this->getFullDescriptionArray ( '获取当前黄金价格并激活相关trigger' );
        return $re;
    }
    /*
     * (non-PHPdoc) @see AppCommandPeer::execute()
     */
    public function execute($data = null)
    {
        if ($data)
        {
            $this->praseParameters ( $data );
        }

        $CI = & get_instance ();
        $CI->load->model ('Kitco_gold_model','kitco_gold_model',true);
        $re = KitcoGoldPeer::model()->fetch();
        return $re;
    }
}
