<?php 
/**
 * $url;
 * $result;
 * $attachment;
 */
?>
<div>
	<h3>访问URL</h3>
	<?php if($result[0] === false):?>
	<p><?php echo $url; ?>访问失败.</p>
	<?php else:?>
	<p><?php echo $url; ?>访问成功!</p>
	<p>获取到的HTML在附件中。<?php echo key($attachment);?></p>
	<?php endif;?>

	<?php $info = $result[1];?>
	<table>
		<thead>
			<tr>
				<th>项目名</th>
				<th>值</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>URL地址</td>
				<td><?php echo $info['url'];?></td>
			</tr>
			<tr>
				<td>Content Type</td>
				<td><?php echo $info['content_type'];?></td>
			</tr>
			<tr>
				<td>HTTP Code</td>
				<td><?php echo $info['http_code'];?></td>
			</tr>
			<tr>
				<td>最后修改时间</td>
				<td><?php echo date('Y-m-d H:i:s',$info['filetime']);?></td>
			</tr>
			<tr>
				<td>是否SSL</td>
				<td><?php echo $info['ssl_verify_result']?'Yes':'No';?></td>
			</tr>
			<tr>
				<td>重定向次数</td>
				<td><?php echo $info['redirect_count'];?> 秒</td>
			</tr>
			<tr>
				<td>传输总用时</td>
				<td><?php echo $info['total_time'];?> 秒</td>
			</tr>
			<tr>
				<td>域名解析用时</td>
				<td><?php echo $info['namelookup_time'];?> 秒</td>
			</tr>
			<tr>
				<td>建立连接用时</td>
				<td><?php echo $info['connect_time'];?> 秒</td>
			</tr>
			<tr>
				<td>准备传输用时</td>
				<td><?php echo $info['pretransfer_time'];?> 秒</td>
			</tr>
			<tr>
				<td>开始传输以后用时</td>
				<td><?php echo $info['starttransfer_time'];?> 秒</td>
			</tr>
			<tr>
				<td>重定向用时</td>
				<td><?php echo $info['redirect_time'];?> 秒</td>
			</tr>
			<tr>
				<td>上传数据量</td>
				<td><?php echo $info['size_upload'];?> byte</td>
			</tr>
			<tr>
				<td>下载数据量</td>
				<td><?php echo $info['size_download'];?> byte</td>
			</tr>
			<tr>
				<td>上传速度</td>
				<td><?php echo $info['speed_upload'];?> byte</td>
			</tr>
			<tr>
				<td>下载速度</td>
				<td><?php echo $info['speed_download'];?> byte</td>
			</tr>
			<tr>
				<td>下载内容大小</td>
				<td><?php echo $info['download_content_length'];?> byte</td>
			</tr>
			<tr>
				<td>上传内容大小</td>
				<td><?php echo $info['upload_content_length'];?> byte</td>
			</tr>
			<tr>
				<td>HTTP请求头大小</td>
				<td><?php echo $info['request_size'];?> byte</td>
			</tr>
			<tr>
				<td>HTTP响应头大小</td>
				<td><?php echo $info['header_size'];?> byte</td>
			</tr>
			<tr>
				<td>HTTP请求头</td>
				<td><pre><?php printf($info['request_header']);?></pre></td>
			</tr>
			<tr>
				<td>HTTP响应头</td>
				<td><pre><?php printf($info['response_header']);?></pre></td>
			</tr>
		</tbody>
	</table>
</div>
