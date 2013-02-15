<?php $current_user instanceof UserPeer;?>
<?php $weibo_link instanceof WeiboLinkPeer;?>
<div class="container user-hub">
    <div class="row">
		<div class="span7">
			<dl class="dl-horizontal">
				<dt>帐号</dt>
				<dd><?php echo $current_user->name;?></dd>
				<dt>电子邮箱</dt>
				<dd><?php echo $current_user->email;?></dd>
				<dt>注册时间</dt>
				<dd><?php echo $current_user->reg_datetime;?></dd>
			</dl>
		</div>
		<div class="span5 metro">
			<?php if($weibo_link):?>
			<a class="tile wide imagetext" href="http://weibo.com/<?php echo $weibo_link->get_sae_domain();?>" target="_blank">
               <div class="image-wrapper">
                  <img alt="<?php echo $weibo_link->get_sae_screen_name();?>" src="<?php echo $weibo_link->get_sae_avatar_large();?>">
               </div>
               <div class="column-text">
                  <h3><?php echo $weibo_link->get_sae_screen_name();?></h3>
                  <div class="text">已绑定新浪微博账号</div>
               </div>
            </a>
			<?php else:?>
            <a class="tile wide imagetext" href="<?php echo $weibo_oauth_url;?>">
                <div class="image-wrapper">
                    <img src="http://www.sinaimg.cn/blog/developer/wiki/LOGO_64x64.png" />
                </div>
                <div class="column-text">
                    <h3>绑定新浪微博帐号</h3>
                </div>
            </a>
			<?php endif;?>
			<div class="tile wide imagetext">
				<h3>绑定QQ帐号</h3>
			</div>
		</div>
	</div>
</div>
