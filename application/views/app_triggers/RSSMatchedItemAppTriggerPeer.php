<?php $trigger instanceof RSSMatchedItemAppTriggerPeer; ?>
<form class="RSSMatchedItem form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->app_trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">请输入RSS源地址：</label>
		<div class="controls">
			<div class="input-append">
				<input type="text" name="trigger[url]" >
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">请输入匹配关键字：</label>
		<div class="controls">
			<div class="input-append">
				<input type="text" name="trigger[match_str]" >
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit">设为条件</button>
	</div>
</form>