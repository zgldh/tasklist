<form class="form-horizontal" method="post">
	<input type="hidden" name="redirect_to" value="<?php echo $this->navbar->getSignInPageRedirectTo();?>" />
    <fieldset>
        <legend>会员登录</legend>
        <?php if(isset($error)):?>
        <div class="alert alert-error">
            <strong>登录失败</strong>
            <?php echo $error['msg'];?>
        </div>
        <?php endif;?>
        <div class="control-group">
            <label class="control-label" for="user_name">帐号</label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="user_name" name="user_name">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="password">密码</label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="password" name="password">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary btn-large" type="submit">登录</button>
            <a class="btn btn-success btn-large" href="/register">注册</a>
        </div>
    </fieldset>
</form>