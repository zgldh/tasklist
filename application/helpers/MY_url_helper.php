<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_url'))
{
	function get_url( $url,  $javascript_loop = 0, $timeout = 5 )
	{
		$url = str_replace( "&amp;", "&", urldecode(trim($url)) );
		$user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1";
	
		$cookie = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HEADER, true);
		curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
		curl_setopt( $ch, CURLOPT_FILETIME, true);
	
		$content = curl_exec( $ch );
		$response = curl_getinfo( $ch );
		curl_close ( $ch );
	
		$rep_header = substr($content,0,$response['header_size']);
		$content = substr($content,$response['header_size']);
		$response['response_header'] = $rep_header;
	
		if ($response['http_code'] == 301 || $response['http_code'] == 302)
		{
			foreach( explode("\n",$rep_header) as $line )
			{
				if ( substr( strtolower($line), 0, 9 ) == "location:" )
				{
					return get_url( trim( substr( $line, 9, strlen($line) ) ) );
				}
			}
		}
	
		if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) ||
				preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) &&
				$javascript_loop < 5
			)
		{
			return get_url( $value[1], $javascript_loop+1 );
		}
		else
		{
			return array($content, $response );
		}
	}
	
}
