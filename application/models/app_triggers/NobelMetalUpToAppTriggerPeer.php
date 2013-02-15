<?php
class NobelMetalUpToAppTriggerPeer extends AppTriggerPeer
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
        $html = $this->triggerView ( 'NobelMetalUpToAppTriggerPeer', array ('trigger' => $this ), true );
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
    /*
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( null, TimingProcessPeer::STATUS_IGNORE );
        return $timing_process;
    }
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        $timing_process->updateParameters(null, TimingProcessPeer::STATUS_IGNORE);
    }
}
