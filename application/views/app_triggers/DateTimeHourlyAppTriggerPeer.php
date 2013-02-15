<?php $trigger instanceof DateTimeHourlyAppTriggerPeer; ?>
<form class="DateTimeHourly form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">在每小时的第</label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[minute]">
					<option value="0">00</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="45">45</option>
				</select>
				<span class="add-on">分</span>
			</div>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit">设为条件</button>
	</div>
</form>