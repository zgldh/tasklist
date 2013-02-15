<header id="hero" class="">
	<div class="jumbotron masthead">
		<div class="container-fluid" style="text-align: center;">
			<h1>自动任务</h1>
			<h2>互联网事件中心</h2>
			<p>您可以设置在特定的日子，特定的情况下来执行发送邮件、发送微博、访问URL等等任务。</p>
				
			<div class="metro" style="display: inline-block;">
				<a class="tile app" href="/task/create">
					<div class="image-wrapper">
						<i class="icon-new"></i>
			        </div>
			        <div class="app-label">新建任务</div>
			    </a>
			    <?php if($this->webuser->isLogin()):?>
				<a class="tile app" href="/task/list">
					<div class="image-wrapper">
						<i class="icon-list"></i>
			        </div>
			        <div class="app-label">我的任务列表</div>
			    </a>
				<a class="tile app" href="/user/hub">
					<div class="image-wrapper">
						<i class="icon-user"></i>
			        </div>
			        <div class="app-label">用户中心</div>
			    </a>
			    <?php else:?>
				<a class="tile app" href="/signin">
					<div class="image-wrapper">
						<i class="icon-enter"></i>
			        </div>
			        <div class="app-label">登录</div>
			    </a>
				<a class="tile app" href="/register">
					<div class="image-wrapper">
						<i class="icon-contact"></i>
			        </div>
			        <div class="app-label">注册</div>
			    </a>
			    <?php endif;?>
			</div>
		</div>
	</div>

</header>

<hr />
<div id="home-tiles" class="container-fluid">
	<h1>常用的自动任务</h1>
	<div class="row-fluid metro">
		<a class="tile wide app red" href="#">
	        <div class="app-label">任务1</div>
	    </a>
		<a class="tile wide app orange" href="#">
	        <div class="app-label">任务2</div>
	    </a>
		<a class="tile wide app yellow" href="#">
	        <div class="app-label">任务3</div>
	    </a>
		<a class="tile wide app green" href="#">
	        <div class="app-label">任务4</div>
	    </a>
		<a class="tile wide app sea" href="#">
	        <div class="app-label">任务5</div>
	    </a>
		<a class="tile wide app purple" href="#">
	        <div class="app-label">任务6</div>
	    </a>
		<a class="tile wide app" href="#">
	        <div class="app-label">任务7</div>
	    </a>
		<a class="tile wide app" href="#">
	        <div class="app-label">任务8</div>
	    </a>
		<a class="tile wide app" href="#">
	        <div class="app-label">任务9</div>
	    </a>
	</div>
</div>
