<div class="container mylist-page">
    <?php if($processes):?>
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="span1">编号</th>
                <th>任务名</th>
                <th class="span2">执行次数</th>
                <th class="span2">创建日期</th>
                <th class="span2">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($processes as $process):?>
            <?php $process instanceof TaskPeer;?>
            <tr>
                <td><?php echo $process->task_id;?></td>
                <td><?php echo $process->getName();?></td>
                <td><?php echo $process->times;?>/<?php echo $process->limit?$process->limit:'无限';?></td>
                <td><?php echo $process->create_date;?></td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-small btn-link"
                        	href="/task/edit/<?php echo $process->task_id;?>"
                        >编辑</a>
                        <a class="btn btn-small btn-link">暂停</a>
                        <a class="btn btn-small btn-danger delete-task-btn" href="/task/delete/<?php echo $process->task_id;?>"
							data-loading-text="正在删除"
							data-task-name="<?php echo $process->name; ?>"
						>删除</a>
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
