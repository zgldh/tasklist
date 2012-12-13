/**
 * tl-process-mylist.js
 */
$(function(){
	var error_modal = $('#error-modal');
	error_modal.modal({show:false});

	//跳过按钮
	var skip_process_btn = $('a.skip-process-btn');
	skip_process_btn.click(function(){
	    var btn = $(this);
        var href= btn.attr('href');
        btn.button('loading');
        $.getJSON(href,function(re){
            if(re.success == true)
            {
                var tr = btn.parentsUntil('tbody').filter('tr');
                tr.removeClass().addClass('skip');
                btn.addClass('hide');
                btn.siblings('a.restore-process-btn').removeClass('hide');
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
	var restore_process_btn= $('a.restore-process-btn');
	restore_process_btn.click(function(){
        var btn = $(this);
        var href= btn.attr('href');
        btn.button('loading');
        $.getJSON(href,function(re){
            if(re.success == true)
            {
                var tr = btn.parentsUntil('tbody').filter('tr');
                tr.removeClass();
                btn.addClass('hide');
                btn.siblings('a.skip-process-btn').removeClass('hide');
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
