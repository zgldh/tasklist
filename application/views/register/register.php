<div class="container-fluid">
	<div class="row-fluid">
		<div class="span7">
			<form class="form-horizontal" method="post">
				<fieldset>
					<div class="control-group <?php echo @$errors['user_name']?'error':'';?>">
						<label class="control-label" for="user_name">帐号</label>
						<div class="controls">
							<input type="text" class="input-xlarge" id="user_name" name="Register[user_name]" value="<?php echo @$form['user_name'];?>" maxlength="16">
							<?php if(@$errors['user_name']):?>
							<p class="help-block"><?php echo $errors['user_name'];?></p>
							<?php else:?>
							<p class="help-block">登录时会用到</p>
							<?php endif;?>
						</div>
					</div>
					<div class="control-group <?php echo @$errors['password']?'error':'';?>">
						<label class="control-label" for="password">密码</label>
						<div class="controls">
							<input type="password" class="input-xlarge" id="password" name="Register[password]">
							<?php if(@$errors['password']):?>
							<p class="help-block"><?php echo $errors['password'];?></p>
							<?php endif;?>
							<p class="help-block">请输入大小写字母、数字来作为密码</p>
						</div>
					</div>
					<div class="control-group <?php echo @$errors['re_password']?'error':'';?>">
						<label class="control-label" for="re_password">重复密码</label>
						<div class="controls">
							<input type="password" class="input-xlarge" id="re_password" name="Register[re_password]">
							<?php if(@$errors['re_password']):?>
							<p class="help-block"><?php echo $errors['re_password'];?></p>
							<?php endif;?>
							<p class="help-block">重新输入一遍，确保没记错</p>
						</div>
					</div>
					<div class="control-group <?php echo @$errors['email']?'error':'';?>">
						<label class="control-label" for="email">电子邮箱</label>
						<div class="controls">
							<input type="text" class="input-xlarge" id="email" name="Register[email]" value="<?php echo @$form['email'];?>">
							<?php if(@$errors['email']):?>
							<p class="help-block"><?php echo $errors['email'];?></p>
							<?php else:?>
							<p class="help-block">确认注册，接收网站活动提醒等</p>
							<?php endif;?>
						</div>
					</div>
					<div class="form-actions">
						<button class="btn btn-primary btn-large" type="submit">注册</button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
