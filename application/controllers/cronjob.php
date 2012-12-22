<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cronjob extends MY_Controller
{
	public function index()
	{
	    echo 'hi cronjob!';
	}
	
	private $_start_time_seconds = 0;
	private function timeStart()
	{
	    $this->_start_time_seconds = time();
	}
	private function timeCurrent()
	{
	    $current = time();
	    return $current - $this->_start_time_seconds;
	}
	private function isOvertime($max_seconds)
	{
	    if($this->timeCurrent() > $max_seconds)
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 轮询 report_email 表， 发送报告邮件
	 */
	public function send_report_email()
	{
	    $this->needCliOrExit();

	    $this->timeStart();
	    $max_seconds = 290;
	    $max_reports = 100;
	     
	    $this->loadReportEmailModel();
	    $reports_limit = new DB_Limit($max_reports);
	    $reports = $this->report_email_model->getAll(null,null,$reports_limit,false);
	     
	    foreach($reports as $report)
	    {
	        if($this->isOvertime($max_seconds))
	        {
	            break;
	        }
	        
	        $report instanceof ReportEmailPeer;
	        $report->send();
	    }
	}
	
	/**
	 * 轮询  timing_process表， 判断并执行 task
	 */
	public function timing_process()
	{
	    $this->needCliOrExit();
	    
	    $this->timeStart();
	    $max_seconds = 290;
	    
	    $this->loadProcessLogModel();
	    $this->loadTimingProcessModel();
	    $current_hour = date('Y-m-d H:00:00');
	    $timings = $this->timing_process_model->getInOneHour($current_hour,null,false);
	    
	    foreach($timings as $timing)
	    {
	        if($this->isOvertime($max_seconds))
	        {
	            break;
	        }
	        $timing instanceof TimingProcessPeer;
	        $timing->setExecuted();
	        
	        $task = $timing->getTask();
	        if($task->isOverExecuted())
	        {
	            $task->deleteUnexecutedProcesses();
                //任务task 已经达到执行上限。 记录个日志吧。
	            ProcessLogPeer::log($task->task_id, sprintf("本任务已达到执行上限 times:%d; limit:%d",$task->times,$task->limit));
	            continue;
	        }
	        
	        /** ------------------------------------------------------------------------- **/ 
	        
	        $conditions = $task->getConditions();
	        $condition_ok = true;
	        
	        foreach($conditions as $condition)
	        {
	            $condition instanceof ConditionPeer;
	            if($condition->type == ConditionPeer::TYPE_DATE_STATIC)
	            {
	                continue;
	            }
	            else
	            {
	                if(!$condition->check())
	                {
	                    $condition_ok = false;
	                    break;
	                }
	            }
	        }
	        
	        if(!$condition_ok)
	        {
	            //本任务 条件失败, 记录日志
	            ProcessLogPeer::log($task->task_id, sprintf("条件失败。 条件ID: %d; 条件类型: %s",$condition->condition_id,$condition->type));
	            continue;
	        }
	        
	        /** ------------------------------------------------------------------------- **/
	        
	        //本任务条件全部通过。 开始执行
	        $commands = $task->getCommands();
	        $commands_error = array();
	        foreach($commands as $command)
	        {
	            $command instanceof CommandPeer;
	            $command_ok = true;
	            if($command->execute())
	            {
	                //命令执行成功
	            }
	            else
	            {
	                //命令执行失败
	                $commands_error[] = sprintf("命令Id: %d; 命令类型: %s",$command->command_id,$command->type);
	                $command_ok = false;
	            }
	        }

	        if($command_ok)
	        {
	            //命令全部OK 记录日志
	            ProcessLogPeer::log($task->task_id, sprintf("全部命令执行成功。"));
	        }
	        else
	        {
	            //有失败的命令 记录日志
	            ProcessLogPeer::log($task->task_id, sprintf("部分命令执行失败： %s",join(', ', $commands_error)));
	        }
	        
	        /** ------------------------------------------------------------------------- **/
	        
	        //任务执行次数+1
	        $task->times++;
	        $task->save();
	        
	        //保存报告邮件， 待发送
	        $this->saveReportEmails($task, $commands);
	    }
	}
	
	/**
	 * 生成并保存报告邮件。 但不发送
	 * 需要预先 $this->loadCommandModel()
	 * @param TaskPeer $task
	 * @param Array<CommandPeer> $commands
	 */
	private function saveReportEmails($task,$commands)
	{
	    list($sections,$attachements) = CommandPeer::getReportComponents($commands);
	    $sections = serialize($sections);
	    $attachements = serialize($attachements);
	    
	    $user = $task->getUser();
	    
	    $this->loadReportEmailModel();
	    $report_email = ReportEmailPeer::create($user->user_id,$task,$sections,$attachements);
	    $report_email->save();
	}
	
	
	public function testReport($report_id)
	{
	    return false;
	    $this->loadReportEmailModel();
	    $report = $this->report_email_model->getByPK($report_id);
	    
	    $data = array('report'=>$report);
	    $this->load->view('email_report/report',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */