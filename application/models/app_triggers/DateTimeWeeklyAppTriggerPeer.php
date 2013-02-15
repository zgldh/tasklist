<?php
class DateTimeWeeklyAppTriggerPeer extends AppTriggerPeer
{
    use app_parameter;
    /**
     * array(1=>true,2=>true,4=>true,7=>true);
     *
     * @var array
     */
    private $week_day = array ();
    private $hour = 1;
    private $minute = 0;
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrivateParameters()
    {
        return array ('week_day', 'hour', 'minute' );
    }
    /**
     * (non-PHPdoc)
     *
     * @see AppTriggerPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->triggerView ( 'DateTimeWeeklyAppTriggerPeer', array ('trigger' => $this ), true );
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
        
        $temp_week_day = $this->week_day;
        $week_string = join ( ',', $temp_week_day );
        $string = sprintf ( "每周%s 的%d点%d分", $week_string, $this->hour, $this->minute );
        $re = $this->getFullDescriptionArray ( $string );
        return $re;
    }
    
    /*
     * (non-PHPdoc) @see AppTriggerPeer::setNextTimingProcess()
     */
    public function setNextTimingProcess($timing_process)
    {
        $next_timestamp = $this->getNextTriggerTimeStamp ();
        if (! $this->week_day || $next_timestamp === false)
        {
            throw new Exception ( 'DateTimeWeeklyAppTriggerPeer::setNextTimingProcess week_day必须有值' );
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
        if (! $this->week_day || $next_timestamp === false)
        {
            throw new Exception ( 'DateTimeWeeklyAppTriggerPeer::generateTimingProcess week_day必须有值' );
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
        $today = strtotime ( date ( sprintf ( 'Y-m-d %02d:%02d:00', $this->hour, $this->minute ) ) );
        $now = time ();
        $flag = false;
        for($limit = 0; $limit < 7; $limit ++)
        {
            if ($now >= $today)
            {
                // nothing todo
            }
            else
            {
                $info = getdate ( $today );
                $wday = $info ['wday'] + 1;
                if (isset ( $this->week_day [$wday] ) && $this->week_day [$wday] === true)
                {
                    // that's the time
                    $flag = true;
                    break;
                }
                else
                {
                    // nothing todo
                }
            }
            $today = $today + 86400;
        }
        
        if($flag === true)
        {
            return $today;
        }
        else
        {
            return false;
        }
    }
    
}
