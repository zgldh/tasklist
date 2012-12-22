<?php 
/**
 * $report;
 * $task;
 */
$report instanceof ReportEmailPeer;
$sections = $report->getSections();
?>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
</head>
<div>
    <div>
        <h1><?php echo $task->getName();?> 任务执行报告</h1>
        <div>执行时间： <span><?php echo $report->gen_datetime;?></span></div>
    </div>
    <hr>
    <div>
        <h2>执行结果</h2>
        <div>
        <?php $section_len = count($sections);?>
        <?php $section_index = 1;?>
        <?php foreach($sections as $section):?>
        <?php echo $section;?>
        <?php if($section_index<$section_len):?>
        <hr style="border-bottom: 1px solid #ccc;border-top:none;height: 0;" >
        <?php endif;?>
        <?php $section_index++;?>
        <?php endforeach;?>
        </div>
    </div>
    <hr>
    <div style="text-align: center;">
        <a href="<?php echo BASEURL;?>">TaskList</a><br />
        <?php echo date('Y');?>
    </div>
</div>
</html>
