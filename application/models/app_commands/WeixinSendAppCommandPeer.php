<?php
class WeixinSendAppCommandPeer extends AppCommandPeer
{
    use app_parameter;
    
    private $weixin_title = '';
    private $weixin_content = '';
    
    public function getPrivateParameters()
    {
        return array ('weixin_title', 'weixin_content' );
    }
    
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
        $html = $this->commandView ( 'WeixinSendAppCommandPeer', array ('command' => $this ), true );
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
        
        return $re;
    }
    
    /*
     * (non-PHPdoc) @see AppCommandPeer::execute()
     */
    public function execute($data = null)
    {
        if($data)
        {
            $this->praseParameters($data);
        }
        // TODO WeixinSendAppCommandPeer::execute
    }
}
