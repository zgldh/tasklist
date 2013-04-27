<?php
class RSSNewItemAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $url = '';
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
    	return array ('url');
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppCommandPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView( 'RSSNewItemAppTriggerPeer', array ('trigger' => $this ), true );
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
        $string = sprintf ( "RSS源地址：%s",$this->url );
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
