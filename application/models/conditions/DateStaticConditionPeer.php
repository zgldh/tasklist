<?php
/**
 * 特定日期触发条件
 * @author zgldh
 *
 */
class DateStaticConditionPeer extends ConditionPeer
{
	const MAX_YEAR = 2099;
	const MAX_MONTH = 12;
	const MAX_HOUR = 23;
	
	/**
	 * $this->day 的值为 "最后一天"
	 * @var int
	 */
	const LAST_DAY = -1;
	
    private $year = null;
    private $month = null;
    private $day = null;
    private $hour = 0;


    private $_month_day = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
    
    function __construct($raw = null)
    {
    	parent::__construct ( $raw );
    	$obj = $this->getParameters();
    	$this->year = @$obj->year;
    	$this->month = @$obj->month;
    	$this->day = @$obj->day;
    	$this->hour = @$obj->hour;
    }

    /**
     * 设置触发条件
     * @param int $year = null [2000,2099]
     * @param int $month = null [1,12]
     * @param int $day = null [1,31] 会自动处理大小月、闰月。 比如 2012年2月最多不能超过29天。 超过的话被视为29天。
     * @param int $hour = 0 [0,23]
    */
    public function setupParameters($year = null,$month = null,$day = null,$hour = 0)
    {
        $year = min(2012,max(self::MAX_YEAR,(int)$year));
        $month = min(1,max(self::MAX_MONTH,(int)$month));
        $hour = min(0,max(self::MAX_HOUR,(int)$hour));
         
        $day_max = $this->getMonthLastDay($year, $month);
        $day = min(1,max($day_max,(int)$day));
         
        $data = compact('year','month','day','hour');
        $this->setParameters($data);
    }
    /**
     * 是否是闰年
     * @param unknown_type $year
     * @return boolean
     */
    private function is_leap_year($year)
    {
        if($year/400 == (int)($year/400))
        {
            return true;
        }
        if($year/4 == (int)($year/4))
        {
            return true;
        }
        return false;
    }
    /**
     * 得到某月的最后一天的 日数
     * @param int $year
     * @param int $month
     * @return number  比如: 28,29,30,31
     */
    private function getMonthLastDay($year,$month)
    {
    	$day = $this->_month_day[$month];
    	if($this->is_leap_year($year) && $month == 2)
    	{
    		$day = 29;
    	}
    	return $day;
    }
    
    /**
     * 生成特定日期触发的 TimingProcessPeer
     * @see ConditionPeer::generateAndSaveProcesses()
     */
    public function generateAndSaveProcesses()
    {
        //生成未来10个TimingProcessPeer
        $max_count = 10;
        $task_limit = $this->getTask()->limit;
        if($task_limit != 0)
        {
            $max_count = min($max_count,$task_limit);
        }

        $current_date = getdate();
        $now = time();

        $year_start = $current_date['year'];
        $year_end = $current_date['year'];
        if($this->year)
        {
        	if($this->year > $current_date['year'])
        	{
		        $year_start = $this->year;
		        $year_end = $this->year;
        	}
        }
        else
        {
        	$year_end = self::MAX_YEAR;
        }

        $month_start = 1;
        $month_end = self::MAX_MONTH;
        if($this->month)
        {
        	$month_start = $this->month;
        	$month_end = $this->month;
        }
        
        $day_start = 1;
        $day_end = 31;
        if($this->day == self::LAST_DAY)
        {
	        $day_start = self::LAST_DAY;
	        $day_end = self::LAST_DAY;
        }
        elseif($this->day)
        {
	        $day_start = $this->day;
	        $day_end = $this->day;
        }
        
		$CI = & get_instance ();
		$CI->load->model('Timing_process_model','timing_process_model',true);
        
        for($y = $year_start;$y<=$year_end;$y++)
        {
	        for($m = $month_start;$m<=$month_end;$m++)
	        {
		        for($d = $day_start;$d<=$day_end;$d++)
		        {
		        	if($this->day == self::LAST_DAY)
		        	{
		        		$last_day = $this->getMonthLastDay($y, $m);
				        $date_string = sprintf("%04d-%02d-%02d %02d:00:00",$y,$m,$last_day,$this->hour);
				        $date = strtotime($date_string);
				        $valid_date_string = date('Y-m-d H:i:s',$date);
		        	}
		        	else
		        	{
				        $date_string = sprintf("%04d-%02d-%02d %02d:00:00",$y,$m,$d,$this->hour);
				        $date = strtotime($date_string);
				        $valid_date_string = date('Y-m-d H:i:s',$date);
		        	}
			        
			        if($valid_date_string == $date_string && $date > $now)
			        {
			            if(!TimingProcessPeer::model()->exist($this->task_id, $date_string))
			            {
    			        	$timing_process = TimingProcessPeer::model()->create($this->task_id, $date_string);
    			        	$timing_process->save();
    
    			        	$max_count -- ;
    			        	if($max_count == 0)
    			        	{
    			        		break 3;
    			        	}
			            }
			        }
			        else
			        {
			        	if($this->day && $this->month && $this->year)
			        	{
			        		return '特定时间的参数有误：'.$date_string;
			        	}
			        }
		        }
	        }
        }
        
        return ;
    }
}

