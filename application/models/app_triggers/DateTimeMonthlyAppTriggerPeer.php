<?php
class DateTimeMonthlyAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    private $day = 1;
    private $hour = 1;
    private $minute = 0;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ('day', 'hour', 'minute' );
    }
    /**
     * (non-PHPdoc)
     * 
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeMonthlyAppTriggerPeer', array ('trigger' => $this ), true );
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
        
        if ($this->day == - 1)
        {
            $string = sprintf ( "每月最后一天的%d点%d分", $this->hour, $this->minute );
        }
        else
        {
            $string = sprintf ( "每月%d日的%d点%d分", $this->day, $this->hour, $this->minute );
        }
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
            throw new Exception ( "DateTimeMonthlyAppTriggerPeer::setNextTimingProcess 参数错误 {$this->day}, {$this->hour}, {$this->minute}" );
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
            throw new Exception ( "DateTimeMonthlyAppTriggerPeer::generateTimingProcess 参数错误 {$this->day}, {$this->hour}, {$this->minute}" );
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
        
        $info = getdate ( $now );
        $year = $info ['year'];
        $month = $info ['mon'];
        
        $CI = & get_instance ();
        $CI->load->helper ( 'date' );
        
        $dday = $this->day;
        if ($this->day == - 1)
        {
            $dday = days_in_month ( $month, $year );
        }
        $that_day_string = sprintf ( '%04d-%02d-%02d %02d:%02d:00', $year, $month, $dday, $this->hour, $this->minute );
        $that_day = strtotime ( $that_day_string );
        
        $flag = false;
        for($limit = 0; $limit < 12; $limit ++)
        {
            if ($now < $that_day)
            {
                $date_string = $this->timestampToDatetimeString ( $that_day );
                if ($that_day_string == $date_string)
                {
                    // that's the time
                    $flag = true;
                    break;
                }
            }
            
            $this->getNextYearMonth ( $year, $month );
            if ($this->day == - 1)
            {
                $dday = days_in_month ( $month, $year );
            }
            $that_day_string = sprintf ( '%04d-%02d-%02d %02d:%02d:00', $year, $month, $dday, $this->hour, $this->minute );
            $that_day = strtotime ( $that_day_string );
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
    private function getNextYearMonth(&$year, &$month)
    {
        $month ++;
        if ($month > 12)
        {
            $month = 1;
            $year ++;
        }
    }
}
