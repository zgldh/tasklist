<?php $trigger instanceof DateTimeStaticDateAppTriggerPeer; ?>
<?php $date = getdate(time() + 86400);?>
<form class="DateTimeStaticDate form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->app_trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">在特定日期为</label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[year]">
				<?php for($i=$date['year'];$i<2099;$i++):?>
					<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
				</select>
				<span class="add-on">年</span>
			</div>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			<div class="input-append">
				<select name="trigger[month]">
				<?php for($i=1;$i<13;$i++):?>
					<option value="<?php echo $i;?>"
					<?php if($i == $date['mon']):?>selected="selected"<?php endif;?>
					><?php echo $i;?></option>
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
				<?php for($i=1;$i<32;$i++):?>
					<option value="<?php echo $i;?>"
					<?php if($i == $date['mday']):?>selected="selected"<?php endif;?>
					><?php echo $i;?></option>
				<?php endfor;?>
				</select>
				<span class="add-on">日</span>
			</div>
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
	var month_day = [0,31,28,31,30,31,30,31,31,30,31,30,31];
	var trigger_year = $("select[name='trigger[year]']");
	var trigger_month = $("select[name='trigger[month]']");
	var trigger_day = $("select[name='trigger[day]']");
	var trigger_hour = $("select[name='trigger[hour]']");
	var trigger_minute = $("select[name='trigger[minute]']");

	trigger_month.change(function(){
	    var month =trigger_month.val() ; 
	    $day = month_day[month];
	    if(month == 2 && is_leap_year(trigger_year.val()) )
        {
	        $day = 29;
        }
        setup_month_day($day);
	});
	trigger_year.change(function(){
	    var year = trigger_year.val();
        var month = trigger_month.val();
        if(month == 2)
        {
            if(is_leap_year(year))
            {
                setup_month_day(29);
            }
            else
            {
                setup_month_day(28);
            }
        }
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

	function is_leap_year(year)
	{
	    if(year/400 == parseInt(year/400))
        {
	        return true;
        }
	    if(year/4 == parseInt(year/4))	        
        {
	        return true;
        }
	    return false;
	}

	trigger_month.change();
})();
</script>