<?php
/**
 * report_email 处理模型<br />
 * 用于记录要发送的报告邮件。<br />
 * @author zgldh
 *
 */
class Report_email_model extends MY_Model
{
    const TABLE = 'report_email';
    public function getByPK($report_id)
    {
        if ($this->cache_pk->hasData ( $report_id ))
        {
            return $this->cache_pk->getData ( $report_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (ReportEmailPeer::PK => $report_id ) )->row_array ();
        $process = $raw ? new ReportEmailPeer ( $raw ) : false;
        
        $this->cache_pk->setData ( $report_id, $process );
        
        return $process;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param ReportEmailPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    
    /**
     * 得到一批ReportEmailPeer
     * 
     * @param int $user_id
     *            = null 限定某用户
     * @param
     *            int type $task_id = null 限定某任务
     * @param DB_Cache $limitObj
     *            = null
     * @param boolean $sent
     *            = null true只取出发送过的, false只取出发送过的, null忽略
     * @return multitype:ReportEmailPeer
     */
    public function getAll($user_id = null, $task_id = null, $limitObj = null, $sent = null)
    {
        $re = array ();
        if ($user_id !== null)
        {
            $this->db->where ( "user_id", $user_id );
        }
        if ($task_id !== null)
        {
            $this->db->where ( "task_id", $task_id );
        }
        if ($limitObj)
        {
            $limitObj->setLimit ( $this->db );
        }
        if ($sent === true)
        {
            $this->db->where ( "sent", 1 );
        }
        elseif ($sent === false)
        {
            $this->db->where ( 'sent', 0 );
        }
        
        $rows = $this->db->get_where ( self::TABLE );
        foreach ( $rows as $row )
        {
            $re [] = new ReportEmailPeer ( $row );
        }
        return $re;
    }
}
class ReportEmailPeer extends BasePeer
{
    const PK = 'report_id';
    
    /**
     * 报告邮件Id
     * 
     * @var int
     */
    public $report_id = 0;
    /**
     * 要发给的用户id
     * 
     * @var int
     */
    public $user_id = 0;
    /**
     * 产生报告的任务id
     * 
     * @var int
     */
    public $task_id = 0;
    /**
     * 报告的章节(serialized array)
     * 
     * @var string
     */
    public $sections = null;
    /**
     * 报告的附件(serialize array)
     * 
     * @var string
     */
    public $attachment = null;
    /**
     * 生成时间
     * 
     * @var string
     */
    public $gen_datetime = '';
    /**
     * 发送时间
     * 
     * @var string
     */
    public $sent_datetime = null;
    /**
     * 邮件是否发送
     * 0: not sent; 1: sent
     * 
     * @var int
     */
    public $sent = 0;
    function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrimaryKeyName()
    {
        return self::PK;
    }
    public function getPrimaryKeyValue()
    {
        return $this->report_id;
    }
    public function save()
    {
        ReportEmailPeer::model ()->save ( $this );
    }
    /**
     *
     * @return Report_email_model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->report_email_model;
    }
    /**
     * 得到生成本报告的任务
     * @return TaskPeer
     */
    public function getTask()
    {
        $CI = & get_instance ();
        $CI->load->model('Task_model','task_model',true);
        
        $task = TaskPeer::model()->getByPK($this->task_id);
        return $task;
    }
    /**
     * 得到要发送给的用户
     * @return UserPeer
     */
    public function getUser()
    {
        $CI = & get_instance ();
        $CI->load->model('User_model','user_model',true);
        
        $user = UserPeer::model()->getByPK($this->user_id);
        return $user;
    }
    
    /**
     * 创建一个新的ReportEmailPeer
     * 
     * @param int $user_id
     *            该报告要发给哪个用户
     * @param TaskPeer $task
     *            生成该报告的task
     * @param string $sections
     *            序列化(serilized)的报告章节
     * @param string $attachment
     *            序列化(serilized)的附件
     * @return ReportEmailPeer
     */
    public static function create($user_id, $task, $sections = null, $attachment = null)
    {
        $report = new ReportEmailPeer ();
        $report->user_id = $user_id;
        $report->task_id = $task->task_id;
        $report->sections = $sections;
        $report->attachment = $attachment;
        $report->gen_datetime = self::getTimeStamp ();
        return $report;
    }
    
    /**
     * 将本报告邮件发送出去
     */
    public function send()
    {
        $CI = & get_instance ();
        $user = $this->getUser();
        $task = $this->getTask();

        $data = array('task'=>$task,'report'=>$this);
        $content = $CI->load->view('email_report/report',$data,true);
        $debug_content = null;
        
        if (isLiveServer ())
        {
            $CI->load->library ( 'email' );
        
            $CI->email->from ( SITE_EMAIL, 'TaskList' );
            $CI->email->to ( $user->email);
            $CI->email->subject ( sprintf ( "%s 的任务执行报告", $task->getName()));
            foreach($this->prepareTempAttachment() as $attachment_path)
            {
                $CI->email->attach($attachment_path);
            }
            $CI->email->message ( $content);
            $result = $CI->email->send ();
            $debug_content = $CI->email->print_debugger();
        }
        
        $log_file = fopen ( LOG_PATH . '/send_email_log.txt', 'a+' );
        if($result == true)
        {
            fwrite ( $log_file, sprintf ( "%s ReportEmailPeer::send() TaskID=%d, 向%s(%s)发送报告邮件。 成功\n", $this->getTimeStamp(), $task->task_id, $user->name,$user->email));
        }
        else
        {
            fwrite ( $log_file, sprintf ( "%s ReportEmailPeer::send() TaskID=%d, 向%s(%s)发送报告邮件。 失败\n%s", $this->getTimeStamp(), $task->task_id, $user->name,$user->email,$debug_content));
        }
        fclose ( $log_file );
        
        $this->cleanTempAttachment();
        $this->sent = 1;
        $this->sent_datetime = $this->getTimeStamp();
        $this->save();
         
        return $result;
    }
    
    /**
     * 准备附件临时文件
     * @return multitype:string
     */
    private function prepareTempAttachment()
    {
        $dir = LOG_PATH . '/temp_attachment/';
        $re = array();
        
        $attachments = $this->getAttachment();
        foreach($attachments as $file_name=>$file_content)
        {
            $file_path = $dir.$file_name;
            file_put_contents($file_path, $file_content);
            $re[] = $file_path;
        }
        return $re;
    }
    /**
     * 清空附件临时文件
     */
    private function cleanTempAttachment()
    {
        $dir = LOG_PATH . '/temp_attachment/';
        
        $files = scandir('aaa');
        foreach($files as $file)
        {
            $p = $dir.$file;
            if($file != 'index.html' && is_file($p))
            {
                unlink($p);
            }
        }
    }
    
    /**
     * 得到报告章节数组
     * @param boolean $unserilize=true 是否自动unserilize
     * @return multitype:|mixed|string
     */
    public function getSections($unserilize = true)
    {
        if(!$this->sections)
        {
            return array();
        }
        if($unserilize)
        {
            return unserialize($this->sections);
        }
        else
        {
            return $this->sections;
        }
    }
    /**
     * 得到报告附件数组
     * @param boolean $unserilize=true 是否自动unserilize
     * @return multitype:|mixed|string
     */
    public function getAttachment($unserilize = true)
    {
        if(!$this->attachment)
        {
            return array();
        }
        if($unserilize)
        {
            return unserialize($this->attachment);
        }
        else
        {
            return $this->attachment;
        }
    }
}

?>