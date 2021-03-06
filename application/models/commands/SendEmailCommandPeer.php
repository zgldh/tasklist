<?php
/**
 * 发送电子邮件 命令
 * @author zgldh
 *
 */
class SendEmailCommandPeer extends TaskCommandPeer
{
    /**
     * 最多地址数
     *
     * @var int
     */
    const MAX_ADDRESS_NUM = 5;
    const REG_EMAIL = '/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/';
    /**
     * 收件人数组
     * 
     * @var array array('zgldh@hotmail.com','abc@qq.com')
     */
    private $recipients = array ();
    private $content = null;
    function __construct($raw = null)
    {
        parent::__construct ( $raw );
        $obj = $this->getParameters ();
        $this->recipients = @$obj->recipients;
        $this->content = @$obj->content;
    }
    
    /**
     * 设置参数
     *
     * @param array $recipients
     *            收件人email array('john@email.com', 'mike@email.com')
     * @param string $content
     *            邮件内容
     * @return boolean
     */
    public function setupParameters($recipients, $content)
    {
        $content = trim ( $content);
        $recipients = str_replace ( '；', ';', $recipients );
        $valid_recipients = array ();
        $count = 0;
        foreach ( explode ( ';', $recipients ) as $recipient )
        {
            $recipient = trim ( $recipient );
            if ($this->isValidEmail ( $recipient ))
            {
                $valid_recipients [] = $recipient;
                $count ++;
                if ($count >= self::MAX_ADDRESS_NUM)
                {
                    break;
                }
            }
        }
        
        // 抽取图片，并且转存
        $content = $this->purifyContent ( $content );
        
        $this->recipients = $valid_recipients;
        $this->content = $content;
        
        $data = array ('recipients' => $this->recipients, 'content' => $this->content );
        parent::setParameters ( $data );
        return true;
    }
    /**
     * 重载 设置参数
     *
     * @see CommandPeer::setParameters()
     */
    public function setParameters($data)
    {
        if (is_array ( $data ))
        {
            if (isset ( $data ['recipients'] ) && isset ( $data ['content'] ))
            {
                if ($this->setupParameters ( $data ['recipients'], $data ['content'] ))
                {
                    return null;
                }
                return '发送电子邮件 错误的参数: ' . print_r ( $data, true );
            }
        }
        return '发送电子邮件 参数错误';
    }
    private function isValidEmail($email)
    {
        if (preg_match ( self::REG_EMAIL, $email ) == 0)
        {
            return false;
        }
        return true;
    }
    /**
     * 得到收件人的email字符串
     * 
     * @param string $default
     *            默认收件人字符串
     * @return string 'zgldh@hotmail.com;abc@email.com'
     */
    public function getRecipientsString($default)
    {
        if (is_string ( $this->recipients ))
        {
            $str = $this->recipients;
        }
        elseif (count ( $this->recipients ))
        {
            $str = join ( ';', $this->recipients );
        }
        else
        {
            $str = $default;
        }
        return $str;
    }
    const REG_BASE64_JPG = '/data:image\/(jpeg);base64,(.*?)"/';
    const REG_BASE64_GIF = '/data:image\/(gif);base64,(.*?)"/';
    const REG_BASE64_PNG = '/data:image\/(png);base64,(.*?)"/';
    
    /**
     * 转存所有的图片
     * 
     * @param string $content            
     * @return string
     */
    private function purifyContent($content)
    {
        $content = preg_replace_callback ( self::REG_BASE64_JPG, array ($this, 'purifyContentCallback' ), $content );
        $content = preg_replace_callback ( self::REG_BASE64_GIF, array ($this, 'purifyContentCallback' ), $content );
        $content = preg_replace_callback ( self::REG_BASE64_PNG, array ($this, 'purifyContentCallback' ), $content );
        
        return $content;
    }
    private function purifyContentCallback($args)
    {
        $type = $args [1];
        $str = $args [2];
        
        $content = base64_decode ( $str );
        
        if ($type == 'jpeg')
        {
            $file_name = md5 ( $str ) . '.jpg';
        }
        elseif ($type == 'gif')
        {
            $file_name = md5 ( $str ) . '.gif';
        }
        elseif ($type == 'png')
        {
            $file_name = md5 ( $str ) . '.png';
        }
        else
        {
            return null;
        }
        
        $date_folder = date ( 'Y-m-d' );
        $dir = UPLOADS . '/' . $date_folder;
        if (! is_dir ( $dir ))
        {
            mkdir ( $dir );
        }
        
        $file_path = $dir . '/' . $file_name;
        file_put_contents ( $file_path, $content );
        
        $url = BASEURL . 'uploads/' . $date_folder . '/' . $file_name;
        
        return $url . '"';
    }
    
    /**
     * 执行该命令， 发送email(non-PHPdoc)
     * 
     * @see CommandPeer::execute()
     */
    public function execute()
    {
        $CI = & get_instance ();
        if (isLiveServer ())
        {
            $CI->load->library ( 'email' );
            
            $CI->email->from ( SITE_EMAIL, 'TaskList' );
            $CI->email->to ( $this->recipients );
            $CI->email->subject ( sprintf ( "您有来自%s的消息", $this->getTask ()->getUser ()->name ) );
            $CI->email->message ( $this->content );
            $result = $CI->email->send ();
            $debug_content = $CI->email->print_debugger();
            $this->report_attachment = $this->makeReportAttachment($debug_content);
        }
        else
        {
            $task = $this->getTask ();
            $log_file = fopen ( LOG_PATH . '/send_email_log.txt', 'a+' );
            fwrite ( $log_file, sprintf ( "%s SendEmailCommandPeer::execute() TaskID=%d, %s向%s发送邮件\n", $this->getTimeStamp(), $task->task_id, $task->getUser ()->name, join ( ',', $this->recipients ) ) );
            $result = fclose ( $log_file );
        }

        $data = array ('recipients' => $this->getRecipientsString(null), 'result' => $result,'attachment' => $this->report_attachment);
        $this->report_section = $CI->load->view ( 'email_report/commands/send_email', $data, true );
        
        return $result;
    }
    
    private function makeReportAttachment($debug_content)
    {
        $file_name = sprintf ( "TASK_%d_SEND_EMAIL_%s.html", $this->task_id, $this->getTimeStamp (true,'YmdHis') );
        $file_content = $debug_content;
        return array ($file_name => $file_content );
    }
}

