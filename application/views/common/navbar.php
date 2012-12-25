<?php
/**
 * @param WebUser $webuser
 * @param string $current_nav_item
 */
?>
<header id="nav-bar" class="container-fluid">
	<div class="row-fluid">
		<div class="span2">
			<div id="header-container">
				<?php if($this->navbar->issetBackBtn()):?>
				<a id="backbutton" class="win-backbutton" href="<?php echo $this->navbar->getBackBtnURL();?>"
					title="<?php echo $this->navbar->getBackBtnTitle();?>"
				></a>
				<?php endif;?>
				<h5>自动任务</h5>
				<div class="dropdown">
					<a class="header-dropdown dropdown-toggle accent-color"
						data-toggle="dropdown" href="#"
					>开始<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li><a href="/task/list">我的任务</a></li>
						<li><a href="/process/list">任务执行历史和计划管理</a></li>
						<li><a href="/task/create">新建任务</a></li>
						<li class="divider"></li>
						<li><a href="/">首页</a></li>
						<li><a href="/about">关于</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="span6">
			<h2><?php echo $this->navbar->getHeaderTitle();?></h2>
		</div>
		<div id="top-info" class="pull-right">
			<?php if($this->webuser->isLogin()):?>
			<a class="pull-left" href="/user/hub">
				<span class="top-info-block">
					<h3><?php echo $this->webuser->getUserName();?></h3>
					<h4>{user level}</h4>
				</span>
				<span class="top-info-block">
					<b class="icon-user"></b>
				</span>
			</a>
			<hr class="separator pull-left" />
			<a href="/logout" title="退出"><b class="icon-exit"></b></a>
            <?php else:?>
			<a class="pull-left" href="/register" title="注册">
				<span class="top-info-block">
				<h4>注册</h4>
				</span>
				<span class="top-info-block">
				<b class="icon-contact"></b>
				</span>
			</a>
	            <?php if($this->navbar->isDisplaySignIn()):?>
	            <hr class="separator pull-left" />
	            <a class="pull-left" data-toggle="modal" href="/signin">
					<span class="top-info-block">
					<h4>登录</h4>
					</span>
					<span class="top-info-block">
					<b class="icon-enter"></b>
					</span>
				</a>
				<?php endif;?>
            <?php endif;?>
<!-- 			<hr class="separator pull-left" /> -->
<!-- 			<a id="settings" class="pull-left" href="#"> <b class="icon-settings"></b></a> -->
		</div>
	</div>
</header>

<?php if($this->navbar->isDisplaySignIn()):?>
<div class="modal hide" id="login_modal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h3>登录</h3>
	</div>
	<form method="post" action="/signin" >
		<input type="hidden" name="redirect_to"	value="<?php echo $this->navbar->getRedirectTo();?>"/>
		<div class="modal-body">
			<input type="text" class="input-medium" placeholder="帐号" name="user_name">
			<input type="password" class="input-medium" placeholder="密码" name="password">
			<hr />
			<img src="http://timg.sjs.sinajs.cn/t4/appstyle/widget/images/loginButton/loginButton_24.png"/>
			<img src="http://wiki.dev.renren.com/mediawiki/images/9/95/%E8%BF%9E%E6%8E%A5%E6%8C%89%E9%92%AE2_%E7%99%BD%E8%89%B2132X28.png"/>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn btn-primary" value="登录" />
			<a class="btn btn-info">忘记密码</a>
			<a href="#" class="btn btn-info" data-dismiss="modal">关闭</a>
		</div>
	</form>
</div>
<?php endif;?>