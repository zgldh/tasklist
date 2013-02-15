<?php
class DateTimeYearlyAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
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
        return array ('month', 'day', 'hour', 'minute' );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeYearlyAppTriggerPeer', array ('trigger' => $this ), true );
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
            $string = sprintf ( "每年%d月最后一天的%d点%d分", $this->month, $this->hour, $this->minute );
        }
        else
        {
            $string = sprintf ( "每年%d月%d日的%d点%d分", $this->month, $this->day, $this->hour, $this->minute );
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
        if($next_timestamp === false)
        {
            throw new Exception(sprintf('DateTimeYearlyAppTriggerPeer::setNextTimingProcess 参数错误 month:%s;day:%s;hour:%s;minute:%s',$this->month,$this->day,$this->hour,$this->minute));
            return false;
        }
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
        if($next_timestamp === false)
        {
            throw new Exception(sprintf('DateTimeYearlyAppTriggerPeer::generateTimingProcess 参数错误 month:%s;day:%s;hour:%s;minute:%s',$this->month,$this->day,$this->hour,$this->minute));
            return false;
        }
        
        $timing_process = new TimingProcessPeer ();
        $timing_process->updateParameters ( $next_timestamp, TimingProcessPeer::STATUS_COMMAND );
        return $timing_process;
    }
    private function getNextTriggerTimeStamp()
    {
        $now = time ();
        $year = (int)date ( 'Y' ) - 1;
        $date_string = '';
        $today = 0;
        
	    $dday = $this->day;

	    $CI = & get_instance();
	    $CI->load->helper('date');
	    
        $flag = false;
        for($limit = 0;$limit < 10;$limit ++)
        {
            $year++;
    	    if($this->day == -1)
    	    {
    	        $dday = days_in_month($this->month,$year);
    	    }
    	    
            $date_string = sprintf ( '%04d-%02d-%02d %02d:%02d:00', $year, $this->month, $dday, $this->hour, $this->minute );
            $today = strtotime ( $date_string );
            if ($now < $today)
            {
                if($this->timestampToDatetimeString($today) == $date_string)
                {
                    $flag = true;
                    break;
                }
            }
        }
        
        if($flag === true)
        {
            return $today;
        }
        return false;
    }
}
