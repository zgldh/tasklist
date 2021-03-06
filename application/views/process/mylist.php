<div class="container mylist-page">
    <?php if($processes):?>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="span1">编号</th>
                <th class="span2" style="width: 130px;">计划执行时间</th>
                <th class="span2">实际执行时间</th>
                <th>任务名</th>
                <th class="span2">操作</th>
            </tr>
        </thead>
        <tbody>
        	<?php $last_executed = 0;?>
            <?php foreach($processes as $process):?>
            <?php $process instanceof TimingProcessPeer;?>
            <tr class="<?php echo $process->isSkip()?'skip':null; ?> 
            <?php if($process->executed == 1):?>
            <?php echo 'success';?>
	            <?php if($last_executed == 0):?>
	            <?php echo 'execute-bolder';?>
	            <?php $last_executed = 1;?>
	            <?php endif;?>
            <?php endif;?>">
                <td><?php echo $process->process_id;?></td>
                <td><?php echo $process->getPlanTime();?></td>
                <td><?php echo $process->getExecTime();?></td>
                <td><?php echo $process->getTask()->getName();?></td>
                <td>
                    <div class="btn-group">
                    	<?php if($process->executed == 0):?>
	                        <a class="btn btn-small btn-link skip-process-btn
	                        <?php echo $process->isSkip()?'hide':null;?>
	                        "
	                        	href="/process/skip/<?php echo $process->process_id;?>"
	                        	data-loading-text="跳过"
	                        >跳过</a>
	                        <a class="btn btn-small btn-link restore-process-btn
	                        <?php echo $process->isSkip()?null:'hide';?>
	                        "
	                        	href="/process/restore/<?php echo $process->process_id;?>"
	                        	data-loading-text="恢复"
	                        >恢复</a>
                        <?php endif;?>
                        <a class="btn btn-small btn-link"
                        	href="/task/edit/<?php echo $process->getTask()->task_id;?>?ref=/process/list"
                        >编辑任务</a>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php else:?>
    <div class="alert alert-info alert-block">
        <h4><i class="icon-info"></i>您还没有任何自动任务</h4>
                    为什么不去<a class="btn btn-primary" class="link" href="/task/create">创建一个</a>试试呢？
    </div>
    <?php endif;?>
</div>

<div id="error-modal" class="modal hide fade warning" tabindex="-1" role="dialog" aria-labelledby="error-modal-label" aria-hidden="true">
	<div class="modal-header">
		<h3 id="error-modal-label">错误</h3>
	</div>
	<div class="modal-body">
		<p></p>
	</div>
</div>
