<?php
/**
 * 发送电子邮件 命令
 * @author zgldh
 *
 */
class SendEmailCommandPeer extends CommandPeer
{
    /**
     * 最多地址数
     * 
     * @var int
     */
    const MAX_ADDRESS_NUM = 5;
    const REG_EMAIL = '/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/';
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
        $content = trim ( $content,' ;' );
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
     * @param string $default 默认收件人字符串
     * @return string 'zgldh@hotmail.com;abc@email.com'
     */
    public function getRecipientsString($default)
    {
        if(is_string($this->recipients))
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
}

