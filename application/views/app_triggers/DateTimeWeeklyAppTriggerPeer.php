<?php $trigger instanceof DateTimeWeeklyAppTriggerPeer; ?>
<form class="DateTimeWeekly form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->app_trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">在每周的</label>
		<div class="controls">
			<label class="checkbox">
				<input name="trigger[week_day][]" value="1" id="DateTimeWeeklyMonday" type="checkbox" />
				<span class="metro-checkbox">周一</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="2" id="DateTimeWeeklyTuesday" type="checkbox" />
				<span class="metro-checkbox">周二</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="3" id="DateTimeWeeklyWednesday" type="checkbox" />
				<span class="metro-checkbox">周三</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="4" id="DateTimeWeeklyThursday" type="checkbox" />
				<span class="metro-checkbox">周四</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="5" id="DateTimeWeeklyFriday" type="checkbox" />
				<span class="metro-checkbox">周五</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="6" id="DateTimeWeeklySaturday" type="checkbox" />
				<span class="metro-checkbox">周六</span>
			</label>
			<label class="checkbox">
				<input name="trigger[week_day][]" value="日" id="DateTimeWeeklySunday" type="checkbox" />
				<span class="metro-checkbox">周日</span>
			</label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[hour]">
				<?php for($i=0;$i<24;$i++):?>
					<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
				</select>
				<span class="add-on">点</span>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"></label>
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
<script type="text/javascript">
(function(){
	var week_checkboxes = $('.DateTimeWeekly label.checkbox input[type="checkbox"]');
	var submit = $('.DateTimeWeekly button[type="submit"]');
	var validation = function()
	{
		if(week_checkboxes.filter(':checked').size() == 0)
		{
			var msg = '请选择周日期。';
			return msg;
		}
		else
		{
			return null;
		} 
	};
	submit.data('validation',validation);
})();
</script>