/**
 * tl-editor.js
 */
$(function(){
    var error_modal = $('#error-modal');
    error_modal.modal({show:false});
    function showErrorModal(message)
    {
        error_modal.find('.modal-body p').html(message);
        error_modal.modal('show');
    }

    function setMask(obj)
    {
    	if(obj.is('.fade'))
    	{
    		return false;
    	}
    	var mask = $('<div class="mask"></div>');
    	mask.height(obj.height());
    	mask.width(obj.width());
    	obj.prepend(mask);
    	obj.data('mask',mask);
    	obj.addClass('fade');
    }
    function removeMask(obj)
    {
    	if(!obj.is('.fade'))
		{
    		return false;
		}
    	var maskObj = obj.data('mask');
    	maskObj.remove();
    	obj.removeClass('fade');
    }

    function setHeight(obj)
    {
    	var window_height = $(window).height();
    	if(obj.height() < window_height)
		{
    		obj.height(window_height);
		}
    }

    var back_btns = $('a.back');
    back_btns.click(function(){
    	var btn = $(this);
    	var target = $('#'+btn.attr('data-target'));
    	removeMask(target);
		$.scrollTo(target,200,function(){
			var parent = btn.parent();
			if(btn.attr('remain') != 'remain')
			{
				parent.find('.span12').empty();
			}
			parent.addClass('hide');
		});
    });

    var app_tiles = {
    		trigger_apps_box: $('#trigger-apps-box'),
    		command_apps_box: $('#command-apps-box'),
    		template_tag: $('#app-tile-template'),
    		icon_tag: $('#app-tile-template .image-wrapper i'),
    		label_tag: $('#app-tile-template .app-label'),
    		count_tag: $('#app-tile-template .app-count'),
    		create: function(app_id,title,count,active)
    		{
    			this.icon_tag.attr('class','icon- app_icon_'+app_id);
    			this.label_tag.text(title);
    			this.count_tag.text(count);
    			var tile = this.template_tag.clone();
    			tile.removeAttr('id').removeClass('hide');
    			tile.addClass('app_'+app_id);
                tile.attr('data-app-id',app_id);
    			if(!(active && active.actived == 1))
    			{
    				tile.addClass('unactived');
    			}
    			return tile;
    		},
            cleanTriggerBox: function()
            {
                this.trigger_apps_box.empty();
            },
            cleanCommandBox: function()
            {
                this.command_apps_box.empty();
            },
    		getTriggersDataURL: function()
    		{
    			return this.trigger_apps_box.attr('data-url');
    		},
		    getCommandsDataURL: function()
		    {
		        return this.command_apps_box.attr('data-url');
            },
            appendTriggerAppTile: function(tile)
            {
                this.trigger_apps_box.append(tile);
            },
            appendCommandAppTile: function(tile)
            {
                this.command_apps_box.append(tile);
            }
    };
    var trigger_tiles = {
    		trigger_box: $('#triggers-box'),
    		template_tag: $('#trigger-tile-template'),
    		label_tag: $('#trigger-tile-template h3'),
    		description_tag: $('#trigger-tile-template p'),
            current_app_id: null,
    		create: function(trigger_id,title,description)
    		{
    			this.label_tag.text(title);
    			this.description_tag.text(description);
    			var tile = this.template_tag.clone();
    			tile.removeAttr('id').removeClass('hide');
    			tile.addClass('trigger_'+trigger_id);
    			tile.data('trigger_id',trigger_id);

    			return tile;
    		},
    		cleanBox: function()
    		{
    			this.trigger_box.empty();
    		},
    		getDataURL: function()
    		{
    			return this.trigger_box.attr('data-url');
    		},
            appendTile: function(tile)
            {
                this.trigger_box.append(tile);
            }
    };
    var trigger_detail = {
    		trigger_detail_box: $('#trigger-detail-box'),
            current_trigger_id: null,
            setup: function(app_trigger)
            {
                this.current_trigger_id = app_trigger.trigger_id;
            	this.trigger_detail_box.html(app_trigger.detial_html);
            },
            cleanBox: function()
            {
            	this.trigger_detail_box.empty();
            }
    };
    var command_tiles = {
            command_box: $('#commands-box'),
            template_tag: $('#command-tile-template'),
            label_tag: $('#command-tile-template h3'),
            description_tag: $('#command-tile-template p'),
            current_app_id: null,
            create: function(command_id,title,description)
            {
                this.label_tag.text(title);
                this.description_tag.text(description);
                var tile = this.template_tag.clone();
                tile.removeAttr('id').removeClass('hide');
                tile.addClass('command_'+command_id);
                tile.data('command_id',command_id);

                return tile;
            },
            cleanBox: function()
            {
                this.command_box.empty();
            },
            getDataURL: function()
            {
                return this.command_box.attr('data-url');
            },
            appendTile: function(tile)
            {
                this.command_box.append(tile);
            }
    };
    var command_detail = {
            command_detail_box: $('#command-detail-box'),
            current_command_id: null,
            setup: function(app_command)
            {
                this.current_command_id = app_command.command_id;
                this.command_detail_box.html(app_command.detial_html);
            },
            cleanBox: function()
            {
                this.command_detail_box.empty();
            }
    };

    var first_trigger_btn = $('#trigger-btn');
    first_trigger_btn.click(function(){
    	if(first_trigger_btn.disabled)
    	{
    		return false;
    	}
    	first_trigger_btn.disabled = true;
    	var url = first_trigger_btn.attr('data-url');
    	$.getJSON(url,function(re){
    		if(re && re.success)
    		{
    			var apps = re.data;
    			app_tiles.cleanTriggerBox();
    			setMask($('#step_1'));
    			for(var i = 0;i<apps.length;i++)
    			{
    				var app = apps[i];
    				var tile = app_tiles.create(app.app_id,app.name,app.triggers_count,app.active);
    				tile.data('app',app);
    				app_tiles.appendTriggerAppTile(tile);
    			}
    			var step_2 = $('#step_2');
    			step_2.removeClass('hide');
    			setHeight(step_2);
    			$.scrollTo(step_2,200);
    		}
    		else
    		{
    			showErrorModal(re.msg);
    		}
    		first_trigger_btn.disabled = false;
    	},
    	function(text){
    	    showErrorModal(text);
    	});
    });

    var trigger_app_btn = $('#trigger-apps-box .tile.app');
    trigger_app_btn.live('click',function(){
    	var btn = $(this);
    	if(btn.disabled)
    	{
    		return false;
    	}
    	btn.disabled = true;
    	var app_id = btn.attr('data-app-id');
    	var app = btn.data('app');
    	var app_name = app.name;
    	var url = app_tiles.getTriggersDataURL()+app_id;
    	$.getJSON(url,function(re){
    		if(re && re.success)
    		{
    			var triggers = re.data;
    			trigger_tiles.cleanBox();
    			trigger_tiles.current_app_id = app_id;
    			setMask($('#step_2'));
    			for(var i = 0;i<triggers.length;i++)
    			{
    				var trigger = triggers[i];
    				var tile = trigger_tiles.create(trigger.trigger_id,trigger.name,trigger.description);
    				trigger_tiles.appendTile(tile);
    			}
    			var step_3 = $('#step_3');
    			step_3.removeClass('hide').find('strong').text(app_name);
    			setHeight(step_3);
    			$.scrollTo(step_3,200);
    		}
    		else
    		{
    			showErrorModal(re.msg);
    		}
    		btn.disabled = false;
    	},
        function(text){
            showErrorModal(text);
        });
    });

    var trigger_btn = $('#triggers-box .tile.triggers');
    trigger_btn.live('click',function(){
    	var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        var trigger_id = btn.data('trigger_id');
        var url = trigger_tiles.getDataURL()+trigger_id;
        $.getJSON(url,function(re){
            if(re && re.success)
            {
                var trigger = re.data;
                trigger_detail.cleanBox();
                setMask($('#step_3'));
                trigger_detail.setup(trigger);
                var step_4 = $('#step_4');
                step_4.removeClass('hide');
                setHeight(step_4);
    			$.scrollTo(step_4,200);
            }
            else
            {
                showErrorModal(re.msg);
            }
            btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });
    });

    trigger_detail.trigger_detail_box.find('button[type="submit"]').live('click',function(){
        var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        
        if(btn.validation)
    	{
        	var validation_errors = btn.validation();
        	if(validation_errors)
    		{
                btn.disabled = false;
                showErrorModal(validation_errors);
        		return false;
    		}
    	}
        
    	var step_4 = $('#step_4');
    	var url = step_4.attr('data-submit-url');
    	var data = step_4.find('form').serialize();
    	$.postJSON(url,data,function(re){
    		if(re && re.success)
		    {
                setMask($('#step_4'));
    		    var data = re.data;
    		    var step_5 = $('#step_5');
    		    var trigger_btn_2 = $('#trigger-btn-2');
    		    if(typeof(trigger_btn_2.org_class) == 'undefined')
		        {
    		        trigger_btn_2.org_class = trigger_btn_2.attr('class');
		        }
    		    trigger_btn_2.removeClass().addClass(trigger_btn_2.org_class).addClass('app_'+trigger_tiles.current_app_id);
    		    trigger_btn_2.find('.column-text .text5').html(data.description);
    		    step_5.removeClass('hide');
    		    setHeight(step_5);
    		    $.scrollTo(step_5,200);
		    }
    		else
		    {
                showErrorModal(re.msg);
		    }
            btn.disabled = false;
    	},
        function(text){
            showErrorModal(text);
        });
    	return false;
    });

    var first_command_btn = $('#command-btn');
    first_command_btn.click(function(){
        if(first_command_btn.disabled)
        {
            return false;
        }
        first_command_btn.disabled = true;
        var url = first_command_btn.attr('data-url');
        $.getJSON(url,function(re){
            if(re && re.success)
            {
                var apps = re.data;
                app_tiles.cleanCommandBox();
                setMask($('#step_5'));
                for(var i = 0;i<apps.length;i++)
                {
                    var app = apps[i];
                    var tile = app_tiles.create(app.app_id,app.name,app.commands_count,app.active);
                    tile.data('app',app);
                    app_tiles.appendCommandAppTile(tile);
                }
                var step_6 = $('#step_6');
                step_6.removeClass('hide');
                setHeight(step_6);
                $.scrollTo(step_6,200);
            }
            else
            {
                showErrorModal(re.msg);
            }
            first_command_btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });
    });

    var command_app_btn = $('#command-apps-box .tile.app');
    command_app_btn.live('click',function(){
        var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        var app_id = btn.attr('data-app-id');
        var app = btn.data('app');
        var app_name = app.name;
        var url = app_tiles.getCommandsDataURL()+app_id;
        $.getJSON(url,function(re){
            if(re && re.success)
            {
                var commands = re.data;
                command_tiles.cleanBox();
                command_tiles.current_app_id = app_id;
                setMask($('#step_6'));
                for(var i = 0;i<commands.length;i++)
                {
                    var command = commands[i];
                    var tile = command_tiles.create(command.command_id,command.name,command.description);
                    command_tiles.appendTile(tile);
                }
                var step_7 = $('#step_7');
                step_7.removeClass('hide').find('strong').text(app_name);
                setHeight(step_7);
                $.scrollTo(step_7,200);
            }
            else
            {
                showErrorModal(re.msg);
            }
            btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });
    });

    var command_btn = $('#commands-box .tile.commands');
    command_btn.live('click',function(){
    	var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        var command_id = btn.data('command_id');
        var url = command_tiles.getDataURL()+command_id;
        $.getJSON(url,function(re){
            if(re && re.success)
            {
                var command = re.data;
                command_detail.cleanBox();
                setMask($('#step_7'));
                command_detail.setup(command);
                var step_8 = $('#step_8');
                step_8.removeClass('hide');
                setHeight(step_8);
    			$.scrollTo(step_8,200);
            }
            else
            {
                showErrorModal(re.msg);
            }
            btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });
    });

    command_detail.command_detail_box.find('button[type="submit"]').live('click',function(){
        var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        var step_8 = $('#step_8');
        var url = step_8.attr('data-submit-url');
        var data = step_8.find('form').serialize();
        $.postJSON(url,data,function(re){
            if(re && re.success)
            {
                setMask($('#step_8'));
                var data = re.data;
                var trigger_btn_2 = $('#trigger-btn-2');
                var trigger_btn_3 = $('#trigger-btn-3');
                trigger_btn_3.html(trigger_btn_2.html()).attr('class',trigger_btn_2.attr('class'));

                var command_btn_3 = $('#command-btn-3');
                if(typeof(command_btn_3.org_class) == 'undefined')
                {
                    command_btn_3.org_class = command_btn_3.attr('class');
                }
                command_btn_3.removeClass().addClass(command_btn_3.org_class).addClass('app_'+command_tiles.current_app_id);
                command_btn_3.find('.column-text .text5').html(data.description);
                var step_9 = $('#step_9');
                step_9.removeClass('hide');
                setHeight(step_9);
                $.scrollTo(step_9,200,function(){
					$('input[name="task[name]"]').focus();
				});
            }
            else
            {
                showErrorModal(re.msg);
            }
            btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });
        return false;
    });

	var final_submit = $('#final-submit');
	final_submit.click(function(){
        var btn = $(this);
        if(btn.disabled)
        {
            return false;
        }
        btn.disabled = true;
        var url = btn.attr('data-url');

		var trigger_form = $('#step_4 form');
		var command_form = $('#step_8 form');
		var task_form = $('#task-form');

		var parameters = [trigger_form.serialize(),command_form.serialize(),task_form.serialize()];
		var parameters_string = parameters.join('&');

		$.postJSON(url,parameters_string,function(re){
            if(re && re.success)
            {
                showErrorModal(re.msg);
            }
            else
            {
                showErrorModal(re.msg);
            }
            btn.disabled = false;
        },
        function(text){
            showErrorModal(text);
        });

		return false;
	});

    $(function(){$('[rel="tooltip"]').tooltip();});
});
