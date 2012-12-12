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
				error_modal.find('.modal-body p').text(re.errors);
				error_modal.modal('show');
				btn.button('reset')
	        }
    	});
		return false;
	});

    $(function(){$('[rel="tooltip"]').tooltip();});
});
