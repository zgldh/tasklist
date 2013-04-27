<?php
class WeatherWindAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView( 'WeatherWindAppTriggerPeer', array ('command' => $this ), true );
        return $html;
    }
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getFullDescription()
     */
    public function getFullDescription($parameters = null)
    {
        $this->praseParameters ( $parameters );
        
        return $re;
    }
	/* (non-PHPdoc)
     * @see AppTriggerPeer::getPrivateParameters()
     */public function getPrivateParameters()
    {
        // TODO Auto-generated method stub
        }

	/* (non-PHPdoc)
     * @see AppTriggerPeer::generateTimingProcess()
     */public function generateTimingProcess()
    {
        // TODO Auto-generated method stub
        }

	/* (non-PHPdoc)
     * @see AppTriggerPeer::setNextTimingProcess()
     */public function setNextTimingProcess($timing_process)
    {
        // TODO Auto-generated method stub
        }

}
