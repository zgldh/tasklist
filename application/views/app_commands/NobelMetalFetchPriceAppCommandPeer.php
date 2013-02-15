<?php $command instanceof NobelMetalFetchPriceAppCommandPeer; ?>
<form class="NobelMetalFetchPrice form-horizontal">
	<input name="command[id]" value="<?php echo $command->command_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			本命令没有什么好设置的。
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit">设为条件</button>
	</div>
</form>