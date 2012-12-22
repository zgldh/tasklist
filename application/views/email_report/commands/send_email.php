<?php 
/**
 * $recipients;
 * $result;
 * $attachment;
 */
?>
<div>
	<h3>发送电子邮件</h3>
	<?php if($result === true):?>
	<p>收件人：<?php echo $recipients;?><br />邮件发送成功。</p>
	<?php else:?>
	<p>收件人：<?php echo $recipients;?><br />邮件发送失败！</p>
	<?php endif;?>
	<?php if($attachment):?>
	<p>发送的邮件详情请查看附件：<?php echo key($attachment);?></p>
	<?php endif;?>
</div>