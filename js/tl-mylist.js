/**
 * tl-mylist.js
 */
$(function(){
	var error_modal = $('#error-modal');
	error_modal.modal({show:false});

	//删除按钮
	var delete_task_btn = $('a.delete-task-btn');
	delete_task_btn.click(function(){
		var btn = $(this);
		var href= btn.attr('href');
		var name= btn.attr('data-task-name');
		if(!confirm('真的要删除 '+name+' 么？'))
		{
			return false;
		}

		btn.button('loading');
    	$.getJSON(href,function(re){
    	    if(re.success == true)
	        {
				var tr = btn.parentsUntil('tbody');
				tr.hide('normal',function(){
					tr.remove();
				});
	        }
    	    else
	        {
    	        showErrorModal(re.errors);
				btn.button('reset');
	        }
    	});
		return false;
	});
	
	//暂停按钮
	var pause_task_btn = $('a.pause-task-btn');
	pause_task_btn.click(function(){
	    var btn = $(this);
        var href= btn.attr('href');
        btn.button('loading');
        $.getJSON(href,function(re){
            if(re.success == true)
            {
                var tr = btn.parentsUntil('tbody').filter('tr');
                tr.removeClass().addClass('pause');
                btn.addClass('hide');
                btn.siblings('a.active-task-btn').removeClass('hide');
            }
            else
            {
                showErrorModal(re.errors);
            }
            btn.button('reset');
        });
        return false;
	});
	//激活按钮
	var active_task_btn = $('a.active-task-btn');
	active_task_btn.click(function(){
        var btn = $(this);
        var href= btn.attr('href');
        btn.button('loading');
        $.getJSON(href,function(re){
            if(re.success == true)
            {
                var tr = btn.parentsUntil('tbody').filter('tr');
                tr.removeClass().addClass('active');
                btn.addClass('hide');
                btn.siblings('a.pause-task-btn').removeClass('hide');
            }
            else
            {
                showErrorModal(re.errors);
            }
            btn.button('reset');
        });
        return false;
	});
	
	function showErrorModal(message)
	{
        error_modal.find('.modal-body p').text(message);
        error_modal.modal('show');
	}

    $(function(){$('[rel="tooltip"]').tooltip();});
});
