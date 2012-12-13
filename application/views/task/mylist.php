<div class="container mylist-page">
    <?php if($tasks):?>
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
            <?php foreach($tasks as $task):?>
            <?php $task instanceof TaskPeer;?>
            <tr class="<?php echo $task->status; ?>">
                <td><?php echo $task->task_id;?></td>
                <td><?php echo $task->getName();?></td>
                <td><?php echo $task->times;?>/<?php echo $task->limit?$task->limit:'无限';?></td>
                <td><?php echo $task->create_date;?></td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-small btn-link"
                        	href="/task/edit/<?php echo $task->task_id;?>?ref=/task/list"
                        >编辑</a>
                        <a class="btn btn-small btn-link active-task-btn
                        <?php echo $task->isPause()?null:'hide';?>
                        "
                            href="/task/active/<?php echo $task->task_id;?>"
							data-loading-text="激活"
						>激活</a>
                        <a class="btn btn-small btn-link pause-task-btn
                        <?php echo $task->isPause()?'hide':null;?>
                        "
                            href="/task/pause/<?php echo $task->task_id;?>"
							data-loading-text="暂停"
						>暂停</a>
                        <a class="btn btn-small btn-danger delete-task-btn" 
                            href="/task/delete/<?php echo $task->task_id;?>"
							data-loading-text="删除"
							data-task-name="<?php echo $task->name; ?>"
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
