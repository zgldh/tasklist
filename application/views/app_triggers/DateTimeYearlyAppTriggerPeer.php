<?php $trigger instanceof DateTimeYearlyAppTriggerPeer; ?>
<form class="DateTimeYearly form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">在每年的</label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[month]">
				<?php for($i=1;$i<13;$i++):?>
					<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
				</select>
				<span class="add-on">月</span>
			</div>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[day]">
					<option value="-1">最后一天</option>
				<?php for($i=1;$i<32;$i++):?>
					<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
				</select>
				<span class="add-on">日</span>
			</div>
			<span class="help-inline">2月29日只有阳历闰年才会触发。<br />如果想在每月最后一天触发， 请选择左边列表中的<strong>最后一天</strong>。</span>
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
	var month_day = [0,31,29,31,30,31,30,31,31,30,31,30,31];
	var trigger_month = $("select[name='trigger[month]']");
	var trigger_day = $("select[name='trigger[day]']");
	var trigger_hour = $("select[name='trigger[hour]']");
	var trigger_minute = $("select[name='trigger[minute]']");

	trigger_month.change(function(){
	    var month =trigger_month.val() ; 
	    $day = month_day[month];
        setup_month_day($day);
	});

	
	function setup_month_day(day)
	{
	    var options = trigger_day.find('option');
	    options.each(function(i){
	        var option = $(this);
	        if(option.val() > day)
            {
	            option.hide();
            }
	        else
            {
	            option.show();
            }
	    });
	    if(trigger_day.val()>day)
        {
	    	trigger_day.val(day);
        }
	}

})();
</script>