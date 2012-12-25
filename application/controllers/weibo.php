<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Weibo extends MY_Controller
{
	public function index()
	{
		header ( 'Location: /' );
		exit ();
	}
	
	/**
	 * TODO weibo callback
	 */
	public function callback()
	{
		$this->needLoginOrExit ();
		
		$this->loadSaeTAuthV2 ();
		if ($this->inputGet ( 'code' ))
		{
			$keys = array ();
			
			// 验证state，防止伪造请求跨站攻击
			$state = $this->inputGet ( 'state' );
			if (empty ( $state ) || $state !== $this->webuser->getSessData ( 'weibo_state' ))
			{
				echo '非法请求！';
				exit ();
			}
			$this->webuser->unsetSessData ( 'weibo_state' );
			
			$keys ['code'] = $this->inputGet ( 'code' );
			$keys ['redirect_uri'] = WB_CALLBACK_URL;
			try
			{
				$token = $this->sae_oauth->getAccessToken('code', $keys );
			}
			catch ( OAuthException $e )
			{
			}
		}
		
		if ($token)
		{
			// 授权成功。
			$this->webuser->setSessData('token', $token);
			setcookie ( 'weibojs_' . $o->client_id, http_build_query ( $token ) );
			
	        $this->loadWeiboLinkModel();
	        $weibo_link = new WeiboLinkPeer();
	        $weibo_link->user_id = $this->webuser->getUserId();
	        $weibo_link->token = $token;
	        $weibo_link->update_datetime = WeiboLinkPeer::getTimeStamp();
	        $weibo_link->save();
	        
	        $this->load->helper('url');
	        redirect('/user/hub');
		}
		else
		{
			// 授权失败。
	        $this->load->helper('url');
	        redirect('/user/hub?weibo_error=1');
	        exit();
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/weibo.php */