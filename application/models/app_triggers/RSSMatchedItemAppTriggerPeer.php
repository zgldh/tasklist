<?php
class RSSMatchedItemAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $url = '';
    private $match_str = '';
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
    	return array ('url', 'match_str');
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView( 'RSSMatchedItemAppTriggerPeer', array ('trigger' => $this ), true );
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
        $string = sprintf ( "匹配的关键字：%s<br />RSS源地址：%s",$this->match_str,$this->url );
        $re = $this->getFullDescriptionArray ( $string );
        return $re;
    }  
    /*
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        // TODO Auto-generated method stub
    }
    
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        // TODO Auto-generated method stub
    }
}
