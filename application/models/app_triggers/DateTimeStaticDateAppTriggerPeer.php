<?php
class DateTimeStaticDateAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $year = 2099;
    private $month = 1;
    private $day = 1;
    private $hour = 1;
    private $minute = 0;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ('year', 'month', 'day', 'hour', 'minute' );
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeStaticDateAppTriggerPeer', array ('trigger' => $this ), true );
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
        
        $string = sprintf ( "%d年%d月%d日的%d点%d分", $this->year, $this->month, $this->day, $this->hour, $this->minute );
        $re = $this->getFullDescriptionArray ( $string );
        return $re;
    }

    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
    */
    public function setNextTimingProcess($timing_process)
    {
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        if ($next_timestamp === false)
        {
            throw new Exception ( "DateTimeStaticDateAppTriggerPeer::setNextTimingProcess 参数错误  {$this->year}, {$this->month}, {$this->day}, {$this->hour}, {$this->minute}" );
            return false;
        }
        
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
    }
    
    /**
     * (non-PHPdoc) @see AppTriggerPeer::generateTimingProcess()
     */
    public function generateTimingProcess()
    {
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        if ($next_timestamp === false)
        {
            throw new Exception ( "DateTimeStaticDateAppTriggerPeer::generateTimingProcess 参数错误  {$this->year}, {$this->month}, {$this->day}, {$this->hour}, {$this->minute}" );
            return false;
        }
        
        $CI = & get_instance ();
        $CI->load->model ( 'Timing_process_model', 'timing_process_model', true );
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
        return $timing_process;
    }
    private function getNextTriggerTimeStamp()
    {
        $now = time ();
        
        $that_day_string = sprintf ( '%04d-%02d-%02d %02d:%02d:00', $this->year, $this->month, $this->day, $this->hour, $this->minute );
        $that_day = strtotime ( $that_day_string );
        
        if ($now < $that_day)
        {
            $date_string = $this->timestampToDatetimeString ( $that_day );
            if ($that_day_string == $date_string)
            {
                // that's the time
                $flag = true;
            }
        }
        
        if ($flag === true)
        {
            return $that_day;
        }
        else
        {
            return false;
        }
    }
}
