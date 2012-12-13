<?php $current_user = $this->webuser->getUser();?>
<?php $current_user instanceof UserPeer;?>
<?php $task instanceof TaskPeer;?>
<div class="container editor-page">
    <div class="row">
    	<div class="span3 bs-docs-sidebar">        
    		<ul class="nav nav-list editor-sidenav" data-spy="affix" data-offset-top="70">
    			<li><a href="#task_base"><i class="icon-pencil-2"></i> 基本信息</a></li>
    			<li><a href="#task_conditions"><i class="icon-target-2"></i> 触发条件</a></li>
    			<li><a href="#task_commands"><i class="icon-settings"></i> 执行内容</a></li>
    			<li><a href="#task_done"><i class="icon-flag"></i> 完成！</a></li>
    			<li class="divider"></li>
    			<li><a href="#help"><i class="icon-help"></i> 帮助</a></li>
    		</ul>
    	</div>
    	<div class="span9">
    		<form id="editor-form" action="" enctype="multipart/form-data" method="post" >
    	        <input type="hidden" name="Task[task_id]" value="<?php echo $task->task_id;?>" />
	    		<section id="task_base">
		    			<div class="page-header">
		    				<h2>基本信息</h2>
		    			</div>
		    			<div class="control-group">
		    				<label class="control-label" for="task_name">任务名（可选）</label>
		    				<input class="input-xlarge" name="Task[name]" id="task_name" type="text" placeholder="任务名..."
		    					value="<?php echo $task->name;?>"
	    					/>
	    				<span class="help-block">任务名会显示在您的任务列表中，以便您查找使用。如果不填写，则系统会自动生成一个名字。</span>
	    			</div>
	    			<div class="control-group">
	    				<label class="control-label" for="task_limit">执行次数限制（可选）</label>
	    				<input class="input-xlarge" name="Task[limit]" id="task_limit" type="text" placeholder="无限制"
	    					value="<?php echo $task->limit?$task->limit:null;?>"
		    				/>
	    				<span class="help-block">请填写希望最多执行多少次，默认无限制。</span>
	    			</div>
	    		</section>
	    		<section id="task_conditions">
	                <div class="page-header">
	                    <h2>触发条件</h2>
	                    <span class="alert alert-info pull-right"><i class="icon-info"></i> 注意， 必须所有条件都满足才会执行。</span>
	                   
	                    <div class="functions-menu">
	                        <div class="dropdown">
	                        	<a class="btn btn-link dropdown-toggle"
	                        		data-toggle="dropdown" href="#"
	                        	>添加时间条件<span class="caret"></span>
	                        	</a>
	                        	<ul class="dropdown-menu">
	                    		    <li><a id="condition_date_static_btn" >特定日期</a></li>
	                    		    <li><a class="disabled">特定节日</a></li>
	                    		    <li><a class="disabled">特定节气（农历）</a></li>
	                        	</ul>
	                        </div>
	                        
	                        <div class="dropdown">
	                        	<a class="btn btn-link dropdown-toggle"
	                        		data-toggle="dropdown" href="#"
	                        	>添加气象条件<span class="caret"></span>
	                        	</a>
	                        	<ul class="dropdown-menu">
	                    		    <li>
	                                	<a class="disabled">城市天气</a>
	                            	</li>
	                        	</ul>
	                        </div>
	                        
	                        <div class="dropdown">
	                        	<a class="btn btn-link dropdown-toggle"
	                        		data-toggle="dropdown" href="#"
	                        	>添加金融条件<span class="caret"></span>
	                        	</a>
	                        	<ul class="dropdown-menu">
	                    		    <li>
	                                	<a class="disabled">中国股市</a>
	                                	<a class="disabled">黄金市场</a>
	                            	</li>
	                        	</ul>
	                        </div>
	                    </div>
	        			
	                </div>
	                
	                <ol class="condition-list">
	                    <?php $has_condition = false;?>
	                	<?php $condition = $task->getCondition(ConditionPeer::TYPE_DATE_STATIC);?>
	                	<?php $parameters = $condition?$condition->getParameters():null;?>
	                	<li class="condition-item condition-date-static
	                	<?php echo $condition?null:'hide';?>
	                	">
	                		<?php if($condition):?>
	                		<?php $has_condition = true;?>
	                		<input type="hidden" name="Task[Conditions][date-static][condition_id]" value="<?php echo $condition->condition_id;?>" />
	                		<?php endif;?>
	                		<label>特定日期</label>
	                		<select name="Task[Conditions][date-static][year]" class="span1 date-static-year" 
	                			<?php echo $condition?null:'disabled="disabled"';?>
	                		>
	                			<option value="">每年</option>
		                		<?php for($i = 0;$i<10;$i++):?>
	                		<?php $y = (int)date('Y')+$i;?>
	                			<option value="<?php echo $y;?>"
	                				<?php echo (@$parameters->year==$y)?'selected="selected"':null;?>
	                			><?php echo $y;?> 年</option>
	                		<?php endfor;?>
	                		</select>
	                		<select name="Task[Conditions][date-static][month]" class="span1 date-static-month"
	                				<?php echo $condition?null:'disabled="disabled"';?>
	                		>
	                			<option value="">每月</option>
		                		<?php for($m = 1;$m<=12;$m++):?>
	                			<option value="<?php echo $m;?>"
	                				<?php echo (@$parameters->month==$m)?'selected="selected"':null;?>
	                			><?php echo $m;?> 月</option>
	                		<?php endfor;?>
	                		</select>
	                		<select name="Task[Conditions][date-static][day]" class="span1 date-static-day" 
	                				<?php echo $condition?null:'disabled="disabled"';?>
	                		>
	                			<option value="">每天</option>
	                			<option value="-1"
	                				<?php echo (@$parameters->day==-1)?'selected="selected"':null;?>
	                			>最后一天</option>
		                		<?php for($d = 1;$d<=31;$d++):?>
	                			<option value="<?php echo $d;?>"
	                				<?php echo (@$parameters->day==$d)?'selected="selected"':null;?>
	                			><?php echo $d;?> 日</option>
	                		<?php endfor;?>
	                		</select>
	                		<select name="Task[Conditions][date-static][hour]" class="span1 date-static-hour" 
	                				<?php echo $condition?null:'disabled="disabled"';?>
	                		>
		                		<?php for($h = 0;$h<24;$h++):?>
	                			<option value="<?php echo $h;?>"
	                				<?php echo (@$parameters->hour==$h)?'selected="selected"':null;?>
	                			><?php echo $h;?> 点</option>
	                		<?php endfor;?>
	                		</select>
	                		<button class="btn btn-warning condition-delete-btn" type="button" title="删除条件"><i class="icon-remove"></i></button>
	                		<p class="hide alert alert-error TaskError_Conditions_date-static"></p>
	                	</li>
	<!--                     	<li class="condition-item condition-date-festival">特定节日</li> -->
	<!--                     	<li class="condition-item condition-date-festival">特定节气（农历）</li> -->
	                	
	                	<li class="condition-item no-conditions alert
	                	<?php echo $has_condition?'hide':null;?>
	                	">还没有任何触发条件。请点击上方下拉按钮添加，要不然我就不知道什么时候执行了。</li>
	                </ol>
	                
	    	    </section>
	    		<section id="task_commands">
	                <div class="page-header">
	                    <h2>执行内容</h2>
	                    
	                    <div class="dropdown">
	                    	<a class="btn btn-link dropdown-toggle"
	                    		data-toggle="dropdown" href="#"
	                    	>添加执行内容<span class="caret"></span>
	                    	</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a id="command-url-request-btn">访问URL</a></li>
	                    		<li><a id="command-send-email-btn">发送电子邮件</a></li>
	                    		<li><a class="disabled">发送微博(coming soon)</a></li>
	                    		<li><a class="disabled">发送短信(coming soon)</a></li>
	                    	</ul>
	                    </div>
	                </div>
	                <ol class="command-list">
	                    <?php $has_command = false;?>
	                	<?php $command = $task->getCommand(CommandPeer::TYPE_URL_REQUEST);?>
	                	<?php $parameters = $command?$command->getParameters():null;?>
	                	<li class="command-item command-url-request
	                	<?php echo $command?null:'hide';?>
	                	">
	                		<?php if($command):?>
	                		<?php $has_command = true;?>
	                		<input type="hidden" name="Task[Commands][url-request][command_id]" value="<?php echo $command->command_id;?>" />
	                		<?php endif;?>
	                		
	                		<label>访问URL</label>
	                		<input type="text" name="Task[Commands][url-request][url]" class="span4" placeholder="请输入一个URL。。。" 
	                			<?php echo $command?null:'disabled="disabled"';?>
	                			value="<?php echo @$parameters->url;?>"
	                		/>
	                		<button class="btn btn-warning command-delete-btn" type="button" title="删除命令"><i class="icon-remove"></i></button>
	                		
	                		<p class="hide alert alert-error TaskError_Conditions_url-request"></p>
	                	</li>
	                	
	                	<?php $command = $task->getCommand(CommandPeer::TYPE_SEND_EMAIL);?>
	                	<?php $parameters = $command?$command->getParameters():null;?>
	                	<li class="command-item command-send-email
		                <?php echo $command?null:'hide';?>
	                	">
	                		<?php if($command):?>
	                		<?php $has_command = true;?>
	                		<input type="hidden" name="Task[Commands][send-email][command_id]" value="<?php echo $command->command_id;?>" />
	                		<?php endif;?>
		                		
		                		<label>发送电子邮件</label>
		                		<span>请填写收件人地址。每个地址用<a class="label label-info" rel="tooltip" title="分号">;</a>隔开。最多五个地址。</span>
		                		<br />
		                		<textarea name="Task[Commands][send-email][recipients]" class="span7"
	                			<?php echo $command?null:'disabled="disabled"';?>
	                		><?php echo $command?$command->getRecipientsString($current_user->email.';'):($current_user->email.';');?></textarea>
	                		<br />
	                		<input type="hidden" name="Task[Commands][send-email][content]" class="send-email-content-input"
	                		<?php echo $command?null:'disabled="disabled"';?>
	                			value="<?php echo @$parameters->content;?>"
	                		/>
	                		<a class="btn btn-info command-send-email-edit" href="#send-email-content-modal" data-toggle="modal" title="设置邮件内容"><i class="icon-write"></i>编辑邮件内容</a>
	                		<button class="btn btn-warning command-delete-btn" type="button" title="删除命令"><i class="icon-remove"></i></button>
	                		
	                		<p class="hide alert alert-error TaskError_Conditions_send-email"></p>
	                	</li>
	                	
	                	<li class="command-item no-commands alert
	                	<?php echo $has_command?'hide':null;?>
	                	">还没有任何执行内容。请点击上方下拉按钮添加，做点什么吧。</li>
	                  
	                </ol>
	
	    	    </section>
	    		<section id="task_done">
	                <div class="page-header">
	                    <h2>完成！</h2>
	                </div>
	                <?php if($has_condition && $has_command):?>
	                <button type="button" id="editor-submit-fake" class="btn btn-primary disabled hide">请先设定触发条件与执行内容</button>
	                <button type="button" id="editor-submit" class="btn btn-primary"
	                    data-loading-text="保存中。。。" autocomplete="off"
	                >保存</button> 
	                <a class="btn" href="/task/edit/<?php echo $task->task_id;?>">复原</a>
	                <?php else:?> 
	                <button type="button" id="editor-submit-fake" class="btn btn-primary disabled">请先设定触发条件与执行内容</button>
	                <button type="button" id="editor-submit" class="btn btn-primary hide"
	                    data-loading-text="保存中。。。" autocomplete="off"
	                >保存</button>
	                <?php endif;?>
	                
	            	<p class="hide alert alert-error TaskError_system"></p>
	            </section>
            </form>
    	</div>
    </div>
</div>

<div id="send-email-content-modal" class="modal hide fade message" tabindex="-1" role="dialog" aria-labelledby="send-email-content-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h3 id="send-email-content-modal-label">编辑邮件内容</h3>
	</div>
	<div class="modal-body">
		<textarea name="email-content" style="width:100%;height:300px;visibility:hidden;"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
		<button class="btn btn-primary">保存</button>
	</div>
</div>
<link rel="stylesheet" href="/js/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="/js/kindeditor/plugins/code/prettify.css" />
<script charset="utf-8" src="/js/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="/js/kindeditor/lang/zh_CN.js"></script>
<script charset="utf-8" src="/js/kindeditor/plugins/code/prettify.js"></script>
<script>
	KindEditor.ready(function(K) {
		var editor1 = K.create('textarea[name="email-content"]', {
			cssPath : '/js/kindeditor/plugins/code/prettify.css',
			allowFileManager : false,
			allowImageUpload : false,
			allowFlashUpload:false,
			allowMediaUpload:false,
			allowFileUpload:false,
			afterCreate : function() {
				var self = this;
			},
			items : [
				'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'image', 'link'
			]
		});
		prettyPrint();
		$('#send-email-content-modal').data('editor',editor1);
	});
</script>



<div id="error-modal" class="modal hide fade warning" tabindex="-1" role="dialog" aria-labelledby="error-modal-label" aria-hidden="true">
	<div class="modal-header">
		<h3 id="error-modal-label">错误</h3>
	</div>
	<div class="modal-body">
		<p></p>
	</div>
</div>
