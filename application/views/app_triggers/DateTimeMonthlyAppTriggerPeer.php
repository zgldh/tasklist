<?php $trigger instanceof DateTimeMonthlyAppTriggerPeer; ?>
<form class="DateTimeMonthly form-horizontal">
	<input name="trigger[id]" value="<?php echo $trigger->app_trigger_id;?>" type="hidden" />
	<div class="control-group">
		<label class="control-label">在每月的</label>
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
			<span class="help-inline">类似2月31日这种不存在的日期会自动跳过。 2月29日只有阳历闰年才会触发。<br />如果想在每月最后一天触发， 请选择左边列表中的<strong>最后一天</strong>。</span>
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