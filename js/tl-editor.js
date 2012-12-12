/**
 * tl-editor.js
 */
$(function(){
	var editor_form = $('#editor-form');
	var task_conditions = $('#task_conditions');
	var condition_list = task_conditions.find('.condition-list');
	var condition_date_static_btn = $('#condition_date_static_btn');
	var condition_date_static = $('li.condition-date-static');
	var date_static_year = condition_date_static.find('.date-static-year');
	var date_static_month = condition_date_static.find('.date-static-month');
	var date_static_day = condition_date_static.find('.date-static-day');
	var date_static_hour = condition_date_static.find('.date-static-hour');

	var task_commands = $('#task_commands');
	var command_list = task_commands.find('.command-list');
	var command_url_request_btn = $('#command-url-request-btn');
	var command_url_request = $('li.command-url-request');
	var command_send_email_btn = $('#command-send-email-btn');
	var command_send_email = $('li.command-send-email');
	
	var condition_delete_btn = $('button.condition-delete-btn');
	var command_delete_btn = $('button.command-delete-btn');
	var no_conditions = $('li.no-conditions');
	var no_commands = $('li.no-commands');
	
	var send_email_content_modal = $('#send-email-content-modal');
	var send_email_content_modal_submit_btn = send_email_content_modal.find('button.btn-primary');
	
	var editor_submit_fake = $('#editor-submit-fake');
	var editor_submit = $('#editor-submit');
	
	function init_condition(){
		//特定日期条件
		condition_date_static_btn.click(function(){
			if(condition_date_static.hasClass('hide'))
			{
				condition_date_static.removeClass('hide');
				condition_date_static.find('select').removeAttr('disabled');
				checkConditionAndCommand();
			}
		});
		var month_day = [0,31,28,31,30,31,30,31,31,30,31,30,31];
		//日期条件-闰年
		date_static_month.change(function(){
		    var month =date_static_month.val() ; 
		    $day = month_day[month];
		    if(month == 2 && is_leap_year(date_static_year.val()) )
	        {
		        $day = 29;
	        }
            setup_month_day($day);
		});
		date_static_year.change(function(){
		    var year = date_static_year.val();
            var month =date_static_month.val();
            if(month == 2)
            {
                if(is_leap_year(year))
                {
                    setup_month_day(29);
                }
                else
                {
                    setup_month_day(28);
                }
            }
		});
		
		//删除条件
		condition_delete_btn.live('click',function(){
			var btn = $(this);
			var condition = btn.parent();
			condition.find('select,input,textarea').attr('disabled','disabled');
			condition.addClass('hide');
			checkConditionAndCommand();
		});
	}
	
	function setup_month_day(day)
	{
	    var options = date_static_day.find('option');
	    options.each(function(i){
	        var option = $(this);
	        if(option.val() > day)
            {
	            option.hide();
            }
	        else
            {
	            option.show();
            }
	    });
	    if(date_static_day.val()>day)
        {
	        date_static_day.val(day);
        }
	}
	
	function is_leap_year(year)
	{
	    if(year/400 == parseInt(year/400))
        {
	        return true;
        }
	    if(year/4 == parseInt(year/4))	        
        {
	        return true;
        }
	    return false;
	}
	
	function init_command()
	{
		//访问URL
		command_url_request_btn.click(function(){
			if(command_url_request.hasClass('hide'))
			{
				command_url_request.removeClass('hide');
				command_url_request.find('input').removeAttr('disabled');
				checkConditionAndCommand();
			}
		});
		//发送电子邮件
		command_send_email_btn.click(function(){
			if(command_send_email.hasClass('hide'))
			{
				command_send_email.removeClass('hide');
				command_send_email.find('input,textarea').removeAttr('disabled');
				checkConditionAndCommand();
			}
		});
		send_email_content_modal.on('shown',function(){
			 var content = command_send_email.find('input.send-email-content-input').val();
			 var send_email_content_modal_content = send_email_content_modal.data('editor');
			 send_email_content_modal_content.html(content);
		});
		
		send_email_content_modal_submit_btn.click(function(){
		    var send_email_content_modal_content = send_email_content_modal.data('editor');
		    var content = send_email_content_modal_content.html();
		    command_send_email.find('input.send-email-content-input').val(content);
		    send_email_content_modal.modal('hide');
		});
		
		//删除命令
		command_delete_btn.live('click',function(){
			var btn = $(this);
			var command = btn.parent();
			command.find('select,input,textarea').attr('disabled','disabled');
			command.addClass('hide');
			checkConditionAndCommand();
		});
	}
	/**
	 * @return boolean true: Empty; false: not empty
	 */
	function checkConditionsEmpty()
	{
		var visible = condition_list.find('li.condition-item:visible').not('.no-conditions');
		if(visible.length == 0)
		{
			showNoConditions();
			return true;
		}
		else
		{
			hideNoConditions();
			return false;
		}
	}
	/**
	 * @return boolean true: Empty; false: not empty
	 */
	function checkCommandEmpty()
	{
		var visible = command_list.find('li.command-item:visible').not('.no-commands');
		if(visible.length == 0)
		{
			showNoCommands();
			return true;
		}
		else
		{
			hideNoCommands();
			return false;
		}
	}
	
	/**
	 * @return boolean true: Empty; false: not empty
	 */
	function checkConditionAndCommand()
	{
	    var checkCommandEmpty_val = checkCommandEmpty();
	    var checkConditionsEmpty_val = checkConditionsEmpty();
	    if(checkCommandEmpty_val || checkConditionsEmpty_val)
	    {
	        editor_submit_fake.removeClass('hide');
	        editor_submit.addClass('hide');
	        return true;
	    }
	    else
	    {
	        editor_submit_fake.addClass('hide');
	        editor_submit.removeClass('hide');
	        return false;
	    }
	}
	
	function hideNoConditions()
	{
		no_conditions.addClass("hide");
	}
	function showNoConditions()
	{
		no_conditions.removeClass("hide");
	}
	function hideNoCommands()
	{
		no_commands.addClass("hide");
	}
	function showNoCommands()
	{
		no_commands.removeClass("hide");
	}
	
	init_condition();
	init_command();
	
	//保存按钮
	editor_submit.click(function(){
        editor_submit.button('loading');
    	var data = editor_form.serialize();
    	$.postJSON('/task/create',data,function(re){
    	    if(re.success == true)
	        {
	            window.location.href = '/task/list';
	        }
    	    else
	        {
    	        errors = re.errors;
    	        if(errors.system)
	            {
    	            $('.TaskError_system').text(errors.system).show();
	            }
	        }
    	});
		return false;
		
	});
	

    $(function(){$('[rel="tooltip"]').tooltip();});
});
