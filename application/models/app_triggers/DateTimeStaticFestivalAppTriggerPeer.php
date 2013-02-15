<?php
class DateTimeStaticFestivalAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ();
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeStaticFestivalAppTriggerPeer', array ('trigger' => $this ), true );
        return $html;
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getFullDescription()
     */
    public function getFullDescription($parameters = null)
    {
    }
    
    /**
     * TODO DateTimeStaticFestivalAppTriggerPeer::generateTimingProcess
     * 特定节日生成TimingProcess
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        return false;
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
        return $timing_process;
    }
    private function getNextTriggerTimeStamp()
    {
    }
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        // TODO DateTimeStaticFestivalAppTriggerPeer::setNextTimingProcess
    }
}
