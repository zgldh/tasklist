<?php $trigger instanceof DateTimeMinutesCycleAppTriggerPeer; ?>
<form class="DateTimeMinutesCycle form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">每隔</label>
		<div class="controls">
			<div class="input-append">
				<input name="trigger[minutes]" type="text" value="10" />
				<span class="add-on">分钟</span>
			</div>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit">设为条件</button>
	</div>
</form>