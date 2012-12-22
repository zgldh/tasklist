<?php
/**
 * 访问URL 命令
 * @author zgldh
 *
 */
class UrlRequestCommandPeer extends CommandPeer
{
	const REG_URL = '/^([a-z]+:\/\/)?([a-z]([a-z0-9\-]*\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(:[0-9]{1,5})?(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&amp;]*)?)?(#[a-z][a-z0-9_]*)?$/';
	private $url = null;
	function __construct($raw = null)
	{
		parent::__construct ( $raw );
		$obj = $this->getParameters ();
		$this->url = @$obj->url;
	}
	
	/**
	 * 设置参数
	 * 
	 * @param string $url        	
	 * @return boolean true成功， false URL非法
	 */
	public function setupParameters($url)
	{
		$url = trim ( $url );
		
		if (preg_match ( self::REG_URL, $url ) == 0)
		{
			return false;
		}
		$this->url = $url;
		
		$data = compact ( 'url' );
		parent::setParameters ( $data );
		return true;
	}
	
	/**
	 * 重载 设置参数
	 *
	 * @param array $url
	 *        	http://xxx.xxx.com
	 * @see ConditionPeer::setParameters()
	 */
	public function setParameters($data)
	{
		if (is_array ( $data ))
		{
			if (isset ( $data ['url'] ))
			{
				if ($this->setupParameters ( $data ['url'] ))
				{
					return null;
				}
				return '访问URL 错误的URL: ' . $data ['url'];
			}
		}
		return '访问URL 参数错误';
	}
	
	/**
	 * 执行该命令， 访问URL(non-PHPdoc)
	 * 
	 * @see CommandPeer::execute()
	 */
	public function execute()
	{
		$CI = & get_instance ();
		$CI->load->helper ( 'url' );
		
		$result = get_url ( $this->url );
		
		$this->report_attachment = $this->makeReportAttachment ( $result );
		$data = array ('url' => $this->url, 'result' => $result, 'attachment' => $this->report_attachment );
		$this->report_section = $CI->load->view ( 'email_report/commands/url_request', $data, true );
		
		return true;
	}
	private function makeReportAttachment($result)
	{
		if ($result [0] === false)
		{
			return array ();
		}
		else
		{
			$file_name = sprintf ( "TASK_%d_URL_REQUEST_%s.html", $this->task_id, $this->getTimeStamp (true,'YmdHis') );
			$file_content = $result [0];
			return array ($file_name => $file_content );
		}
	}
}

