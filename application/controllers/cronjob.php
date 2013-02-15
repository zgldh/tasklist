<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
class Cronjob extends MY_Controller
{
    public function index()
    {
        echo 'hi cronjob!';
    }
    private $_start_time_seconds = 0;
    private function timeStart()
    {
        $this->_start_time_seconds = time ();
    }
    private function timeCurrent()
    {
        $current = time ();
        return $current - $this->_start_time_seconds;
    }
    private function isOvertime($max_seconds)
    {
        if ($this->timeCurrent () > $max_seconds)
        {
            return true;
        }
        return false;
    }
    
    /**
     * 获取外部数据 5分钟一次
     * 
     * @deprecated
     *
     */
    public function data_fetch()
    {
        $this->needCliOrExit ();
        
        $this->timeStart ();
        $max_seconds = 290;
        $max_reports = 100;
        
        $this->loadKitcoGoldModel ();
        $models = array ($this->kitco_gold_model );
        
        foreach ( $models as $model )
        {
            if ($this->isOvertime ( $max_seconds ))
            {
                break;
            }
            $model->fetch ();
        }
    }
    /**
     * 轮询 report_email 表， 发送报告邮件
     * 
     * @deprecated
     *
     */
    public function send_report_email()
    {
        $this->needCliOrExit ();
        
        $this->timeStart ();
        $max_seconds = 290;
        $max_reports = 100;
        
        $this->loadReportEmailModel ();
        $reports_limit = new DB_Limit ( $max_reports );
        $reports = $this->report_email_model->getAll ( null, null, $reports_limit, false );
        $this->load->library ( 'email' );
        
        foreach ( $reports as $report )
        {
            if ($this->isOvertime ( $max_seconds ))
            {
                break;
            }
            
            $report instanceof ReportEmailPeer;
            $report->send ();
        }
    }
    
    /**
     * 轮询 timing_process表， 判断并执行 task
     */
    public function timing_process()
    {
        // $this->needCliOrExit ();
        set_time_limit(60);
        ob_start();
        printf ( "Cronjob::timing_process start at %s;\n", date ( 'Y-m-d H:i:s' ) );
        
        $this->timeStart ();
        $max_seconds = 25;
        
        $this->loadProcessLogModel ();
        $this->loadTimingProcessModel ();
        $limit = new DB_Limit ( 100 );
        
        $count = 0;
        
        printf("max_execution_time: %s;\n",ini_get('max_execution_time'));
        
        while ( 1 )
        {
            if ($this->isOvertime ( $max_seconds ))
            {
                printf("Is Overtime: %s;\n", date ( 'Y-m-d H:i:s' ));
                break;
            }
            
            $timings = $this->timing_process_model->getRunnableBefore ( null, $limit );
            var_dump($this->db->last_query());
            if (count ( $timings ) == 0)
            {
                printf("Sleep 1\n");
                sleep ( 1 );
                continue;
            }
            
            foreach ( $timings as $timing )
            {
                $count ++;
                $timing instanceof TimingProcessPeer;
                if ($timing->isStatusTrigger ())
                {
                    $timing->runTrigger ();
                }
                elseif ($timing->isStatusCommand ())
                {
                    $timing->runCommand ();
                    $timing->next ();
                }
            }
        }
        
        printf ( "Cronjob::timing_process count %d;\n", $count );
        printf ( "Cronjob::timing_process end at %s;\n", date ( 'Y-m-d H:i:s' ) );
        ob_end_flush();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */