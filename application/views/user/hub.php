<?php $current_user instanceof UserPeer;?>
<?php $weibo_link instanceof WeiboLinkPeer;?>
<div class="container user-hub">
    <div class="row">
		<div class="span8">
			<dl class="dl-horizontal">
				<dt>帐号</dt>
				<dd><?php echo $current_user->name;?></dd>
				<dt>电子邮箱</dt>
				<dd><?php echo $current_user->email;?></dd>
				<dt>注册时间</dt>
				<dd><?php echo $current_user->reg_datetime;?></dd>
			</dl>
		</div>
		<div class="span4">
			<div>
			<?php if($weibo_link):?>
				<p>已绑定微博帐号</p>
				<?php $client = new SaeTClientV2(WB_APP_KEY,WB_APP_SECRET, $weibo_link->access_token);?>
				<?php $wb_profile = $client->show_user_by_id($weibo_link->uid); // done?>
				<?php print_r($wb_profile);?>
			<?php else:?>
				<h2><a href="<?php echo $weibo_oauth_url;?>"><img src="http://www.sinaimg.cn/blog/developer/wiki/24x24.png" />绑定新浪微博帐号</a></h2>
			<?php endif;?>
			</div>
			<div>
				<h5>绑定QQ帐号</h5>
			</div>
		</div>
	</div>
</div>
