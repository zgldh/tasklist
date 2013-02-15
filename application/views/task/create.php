<?php $current_user = $this->webuser->getUser();?>
<?php $current_user instanceof UserPeer;?>
<div class="container create-page">
    
    <div id="step_1" class="row-fluid">
    	<div class="span12 metro">
    	    <a class="tile wide text" id="trigger-btn" data-url="/app/ajax_all/triggers">
    	        <div class="text-header3">首先设置条件</div>
    	    </a>
    	    <a class="tile wide text muted">
    	        <div class="text-header3">稍后设置命令</div>
    	    </a>
    	</div>
    </div>
    
    <div id="step_2" class="row-fluid hide">
    	<div class="page-header">
    		<h1>以哪个应用做条件呢？</h1>
    	</div>
    	<div class="span12 metro" id="trigger-apps-box" data-url="/app/ajax_app_triggers/"></div>
    </div>
    
    <div id="step_3" class="row-fluid hide">
    	<a class="btn btn-link back" data-target="step_2">上一步</a>
    	<div class="page-header">
    		<h1><strong style="margin-right: 50px;"></strong>选择一个条件</h1>
    	</div>
    	<div class="span12 metro" id="triggers-box" data-url="/app/ajax_app_trigger_detail/"></div>
    </div>
    
    <div id="step_4" class="row-fluid hide" data-submit-url="/app/ajax_app_trigger_submit">
    	<a class="btn btn-link back" data-target="step_3">上一步</a>
    	<div class="page-header">
    		<h1><strong style="margin-right: 50px;"></strong> 详细配置本条件</h1>
    	</div>
    	<div class="span12" id="trigger-detail-box"></div>
    </div>
    
    <div id="step_5" class="row-fluid hide" >
    	<a class="btn btn-link back" data-target="step_4" remain="remain">上一步</a>
    	<div class="page-header">
    		<h1>继续</h1>
    	</div>
    	<div class="span12 metro">
    	    <a class="tile wide imagetext" id="trigger-btn-2">
    	    	<div class="image-wrapper">
    	    		<i class="icon-"></i>
    	    	</div>
    	    	<div class="column-text">
    	    		<div class="text5"></div>
    	    	</div>
    	    </a>
    	    <a class="tile wide text" id="command-btn" data-url="/app/ajax_all/commands">
    	        <div class="text-header3">然后设置命令</div>
    	    </a>
    	</div>
    </div>
    
    
    <div id="step_6" class="row-fluid hide">
    	<a class="btn btn-link back" data-target="step_5">上一步</a>
    	<div class="page-header">
    		<h1>哪个应用里的命令呢？</h1>
    	</div>
    	<div class="span12 metro" id="command-apps-box" data-url="/app/ajax_app_commands/"></div>
    </div>
    
    <div id="step_7" class="row-fluid hide">
    	<a class="btn btn-link back" data-target="step_6">上一步</a>
    	<div class="page-header">
    		<h1><strong style="margin-right: 50px;"></strong>选择一个命令</h1>
    	</div>
    	<div class="span12 metro" id="commands-box" data-url="/app/ajax_app_command_detail/"></div>
    </div>
    
    <div id="step_8" class="row-fluid hide" data-submit-url="/app/ajax_app_command_submit">
    	<a class="btn btn-link back" data-target="step_7">上一步</a>
    	<div class="page-header">
    		<h1><strong style="margin-right: 50px;"></strong> 详细配置本命令</h1>
    	</div>
    	<div class="span12" id="command-detail-box"></div>
    </div>
    
    <div id="step_9" class="row-fluid hide form-horizontal">
    	<a class="btn btn-link back" data-target="step_8" remain="remain">上一步</a>
    	<div class="page-header">
    		<h1>完成!</h1>
    	</div>
    	<div class="span12 metro">
    	    <a class="tile wide imagetext" id="trigger-btn-3">
    	    	<div class="image-wrapper">
    	    		<i class="icon-"></i>
    	    	</div>
    	    	<div class="column-text">
    	    		<div class="text5"></div>
    	    	</div>
    	    </a>
    	    <a class="tile wide imagetext" id="command-btn-3">
    	    	<div class="image-wrapper">
    	    		<i class="icon-"></i>
    	    	</div>
    	    	<div class="column-text">
    	    		<div class="text5"></div>
    	    	</div>
    	    </a>
    	</div>
    	
    	<form id="task-form">
        <div class="control-group" style="margin-top:240px;">
    		<label class="control-label">任务名（可选）</label>
    		<input id="task-name" type="text" name="task[name]" class="input-xxlarge" maxlength="255"/>
    	</div>
    	</form>
    	
    	<div class="form-actions">
    		<button type="submit" id="final-submit" data-url="/task/ajax_create_submit">完成</button>
    	</div>
    </div>
</div>

<div id="error-modal" class="modal hide fade warning" tabindex="-1" role="dialog" aria-labelledby="error-modal-label" aria-hidden="true">
	<div class="modal-header">
		<h3 id="error-modal-label">错误</h3>
	</div>
	<div class="modal-body">
		<p></p>
	</div>
</div>


<a id="app-tile-template" class="tile app hide">
    <div class="image-wrapper">
        <i class="icon-"></i>
    </div>
    <div class="app-label"></div>
    <div class="app-count"></div>
</a>

<a id="trigger-tile-template" class="triggers tile wide text hide">
	<div class="text5">
		<h3></h3>
		<p></p>
	</div>
</a>

<a id="command-tile-template" class="commands tile wide text hide">
	<div class="text5">
		<h3></h3>
		<p></p>
	</div>
</a>