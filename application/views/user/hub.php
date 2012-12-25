<?php $current_user = $this->webuser->getUser();?>
<?php $current_user instanceof UserPeer;?>
<?php $weibo_link = $current_user->getWeiboLink();?>
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
				<h2><a><img src="http://www.sinaimg.cn/blog/developer/wiki/24x24.png" />绑定新浪微博帐号</a></h2>
			</div>
			<div>
				<h5>绑定QQ帐号</h5>
			</div>
		</div>
	</div>
</div>
