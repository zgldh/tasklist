<?php
class DateTimeMinutesCycleAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $minutes = 0;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ('minutes' );
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeMinutesCycleAppTriggerPeer', array ('trigger' => $this ), true );
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
        
        $string = sprintf ( "每隔%d分钟", $this->minutes );
        $re = $this->getFullDescriptionArray ( $string );
        return $re;
    }
    
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        if ($this->minutes == 0)
        {
            throw new Exception ( 'DateTimeMinutesCycleAppTriggerPeer::setNextTimingProcess 时间间隔minutes不能为0' );
            return false;
        }
        $next_timestamp = strtotime("+{$this->minutes} minutes" ,strtotime($timing_process->plan_time));
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
    }
    
    /**
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        if ($this->minutes == 0)
        {
            throw new Exception ( 'DateTimeMinutesCycleAppTriggerPeer::generateTimingProcess 时间间隔minutes不能为0' );
            return false;
        }
        
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $next_timestamp = time ();
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
        return $timing_process;
    }
}
