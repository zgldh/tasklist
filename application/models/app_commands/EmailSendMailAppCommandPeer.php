<?php
class EmailSendMailAppCommandPeer extends AppCommandPeer
{
    use app_parameter;
    
    private $email_title = '';
    private $email_content = '';
    
    public function getPrivateParameters()
    {
        return array ('email_title', 'email_content' );
    }
    
    public function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see AppCommandPeer::getDetailHTML()
     */
    public function getDetailHTML()
    {
        $html = $this->commandView ( 'EmailSendMailAppCommandPeer', array ('command' => $this ), true );
        return $html;
    }
    /**
     * (non-PHPdoc)
     * 
     * @see AppCommandPeer::getFullDescription()
     */
    public function getFullDescription($parameters = null)
    {
        $this->praseParameters ( $parameters );
        
        return $re;
    }
    
    /*
     * (non-PHPdoc) @see AppCommandPeer::execute()
     */
    public function execute($data = null)
    {
        if($data)
        {
            $this->praseParameters($data);
        }
        // TODO EmailSendMailAppCommandPeer::execute
    }
}
