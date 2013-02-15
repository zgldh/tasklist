<?php
class DateTimeHourlyAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $minute = 0;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ('minute' );
    }
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeHourlyAppTriggerPeer', array ('trigger' => $this ), true );
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
        
        $string = sprintf ( "每小时的%d分", $this->minute );
        $re = $this->getFullDescriptionArray ( $string );
        return $re;
    }
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
    }
    
    /**
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
        return $timing_process;
    }
    private function getNextTriggerTimeStamp()
    {
        $that_time = strtotime ( date ( sprintf ( 'Y-m-d H:%02d:00', $this->minute ) ) );
        $now = time ();
        if ($now >= $that_time)
        {
            $that_time = $that_time + 3600;
        }
        return $that_time;
    }
}
