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
		$user = $this->webuser->getUser();
		$weibo_link = $user->getWeiboLink();
		
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
				$token = Weibo_link_model::$sae_oauth->getAccessToken('code', $keys );
			}
			catch ( OAuthException $e )
			{
			}
		}
		
		if ($token)
		{
			// 授权成功。
			$this->webuser->setSessData('token', $token);
			setcookie ( 'weibojs_' . Weibo_link_model::$sae_oauth->client_id, http_build_query ( $token ) );
			
	        $weibo_link = WeiboLinkPeer::createAndSave($this->webuser->getUserId(), 
	                $token['uid'], 
	                $token['access_token'],
	                $token['expires_in'],
	                $token['remind_in']);
	        
	        $this->load->helper('url');
	        redirect('/user/hub?weibo_success=1');
	        exit();
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