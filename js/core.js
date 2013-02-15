(function($) {       
$.extend({
    postJSON: function(url,data,success_cb,error_cb)
    {
        return $.ajax({
            cache: false,
            data: data,
            dataType: 'json',
            success: function(data,textStatus)
            {
                if(!data)
                {
                    if(error_cb)
                    {
                        error_cb('服务器返回空数据');
                    }
                    return false;
                }
                if(success_cb)
                {
                    success_cb(data);
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown)
            {
                if(error_cb)
                {
                    error_cb(errorThrown);
                }
            },
            type: 'POST',
                url: url
            });
        }
    }); 
$.extend({
    getJSON: function(url,in_data,in_success_cb,in_error_cb)
    {
        var data = null;
        var success_cb = null;
        var error_cb = null;
        if(typeof(in_data) == 'function')
        {
            data = null;
            success_cb = in_data;
            error_cb = in_success_cb;
        }
        else
        {
            data = in_data;
            success_cb = in_success_cb;
            error_cb = in_error_cb;
        }
        
        return $.ajax({
            cache: false,
            data: data,
            dataType: 'json',
            success: function(data,textStatus)
            {
                if(!data)
                {
                    if(error_cb)
                    {
                        error_cb('服务器返回空数据');
                    }
                    return false;
                }
                if(success_cb)
                {
                    success_cb(data);
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown)
            {
                if(error_cb)
                {
                    error_cb(errorThrown);
                }
            },
            type: 'GET',
            url: url
        });
    }
}); 


})(jQuery);    